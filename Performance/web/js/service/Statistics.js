var perfModule = angular.module('Perf');

perfModule.service('StatisticService', function ($http, AbstractService) {

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
});

