var perfModule = angular.module('Perf');

perfModule.service('StatisticService', function ($http, AbstractService) {

    _.extend(this, AbstractService);

    this.getTemplates = function(success, error) {
        var get = $http.get('/statistic/templates');
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.getViewsConfig = function(success, error) {
        var get = $http.get('/statistic/views/config');
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.getTemplate = function(id, success, error) {
        var get = $http.get('/statistic/template/' + id);
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.saveTemplate = function(template, success, error) {
        var request;

        if (template.hasOwnProperty('id')) {
            request = $http.put('/statistic/template/' + template.id, template);
        } else {
            request = $http.post('/statistic/template', template);
        }

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.deleteTemplate = function(id, success, error) {
        var request = $http.delete('/statistic/template/' + id);

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.getSets = function(success, error) {
        var get = $http.get('/statistic/sets');
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.deleteSet = function(id, success, error) {
        var request = $http.delete('/statistic/set/' + id);

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.saveSet = function(set, success, error) {
        var request;

        if (set.hasOwnProperty('id')) {
            request = $http.put('/statistic/set/' + set.id, set);
        } else {
            request = $http.post('/statistic/set', set);
        }

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.getSet = function(id, includeRuns, success, error) {
        var get = $http.get('/statistic/set/' + id, {params : {includeRuns: includeRuns}});

        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.deleteRun = function(id, success, error) {
        var request = $http.delete('/statistic/set/run/' + id);

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.startRun = function(id, success, error) {
        var request = $http.post('/statistic/set/run/' + id + '/start');

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.getRun = function(id, includeData, includeTemplate, success, error) {
        var get = $http.get('/statistic/set/run/' + id, {params : {includeData: includeData, includeTemplate: includeTemplate}});

        AbstractService._assignFunctions(get, success, error);

        return get;
    };
});

