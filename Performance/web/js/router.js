
perfModule.config([
    '$routeProvider', '$locationProvider',
    function($routeProvider, $locationProvider) {
        var templateDir = '/js/template/';
        $routeProvider.
            // ### PROFILER ### //
            when('/profiler/mysql/scenarios', {templateUrl: templateDir+'Profiler/Scenario/list.html',   controller: ProfilerMySQLScenariosList}).
            // scenario
            when('/profiler/mysql/scenario/create', {templateUrl: templateDir+'Profiler/Scenario/create.html', controller: ProfilerMySQLScenarioCreate}).
            when('/profiler/mysql/scenario/edit/:id', {templateUrl: templateDir+'Profiler/Scenario/create.html', controller: ProfilerMySQLScenarioCreate}).
            when('/profiler/mysql/scenario/detail/:id', {templateUrl: templateDir+'Profiler/Scenario/detail.html', controller: ProfilerMySQLScenarioDetail}).
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


            // ### STATISTIC ### //
            // template
            when('/statistic/templates', {templateUrl: templateDir+'Statistics/Template/list.html', controller: StatisticTemplateList}).
            when('/statistic/template/create', {templateUrl: templateDir+'Statistics/Template/create.html', controller: StatisticTemplateCreate}).
            when('/statistic/template/edit/:id', {templateUrl: templateDir+'Statistics/Template/create.html', controller: StatisticTemplateCreate}).
            // set
            when('/statistic/sets', {templateUrl: templateDir+'Statistics/Set/list.html', controller: StatisticSetList}).
            when('/statistic/set/create', {templateUrl: templateDir+'Statistics/Set/create.html', controller: StatisticSetCreate}).
            when('/statistic/set/edit/:id', {templateUrl: templateDir+'Statistics/Set/create.html', controller: StatisticSetCreate}).
            when('/statistic/set/detail/:id', {templateUrl: templateDir+'Statistics/Set/detail.html', controller: StatisticSetDetail}).
            // run
            when('/statistic/set/run/detail/:id', {templateUrl: templateDir+'Statistics/Run/detail.html', controller: StatisticRunDetail}).
            // ### STATISTIC END ### //

            // DEFAULT PAGE
            otherwise({redirectTo: '/profiler/mysql/scenarios'});

        $locationProvider.html5Mode(false);
    }
]);