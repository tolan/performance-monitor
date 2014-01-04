
perfModule.config([
    '$routeProvider', '$locationProvider',
    function($routeProvider, $locationProvider) {
        var templateDir = '/Performance/web/js/template/';
        $routeProvider.
            // ### PROFILER ### //
            // measure
            when('/profiler/measure/list',       {templateUrl: templateDir+'Profiler/Measure/list.html',   controller: ProfilerMeasureListCtrl}).
            when('/profiler/measure/create',     {templateUrl: templateDir+'Profiler/Measure/create.html', controller: ProfilerMeasureCreateCtrl}).
            when('/profiler/measure/edit/:id',   {templateUrl: templateDir+'Profiler/Measure/create.html', controller: ProfilerMeasureCreateCtrl}).
            when('/profiler/measure/detail/:id', {templateUrl: templateDir+'Profiler/Measure/detail.html', controller: ProfilerMeasureDetailCtrl}).

            // test
            when('/profiler/measure/detail/:measureId/test/:id', {templateUrl: templateDir+'Profiler/Test/list.html', controller: ProfilerTestListCtrl}).

            // attempt
            when('/profiler/test/:testId/attempt/:id', {templateUrl: templateDir+'Profiler/Test/detail.html', controller: ProfilerAttemptDetailCtrl}).
            // ### PROFILER END ### //

            // ### SEARCH ### //
            when('/search', {templateUrl: templateDir+'Search/main.html', controller: SearchMainCtrl}).
            // ### SEARCH END ### //

            // DEFAULT PAGE
            otherwise({redirectTo: '/profiler/measure/list'});

        $locationProvider.html5Mode(false);
    }
]);