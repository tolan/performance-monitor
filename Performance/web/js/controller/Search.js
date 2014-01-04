
function SearchMainCtrl($scope) {
    $scope.errorMessage = null;

    $scope.$on('search-finded', function(event, result) {
        $scope.$broadcast('search-show-result', result);
        $scope.errorMessage = null;
    });

    $scope.$on('search-drop-filter', function(event) {
        $scope.$broadcast('search-hide-result');
        $scope.errorMessage = null;
    });

    $scope.$on('search-error', function(event) {
        $scope.errorMessage = 'main.server.error';
    });
}


function SearchFiltersCtrl($scope, $http, $timeout) {
    $scope.templatePrefix = '/Performance/web/js/template/Search/Filters/';
    $scope.template = $scope.templatePrefix + 'main.html';
    $scope.target;
    $scope.menu = [];
    $scope.originalMenu = [];
    $scope.filters = [];
    $scope.templates = {
        'query'  : $scope.templatePrefix + 'query.html',
        'string' : $scope.templatePrefix + 'string.html',
        'date'   : $scope.templatePrefix + 'date.html',
        'enum'   : $scope.templatePrefix + 'enum.html',
        'int'    : $scope.templatePrefix + 'string.html',
        'float'  : $scope.templatePrefix + 'string.html'
    };
    $scope.timer;
    $scope.timerInterval = 800;
    $scope.resultTotal;

    $http.get('search/filters/menu').success(function(menu) {
        $scope.originalMenu = menu;
        $scope.menu = menu;
    });

    $scope.selectFilter = function(event, item) {
        if (item.hasOwnProperty('target')) {
            $scope.$emit('search-drop-filter');
            $scope.target = item.target;
            $scope.menu   = $scope.originalMenu[item.target].submenu;

            $http.get('search/filter/' + item.target + '/' + item.filter).success(function(filter) {
                filter.operator = filter.hasOwnProperty('operators') ? _.first(filter.operators).value : null;
                filter.value    = filter.hasOwnProperty('values')    ? _.first(filter.values).value    : null;

                $scope.filters.push(filter);
            });
        }
    };

    $scope.send = function(filter) {
        $timeout.cancel($scope.timer);
        $scope.timer = $timeout(function() {
            $scope._sendFilters($scope.filters, false);
        }, $scope.timerInterval);
    };

    $scope.sendAll = function() {
        $timeout.cancel($scope.timer);
        $scope._sendFilters($scope.filters, true);
    };

    $scope._sendFilters = function(filters, show) {
        var request = {
            'target'  : $scope.target,
            'filters' : filters
        };

        $http.post('search/find', request).success(function(response) {
            $scope.resultTotal = response.result.length;

            if (show && $scope.resultTotal > 0) {
                $scope.$emit('search-finded', response);
            }
        }).error(function() {
            $scope.$emit('search-error');
        });
    };

    $scope.dropFilter = function(filter) {
        var index = _.indexOf($scope.filters, filter);
        $scope.filters.splice(index, 1);

        if ($scope.filters.length === 0) {
            $scope.target = null;
            $scope.menu = $scope.originalMenu;
        } else {
            $scope.send();
        }

        $scope.$emit('search-drop-filter');
    };

    $scope.$on('menu-selected-item', $scope.selectFilter);
}

function SearchResultCtrl($scope) {
    $scope.templatePrefix = '/Performance/web/js/template/Search/Result/';
    $scope.template = $scope.templatePrefix + 'main.html';
    $scope.templates = {
        'attempt' : $scope.templatePrefix + 'attempt.html',
        'test'    : $scope.templatePrefix + 'test.html',
        'measure' : $scope.templatePrefix + 'measure.html'
    };

    $scope.target;
    $scope.items = [];
    $scope.resultTotal;

    $scope.currentPage = 1;
    $scope.maxSize     = 5;
    $scope.pageSize    = 10;
    $scope.pageSizes   = [10, 20, 50, 100];

    $scope.$on('search-show-result', function(event, result) {
        $scope.target = result.target;
        $scope.items  = result.result;
        $scope.resultTotal = $scope.items.length;;
    });

    $scope.$on('search-hide-result', function(event) {
        $scope.target = null;
        $scope.items  = [];
    });
}