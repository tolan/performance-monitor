
function ProfilerMySQLScenariosList($scope, $http, $modal) {
    $scope.initList('id');
    $scope.scenarios = [];
    $scope.deleteScenarioId;

    $http.get('/profiler/mysql/scenarios').success(function(response) {
        $scope.scenarios  = response;
    });

    $scope.deleteScenario = function(id) {
        $http.delete('/profiler/mysql/scenario/' + id).success(function(response) {
            if (response === "true") {
                var key = _.indexOf($scope.scenarios, _.findWhere($scope.scenarios, {id : id}));
                $scope.scenarios.splice(key, 1);
            }
        });
    };

    $scope.deleteScenarioDialog = function(id) {
        $scope.deleteScenarioId = id;
        $modal({
            template : 'profiler-scenario-modal-delete.html',
            persist  : true,
            show     : true,
            scope    : $scope
        });
    };
}

function ProfilerMySQLScenarioCreate($scope, $http, $routeParams) {
    $scope.name;
    $scope.description;
    $scope.requests      = [];
    $scope.methods       = [];
    $scope.methodsParams = [];
    $scope.filterParams  = [];
    $scope.filterTypes   = [];
    $scope.alerts        = [];

    // It means that it is edit UC.
    if ($routeParams.hasOwnProperty('id')) {
        $http.get('/profiler/mysql/scenario/' + $routeParams.id).success(function(scenario) {
            $scope.name        = scenario.name;
            $scope.description = scenario.description;
            $scope.requests    = scenario.requests;

            $scope.refreshFilters();
        });
    }

    $http.get('/profiler/request/methods').success(function(methods) {
        $scope.methods       = methods.requests;
        $scope.methodsParams = methods.params;
    });

    $http.get('/profiler/filter/options').success(function(options) {
        $scope.filterParams = options.params;
        $scope.filterTypes  = options.types;
        $scope.refreshFilters();
    });

    $scope.refreshFilters = function() {
        if ($scope.filterParams.length && $scope.requests.length) {
            angular.forEach($scope.requests, function(request) {
                if (request.hasOwnProperty('filters')) {
                    angular.forEach(request.filters, function(filter) {
                        filter.options = _.clone($scope.filterParams);
                        angular.forEach(filter.parameters, function(parameter) {
                            var item = _.findWhere($scope.filterParams, {value: parameter.parameter});
                            filter.options = _.without(filter.options, item);
                            parameter.options = item;
                        });
                    });
                }
            });
        }
    };

    $scope.addRequest = function() {
        $scope.requests.push({
            method     : _.first($scope.methods).value,
            toMeasure  : false,
            parameters : [],
            filters    : []
        });
    };

    $scope.addParameter = function(request) {
        request.parameters = request.parameters || [];
        request.parameters.push({
            method : _.first($scope.methodsParams[request.method]).value
        });
    };

    $scope.addFilter = function(request) {
        request.filters = request.filters || [];
        request.filters.push({
            type       : 'negative',
            parameters : [],
            options    : _.clone($scope.filterParams)
        });
    };

    $scope.addFilterParameter = function(event, item, filter) {
        var value    = null,
            operator = _.first(item.operators).value;
        filter.options = _.without(filter.options, item);

        if (operator === 'boolean') {
            value = false;
        }

        filter.parameters.push({
            options   : item,
            operator  : operator,
            value     : value,
            parameter : item.value
        });
    };
    $scope.$on('menu-selected-item', $scope.addFilterParameter);

    $scope.removeFilterParameter = function(filter, parameter) {
        filter.options.push(parameter.options);
        var index = _.indexOf(filter.parameters, parameter);
        filter.parameters.splice(index, 1);
    };

    $scope.removeRequest = function(request) {
        var index = _.indexOf($scope.requests, request);
        $scope.requests.splice(index, 1);
    };

    $scope.removeParameter = function(request, parameter) {
        var index = _.indexOf(request.parameters, parameter);
        request.parameters.splice(index, 1);
    };

    $scope.removeFilter = function(request, filter) {
        var index = _.indexOf(request.filters, filter);
        request.filters.splice(index, 1);
    };

    $scope.send = function() {
        var scenario = {
            name        : $scope.name,
            description : $scope.description || '',
            requests    : angular.copy($scope.requests)
        };

        if($scope.isValid(scenario)) {
            $scope.cleanBeforeSend(scenario);

            if ($routeParams.hasOwnProperty('id')) {
                scenario.id = $routeParams.id;
                $http.put('/profiler/mysql/scenario/' + $routeParams.id, scenario).success(function(response) {
                    $scope.back('/profiler/mysql/scenarios');
                });
            } else {
                $http.post('/profiler/mysql/scenario', scenario).success(function(response) {
                    $scope.back('/profiler/mysql/scenarios');
                });
            }
        }
    };

    $scope.cleanBeforeSend = function(scenario) {
        angular.forEach(scenario.requests, function(request) {
            angular.forEach(request.filters, function(filter) {
                if (filter.hasOwnProperty('options')) {
                    delete(filter.options);
                }

                angular.forEach(filter.parameters, function(parameter) {
                    if (parameter.hasOwnProperty('options')) {
                        delete(parameter.options);
                    }
                });
            });
        });

        return scenario;
    };

    $scope.isValid = function(scenario) {
        $scope.alerts = [];

        if (_.isEmpty(scenario.name)) {
            $scope.addAlert('profiler.scenario.name', 'main.validate.required');
        }

        if (_.isEmpty(scenario.requests)) {
            $scope.addAlert('profiler.scenario.request.list', 'main.validate.required');
        } else {
            angular.forEach(scenario.requests, function(request){
                $scope.validateRequest(request);
            });
        }

        return $scope.alerts.length === 0;
    };

    $scope.validateRequest = function(request) {
        if (_.isEmpty(request.url)) {
            $scope.addAlert('profiler.scenario.request.url', 'main.validate.required');
        }

        if (request.hasOwnProperty('parameters') && request.parameters.length > 0) {
            angular.forEach(request.parameters, function(parameter){
                $scope.validateParameter(request, parameter);
            });
        }

        if (request.hasOwnProperty('filters') && request.filters.length > 0) {
            angular.forEach(request.filters, function(filter){
                $scope.validateFilter(filter);
            });
        }
    };

    $scope.validateParameter = function(request, parameter) {
        if (_.isEmpty(parameter.name)) {
            $scope.addAlert('profiler.scenario.request.parameter.name', 'main.validate.required');
        }

        if (_.isEmpty(parameter.value)) {
            $scope.addAlert('profiler.scenario.request.parameter.value', 'main.validate.required');
        }

        var allowedMethods = _.pluck($scope.methodsParams[request.method], 'value');

        if (_.indexOf(allowedMethods, parameter.method) === -1) {
            $scope.addAlert('profiler.scenario.request.method', 'main.validate.required');
        }
    };

    $scope.validateFilter = function(filter) {
        if (!filter.hasOwnProperty('parameters') || !filter.parameters.length) {
            $scope.addAlert('profiler.scenario.request.filter.parameter', 'main.validate.required');
        } else {
            angular.forEach(filter.parameters, function(parameter) {
                $scope.validateFilterParameter(parameter);
            });
        }
    };

    $scope.validateFilterParameter = function(parameter) {
        if (!parameter.hasOwnProperty('value') || parameter.value === '' || parameter.value === null) {
            $scope.addAlert(parameter.options.text, 'main.validate.required');
        } else {
            var value = parameter.value;

            switch (parameter.operator) {
                case 'lowerThan':
                case 'higherThan':
                    if (_.isNaN(parseInt(value)) || parseInt(value).toString().length !== value.length) {
                        $scope.addAlert(parameter.options.text, 'main.validate.isNotNumber');
                    }

                    break;
            }
        }
    };

    $scope.addAlert = function(caption, message) {
        $scope.alerts.push({
            caption : caption,
            message : message
        });
    };
}

function ProfilerMySQLScenarioDetail($scope, $http, $routeParams, $timeout) {
    $scope.predicate  = 'started';
    $scope.reverse    = false;
    $scope.scenarioId = $routeParams.id;
    $scope.scenario;
    $scope.tests     = [];
    $scope.testTimer = {};
    $scope.refreshInterval = 1000;

    $scope.totalItems  = 0;
    $scope.currentPage = 1;
    $scope.maxSize     = 5;
    $scope.pageSize    = 10;
    $scope.pageSizes   = [10, 20, 50, 100];

    $http.get('/profiler/mysql/scenario/' + $scope.scenarioId).success(function(response) {
        $scope.scenario = response;
    });

    $scope.loadTests = function(gotoLastPage) {
        $scope.tests = [];
        $http.get('/profiler/mysql/scenario/' + $scope.scenarioId + '/tests').success(function(tests) {
            angular.forEach(tests, function(test) {
                $scope.unsetWatcher(test.id);
                if (test.state !== 'done' && test.state !== 'error') {
                    $scope.setWatcher(test.id);
                }

                $scope.tests.push(test);
            });

            if(gotoLastPage) {
                $scope.currentPage = parseInt($scope.tests.length / $scope.pageSize) + 1;
            }
        });
    };

    $scope.startTest = function() {
        $http.post('/profiler/mysql/scenario/' + $scope.scenarioId + '/test/start').success(function(response) {
            $scope.loadTests(true);
        });
    };

    $scope.deleteTest = function(id) {
        $http.delete('/profiler/mysql/scenario/test/' + id).success(function(response) {
            $scope.unsetWatcher(id);
            if (response === "true") {
                var key = _.indexOf($scope.tests, _.findWhere($scope.tests, {id : id}));;
                $scope.tests.splice(key, 1);
            }
        });
    };

    $scope.unsetWatcher = function(id) {
        if ($scope.testTimer.hasOwnProperty(id)) {
            $timeout.cancel($scope.testTimer[id]);
            delete($scope.testTimer[id]);
        }
    };

    $scope.setWatcher = function(id) {
        $scope.unmask().blockLoad(true);
        $scope.testTimer[id] = $timeout(function() {
            $http.get('/profiler/mysql/scenario/test/' + id).success(function(response) {
                var key = _.indexOf($scope.tests, _.findWhere($scope.tests, {id : id}));
                if (key !== -1) {
                    $scope.tests[key] = response;
                    $scope.unsetWatcher(id);

                    if (response.state !== 'done' && response.state !== 'error') {
                        $scope.setWatcher(id);
                    }
                }
            });
        }, $scope.refreshInterval);
    };

    $scope.refresh = function(input) {
        $scope.totalItems = input.length;

        return input;
    };

    $scope.getRequestLink = function(request) {
        var link = request.url, parameters = [];

        if (link.search('http') !== 0) {
            link = 'http://' + link;
        }

        parameters.push('PROFILER_ENABLED=true');
        parameters.push('TYPE=file');
        parameters.push('REQUEST_ID=' + request.id);

        if (request.hasOwnProperty('parameters')) {
            angular.forEach(request.parameters, function(parameter) {
                if (parameter.method === 'GET') {
                    parameters.push(parameter.name + '=' + parameter.value);
                }
            });

        }

        link = link + '?' + parameters.join('&');

        return link;
    };

    $scope.$on('$locationChangeSuccess', function() {
        $scope.blockLoad(false);

        for(var id in $scope.testTimer) {
            if ($scope.testTimer.hasOwnProperty(id)) {
                $scope.unsetWatcher(id);
            }
        }
    });

    $scope.loadTests();
}

function ProfilerMySQLTestListCtrl($scope, $http, $routeParams) {
    $scope.testId = $routeParams.id;
    $scope.predicate = 'started';
    $scope.reverse   = false;
    $scope.measures  = [];

    $http.get('/profiler/mysql/scenario/test/' + $scope.testId + '/measures').success(function(response) {
        $scope.measures = response;
    });
}

function ProfilerFileListCtrl($scope, $http, $modal) {
    $scope.predicate = 'id';
    $scope.reverse   = false;
    $scope.measures  = [];

    $scope.totalItems  = 0;
    $scope.currentPage = 1;
    $scope.maxSize     = 5;
    $scope.pageSize    = 10;
    $scope.pageSizes   = [10, 20, 50, 100];

    $scope.setPage = function (pageNo) {
        $scope.currentPage = pageNo;
    };

    $http.get('/profiler/file/measures').success(function(response) {
        $scope.measures      = response;
        $scope.totalItems = response.length;
    });

    $scope.deleteMeasure = function (id){
        $http.delete('/profiler/file/measure/' + id).success(function(response) {
            if (response === "true") {
                var key = _.indexOf($scope.measures, _.findWhere($scope.measures, {id : id}));
                $scope.measures.splice(key, 1);
            }
        });
    };

    $scope.deleteMeasureDialog = function(id) {
        $scope.deleteMeasureId = id;
        $modal({
            template : 'measure-delete-dialog.html',
            persist  : true,
            show     : true,
            backdrop : 'static',
            scope    : $scope
        });
    };


    $scope.refresh = function(input) {
        $scope.totalItems = input.length;

        return input;
    };
}

function ProfilerMeasureDetailCtrl($scope, $http, $routeParams) {
    $scope.type = $routeParams.type || 'Session';
    $scope.id   = $routeParams.id;

    $scope.template = '/js/template/Profiler/Measure/detail.html';
    $scope.templatePrefix = '/js/template/Profiler/Measure/';
    $scope.tabs = [{
            title    : 'profiler.scenario.test.measure.detail.summary',
            template : $scope.templatePrefix + 'summary.html',
            type     : 'summary',
            active   : true
        }, {
            title    : 'profiler.scenario.test.measure.detail.callStack',
            template : $scope.templatePrefix + 'callStack.html',
            type     : 'callStack'
        }, {
            title    : 'profiler.scenario.test.measure.detail.functionStatistics',
            template : $scope.templatePrefix + 'functionStat.html',
            type     : 'functionStat'
    }];

    $scope.summary;

    $http.get('/profiler/' + $scope.type + '/measure/' + $scope.id + '/summary').success(function(response) {
        $scope.summary = response;
    });

    $scope.selectTab = function(type) {
        $scope.$broadcast('select', type);
    };
}

function ProfilerCallStackCtrl($scope, $http, $routeParams) {
    $scope.type      = $routeParams.type || 'Session';
    $scope.measureId = $routeParams.id || 1;
    $scope.calls     = [];
    $scope.filter    = {
        type:     'timeSubStack',
        operator: 'higherThan',
        value:    null
    };

    $scope.showCall = function(call) {
        if (call.calls === undefined) {
        $http.get('/profiler/' + $scope.type + '/measure/' + $scope.measureId + '/callStack/parent/' + call.id).success(function(response) {
            call.calls = response;
        });
        }
    };

    $scope.filterCall = function(call) {
        var filter = false, value = call[$scope.filter.type], regexp;

        switch ($scope.filter.operator) {
            case 'higherThan':
                filter = value < $scope.filter.value;
                break;
            case 'lowerThan':
                filter = value > $scope.filter.value;
                break;
            case 'regExp':
                if (!_.isEmpty($scope.filter.value)) {
                    regexp = new RegExp($scope.filter.value);
                    filter = _.isEmpty(value.match(regexp));
                }
                break;
        }

        return filter;
    };

    $scope.$on('select', function(event, type) {
        if (type === $scope.tab.type && $scope.calls.length === 0) {
            $http.get('/profiler/' + $scope.type + '/measure/' + $scope.measureId + '/callStack/parent/0').success(function(response) {
                $scope.calls = response;
            });
        }
    });
}

function ProfilerFunctionStatCtrl($scope, $http, $routeParams) {
    $scope.predicate = 'id';
    $scope.reverse   = false;
    $scope.type      = $routeParams.type || 'Session';
    $scope.measureId = $routeParams.id || 1;
    $scope.calls = [];
    $scope.count = 0;

    $scope.totalItems  = 0;
    $scope.currentPage = 1;
    $scope.maxSize     = 5;
    $scope.pageSize    = 10;
    $scope.pageSizes   = [10, 20, 50, 100];

    $scope.setPage = function (pageNo) {
        $scope.currentPage = pageNo;
    };

    $scope.$on('select', function(event, type) {
        if (type === $scope.tab.type && $scope.calls.length === 0) {
            $http.get('/profiler/' + $scope.type + '/measure/' + $scope.measureId + '/statistic/function').success(function(response) {
                $scope.count = _.reduce(response, function(sum, el) {
                    return sum + el.count;
                }, 0);

                $scope.calls      = response;
                $scope.totalItems = response.length;
            });
        }
    });

    $scope.refresh = function(input) {
        $scope.totalItems = input.length;

        return input;
    };
}