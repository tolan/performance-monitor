
/**
 * Controller for list of measures.
 *
 * @param {object} $scope Scope instance
 * @param {object} $http  Http instnace
 * @param {object} $modal Modal service
 *
 * @returns {void}
 */
function ProfilerMeasureListCtrl($scope, $http, $modal) {
    $scope.predicate = 'id';
    $scope.reverse   = false;
    $scope.measures  = [];
    $scope.deleteMeasureId;

    $scope.totalItems  = 0;
    $scope.currentPage = 1;
    $scope.maxSize     = 5;
    $scope.pageSize    = 10;
    $scope.pageSizes   = [10, 20, 50, 100];

    $http.get('profiler/measures').success(function(response) {
        $scope.measures   = response;
        $scope.totalItems = response.length;
    });

    $scope.deleteMeasure = function(id) {
        $http.delete('profiler/measure/' + id).success(function(response) {
            if (response === "true") {
                var key = _.indexOf($scope.measures, _.findWhere($scope.measures, {id : id}));
                $scope.measures.splice(key, 1);
            }
        });
    };

    $scope.deleteMeasureDialog = function(id) {
        $scope.deleteMeasureId = id;
        $modal({
            template : 'modal-delete.html',
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

function ProfilerMeasureCreateCtrl($scope, $http, $routeParams, $window, $location) {
    $scope.name;
    $scope.description;
    $scope.requests      = [];
    $scope.methods       = [];
    $scope.methodsParams = [];
    $scope.alerts        = [];

    // It means that it is edit UC.
    if ($routeParams.hasOwnProperty('id')) {
        $http.get('profiler/measure/' + $routeParams.id).success(function(measure) {
            $scope.name        = measure.name;
            $scope.description = measure.description;
            $scope.requests    = measure.requests;
        });
    }

    $http.get('profiler/request/methods').success(function(methods) {
        $scope.methods       = methods.requests;
        $scope.methodsParams = methods.params;
    });

    $scope.addRequest = function() {
        $scope.requests.push({
            method:    $scope.methods[0].value,
            toMeasure: false,
            parameters: []
        });
    };

    $scope.addParameter = function(request) {
        request.parameters.push({
            method : $scope.methodsParams[request.method][0].value
        });
    };

    $scope.removeRequest = function(request) {
        var index = _.indexOf($scope.requests, request);
        $scope.requests.splice(index, 1);
    };

    $scope.removeParameter = function(request, parameter) {
        var index = _.indexOf(request.parameters, parameter);
        request.parameters.splice(index, 1);
    };

    $scope.send = function() {
        var measure = {
            name        : $scope.name,
            description : $scope.description || '',
            requests    : $scope.requests
        };

        if($scope.isValid(measure)) {
            if ($routeParams.hasOwnProperty('id')) {
                $http.put('profiler/measure/' + $routeParams.id, measure).success(function(response) {
                    $location.path('/profiler/measure/list');
                });
            } else {
                $http.post('profiler/measure', measure).success(function(response) {
                    $location.path('/profiler/measure/list');
                });
            }
        }
    };

    $scope.isValid = function(measure) {
        $scope.alerts = [];

        if (_.isEmpty(measure.name)) {
            $scope.addAlert('profiler.measure.name', 'main.validate.required');
        }

        if (_.isEmpty(measure.requests)) {
            $scope.addAlert('profiler.measure.request.list', 'main.validate.required');
        } else {
            angular.forEach(measure.requests, function(request){
                $scope.validateRequest(request);
            });
        }

        return $scope.alerts.length === 0;
    };

    $scope.validateRequest = function(request) {
        if (_.isEmpty(request.url)) {
            $scope.addAlert('profiler.request.url', 'main.validate.required');
        }

        if (request.parameters.length > 0) {
            angular.forEach(request.parameters, function(parameter){
                $scope.validateParameter(request, parameter);
            });
        }
    };

    $scope.validateParameter = function(request, parameter) {
        if (_.isEmpty(parameter.name)) {
            $scope.addAlert('profiler.request.parameter.name', 'main.validate.required');
        }

        if (_.isEmpty(parameter.value)) {
            $scope.addAlert('profiler.request.parameter.value', 'main.validate.required');
        }

        var allowedMethods = _.pluck($scope.methodsParams[request.method], 'value');

        if (_.indexOf(allowedMethods, parameter.method) === -1) {
            $scope.addAlert('profiler.request.method', 'main.validate.required');
        }
    };

    $scope.addAlert = function(caption, message) {
        $scope.alerts.push({
            caption : caption,
            message : message
        });
    };

    $scope.back = function() {
        $location.path('/profiler/measure/list');
    };
}

function ProfilerMeasureDetailCtrl($scope, $http, $routeParams, $window, $location, $timeout) {
    $scope.predicate = 'id';
    $scope.reverse   = false;
    $scope.measureId = $routeParams.id;
    $scope.measure;
    $scope.tests = [];
    $scope.testTimer = {};
    $scope.refreshInterval = 1000;

    $scope.totalItems  = 0;
    $scope.currentPage = 1;
    $scope.maxSize     = 5;
    $scope.pageSize    = 10;
    $scope.pageSizes   = [10, 20, 50, 100];

    $http.get('profiler/measure/' + $scope.measureId).success(function(response) {
        $scope.measure = response;
    });

    $scope.loadTests = function(lastPage) {
        $scope.tests = [];
        $http.get('profiler/measure/' + $scope.measureId + '/tests').success(function(tests) {
            angular.forEach(tests, function(test) {
                $scope.unsetWatcher(test.id);
                if (test.state !== 'statistic_generated' && test.state !== 'error') {
                    $scope.setWatcher(test.id);
                }

                $scope.tests.push(test);
            });

            if(lastPage) {
                $scope.currentPage = parseInt($scope.tests.length / $scope.pageSize) + 1;
            }
        });
    };

    $scope.startTest = function() {
        $http.post('profiler/measure/' + $scope.measureId + '/test/start').success(function(response) {
            $scope.loadTests(true);
        });
    };

    $scope.deleteTest = function(id) {
        $scope.unsetWatcher(id);

        $http.delete('profiler/measure/test/' + id).success(function(response) {
            if (response === "true") {
                var key = _.indexOf($scope.tests, _.findWhere($scope.tests, {id : id}));;
                $scope.tests.splice(key, 1);
            }
        });
    };

    $scope.unsetWatcher = function(id) {
        if ($scope.testTimer.hasOwnProperty(id)) {
            $timeout.cancel($scope.testTimer[id]);
        }
    };

    $scope.setWatcher = function(id) {
        $scope.testTimer[id] = $timeout(function() {
            $http.get('profiler/measure/test/' + id).success(function(response) {
                var key = _.indexOf($scope.tests, _.findWhere($scope.tests, {id : id}));;
                $scope.tests[key] = response;

                if (response.state !== 'statistic_generated' && response.state !== 'error') {
                    $scope.setWatcher(id);
                } else {
                    $timeout.cancel($scope.testTimer[id]);
                }
            });
        }, $scope.refreshInterval);
    };

    $scope.back = function() {
        $location.path('/profiler/measure/list');
    };

    $scope.refresh = function(input) {
        $scope.totalItems = input.length;

        return input;
    };

    $scope.loadTests();
}

function ProfilerTestListCtrl($scope, $http, $routeParams) {
    $scope.testId = $routeParams.id;
    $scope.predicate = 'id';
    $scope.reverse   = false;
    $scope.attempts  = [];

    $http.get('profiler/measure/test/' + $scope.testId + '/attempts').success(function(response) {
        $scope.attempts = response;
    });
}

function ProfilerAttemptDetailCtrl($scope, $http, $routeParams) {
    $scope.templatePrefix = '/Performance/web/js/template/Profiler/Statistic/';
    $scope.tabs = [{
            title : 'profiler.measure.test.detail.summary',
            template : $scope.templatePrefix + 'summary.html'
        }, {
            title : 'profiler.measure.test.detail.callStack',
            template : $scope.templatePrefix + 'callStack.html'
        }, {
            title : 'profiler.measure.test.detail.functionStatistics',
            template : $scope.templatePrefix + 'functionStat.html'
    }];

    $scope.id = $routeParams.id;
    $scope.statistic;

    $http.get('profiler/test/attempt/' + $scope.id + '/statistic').success(function(response) {
        $scope.statistic = response;
    });
}

function ProfilerCallStackCtrl($scope, $http, $routeParams) {
    $scope.attemptId = $routeParams.id;
    $scope.calls = [];

    $http.get('profiler/test/attempt/' + $scope.attemptId + '/callStack/parent/0').success(function(response) {
        $scope.calls = response;
    });

    $scope.showCall = function(call) {
        if (call.calls === undefined) {
            $http.get('profiler/test/attempt/' + $scope.attemptId + '/callStack/parent/' + call.id).success(function(response) {
                call.calls = response;
            });
        }
    };
}

function ProfilerFunctionStatCtrl($scope, $http, $routeParams) {
    $scope.predicate = 'id';
    $scope.reverse   = false;
    $scope.attemptId = $routeParams.id;
    $scope.calls = [];

    $scope.totalItems  = 0;
    $scope.currentPage = 1;
    $scope.maxSize     = 5;
    $scope.pageSize    = 10;
    $scope.pageSizes   = [10, 20, 50, 100];

    $scope.setPage = function (pageNo) {
        $scope.currentPage = pageNo;
    };

    $http.get('profiler/test/attempt/' + $scope.attemptId + '/statistic/function').success(function(response) {
        $scope.calls      = response;
        $scope.totalItems = response.length;
    });

    $scope.refresh = function(input) {
        $scope.totalItems = input.length;

        return input;
    };
}