var perfModule = angular.module('PM');

perfModule.service('StatisticService', function ($http, AbstractService) {

    _.extend(this, AbstractService);

    this.getTemplates = function(success, error) {
        var get = $http.get('/statistic/template/find');
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.getViewsConfig = function(success, error) {
        var get = $http.get('/statistic/config/views');
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.getTemplate = function(id, success, error) {
        var get = $http.get('/statistic/template/get/' + id);
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.saveTemplate = function(template, success, error) {
        var request;

        if (template.hasOwnProperty('id')) {
            request = $http.put('/statistic/template/update/' + template.id, template);
        } else {
            request = $http.post('/statistic/template/create', template);
        }

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.deleteTemplate = function(id, success, error) {
        var request = $http.delete('/statistic/template/delete/' + id);

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.getSets = function(success, error) {
        var get = $http.get('/statistic/set/get');
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.deleteSet = function(id, success, error) {
        var request = $http.delete('/statistic/set/delete/' + id);

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.saveSet = function(set, success, error) {
        var request;

        if (set.hasOwnProperty('id')) {
            request = $http.put('/statistic/set/update' + set.id, set);
        } else {
            request = $http.post('/statistic/set/create', set);
        }

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.getSet = function(id, includeRuns, success, error) {
        var get = $http.get('/statistic/set/get/' + id, {params : {includeRuns: includeRuns}});

        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.deleteRun = function(id, success, error) {
        var request = $http.delete('/statistic/run/delete/' + id);

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.startRun = function(id, success, error) {
        var request = $http.post('/statistic/run/start/' + id);

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.getRun = function(id, includeData, includeTemplate, success, error) {
        var get = $http.get('/statistic/run/get/' + id, {params : {includeData: includeData, includeTemplate: includeTemplate}});

        AbstractService._assignFunctions(get, success, error);

        return get;
    };
});

