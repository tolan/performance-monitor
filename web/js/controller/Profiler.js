
function ProfilerListCtrl($scope, $http, $modal) {
    $scope.predicate = 'id';
    $scope.reverse   = false;
    $scope.measures  = [];
    $scope.deleteMeasureId;

    $http.get('profiler/measures').success(function(response) {
        $scope.measures = response;
    });

    $scope.selectSortClass = function(column, predicate, reverse) {
        var style = 'icon-minus';
        if (column === predicate) {
            style = 'icon-chevron-' + ((reverse === undefined || reverse === true) ? 'up' : 'down');
        }

        return style;
    };

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

    $scope.parseParams = function(params) {
        var i, param, result = [];

        for (i in params) {
            param = params[i];

            if (param.key !== null) {
                result.push(param.key + ' = ' + param.value);
            }
        }

        return result.join(', ');
    };
}

function ProfilerCreateCtrl($scope, $http, $routeParams, $window, $location) {
    $scope.name;
    $scope.description;
    $scope.link;
    $scope.parameters = [{}];

    // It means that it is edit UC.
    if ($routeParams.hasOwnProperty('id')) {
        $http.get('profiler/measure/' + $routeParams.id).success(function(response) {
            $scope.name        = response.name;
            $scope.description = response.description;
            $scope.link        = response.link;
            $scope.parameters  = response.parameters.length > 0 ? response.parameters : [{}];
        });
    }

    $scope.send = function() {
        var measure = {
            name        : $scope.name,
            description : $scope.description,
            link        : $scope.link,
            parameters  : $scope.parameters
        };

        if ($routeParams.hasOwnProperty('id')) {
            $http.put('profiler/measure/' + $routeParams.id, measure).success(function(response) {
                $location.path('/profiler/list');
            });
        } else {
            $http.post('profiler/measure', measure).success(function(response) {
                $location.path('/profiler/list');
            });
        }
    };

    $scope.back = function() {
        if ($window.history.length === 1) {
            $location.path('/profiler/list');
        } else {
            $window.history.back();
        }
    };

    $scope.deleteParam = function(index) {
        $scope.parameters.splice(index, 1);
        if($scope.parameters.length === 0) {
            $scope.addParam();
        }
    };

    $scope.addParam = function() {
        $scope.parameters.push({key: '', value: ''});
    };
}

function ProfilerDetailCtrl($scope, $http, $routeParams, $window, $location, $timeout) {
    $scope.predicate = 'id';
    $scope.reverse   = false;
    $scope.attempts  = [];
    $scope.measureId = $routeParams.id;
    var timer = {};

    $http.get('profiler/measure/' + $scope.measureId + '/attempts').success(function(response) {
        $scope.attempts = response;
    });

    $scope.selectSortClass = function(column, predicate, reverse) {
        var style = 'icon-minus';
        if (column === predicate) {
            style = 'icon-chevron-' + ((reverse === undefined || reverse === true) ? 'up' : 'down');
        }

        return style;
    };

    $scope.deleteAttempt = function(id) {
        if (timer.hasOwnProperty(id)) {
            $timeout.cancel(timer[id]);
        }

        $http.delete('profiler/measure/attempt/' + id).success(function(response) {
            if (response === "true") {
                var key = _.indexOf($scope.attempts, _.findWhere($scope.attempts, {id : id}));;
                $scope.attempts.splice(key, 1);
            }
        });
    };

    $scope.startAttempt = function() {
        $http.post('profiler/measure/attempt/' + $scope.measureId + '/start').success(function(response) {
            $http.get('profiler/measure/' + $scope.measureId + '/attempts').success(function(response) {
                var index;

                for(index in response) {
                    if(!$scope.attempts.hasOwnProperty(index)) {
                        $scope.watch(response[index].id);
                    }
                }

                $scope.attempts = response;
            });
        });
    };

    $scope.watch = function(id) {
        timer[id] = $timeout(function() {
            $http.get('profiler/measure/attempt/' + id).success(function(response) {
                var key = _.indexOf($scope.attempts, _.findWhere($scope.attempts, {id : id}));;
                $scope.attempts[key] = response;

                if (response.state !== 'statistic_generated' && response.state !== 'error') {
                    $scope.watch(id);
                } else {
                    $timeout.cancel(timer[id]);
                }
            });
        }, 1000);
    };

    $scope.back = function() {
        if ($window.history.length === 1) {
            $location.path('/profiler/list');
        } else {
            $window.history.back();
        }
    };

    $scope.indexOf = function(attempt) {
        return _.indexOf($scope.attempts, attempt);
    };
}

function ProfilerAttemptDetailCtrl($scope, $http, $routeParams) {
    $scope.templatePrefix = '/Performance/web/js/template/Profiler/Statistic/';
    $scope.tabs = [{
            title : 'Souhrn',
            template : $scope.templatePrefix + 'summary.html'
        }, {
            title : 'Strom volání',
            template : $scope.templatePrefix + 'callStack.html'
        }, {
            title : 'Statistiky volání',
            template : $scope.templatePrefix + 'functionStat.html'
    }];

    $scope.id = $routeParams.id;
    $scope.statistic;
    $scope.callStack;

    $http.get('profiler/measure/attempt/' + $scope.id + '/statistic').success(function(response) {
        $scope.statistic = response;
    });
}

function ProfilerCallStackCtrl($scope, $http, $routeParams) {
    $scope.attemptId = $routeParams.id;
    $scope.calls = [];

    $http.get('profiler/measure/attempt/' + $scope.attemptId + '/callStack/parent/0').success(function(response) {
        $scope.calls = response;
    });

    $scope.showCall = function(call) {
        if (call.calls === undefined) {
            $http.get('profiler/measure/attempt/' + $scope.attemptId + '/callStack/parent/' + call.id).success(function(response) {
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
    $scope.pageSizes = [10, 20, 50, 100];

    $scope.setPage = function (pageNo) {
        $scope.currentPage = pageNo;
    };

    $http.get('profiler/measure/attempt/' + $scope.attemptId + '/statistic/function').success(function(response) {
        $scope.calls      = response;
        $scope.totalItems = response.length;
    });

    $scope.selectSortClass = function(column, predicate, reverse) {
        var style = 'icon-minus';
        if (column === predicate) {
            style = 'icon-chevron-' + ((reverse === undefined || reverse === true) ? 'up' : 'down');
        }

        return style;
    };

    $scope.refresh = function(input) {
        $scope.totalItems = input.length;

        return input;
    }
}