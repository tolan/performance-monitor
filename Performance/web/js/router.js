
perfModule.config([
    '$routeProvider', '$locationProvider',
    function($routeProvider, $locationProvider) {
        var templateDir = base + '/js/template/';
        $routeProvider.
            // ### PROFILER ### //
            when('/profiler/mysql/scenarios',       {templateUrl: templateDir+'Profiler/Scenario/list.html',   controller: ProfilerMySQLScenariosList}).
            when('/profiler/mysql/scenario/create',     {templateUrl: templateDir+'Profiler/Scenario/create.html', controller: ProfilerMySQLScenarioCreate}).
            when('/profiler/mysql/scenario/edit/:id',   {templateUrl: templateDir+'Profiler/Scenario/create.html', controller: ProfilerMySQLScenarioCreate}).
            when('/profiler/mysql/scenario/detail/:id',   {templateUrl: templateDir+'Profiler/Scenario/detail.html', controller: ProfilerMySQLScenarioDetail}).
            // test
            when('/profiler/mysql/scenario/test/detail/:id', {templateUrl: templateDir+'Profiler/Test/list.html', controller: ProfilerMySQLTestListCtrl}).
            // measure
            when('/profiler/:type/scenario/test/measure/:id', {templateUrl: templateDir+'Profiler/Measure/detail.html', controller: ProfilerMeasureDetailCtrl}).
            when('/profiler/:type/measure/:id', {templateUrl: templateDir+'Profiler/Measure/detail.html', controller: ProfilerMeasureDetailCtrl}).
            // files
            when('/profiler/file/list', {templateUrl: templateDir+'Profiler/File/list.html', controller: ProfilerFileListCtrl}).
            // ### PROFILER END ### //



            // ### SEARCH ### //
            when('/search', {templateUrl: templateDir+'Search/main.html', controller: SearchMainCtrl}).
            // ### SEARCH END ### //

            // DEFAULT PAGE
            otherwise({redirectTo: '/profiler/mysql/scenarios'});

        $locationProvider.html5Mode(false);
    }
]);