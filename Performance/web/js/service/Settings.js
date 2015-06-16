angular.module('PM');

perfModule.service('SettingsService', function ($http, AbstractService) {

    _.extend(this, AbstractService);

    this.getTasks = function(success, error) {
        var get = $http.get('/cron/task/find');
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.deleteTask = function(id, success, error) {
        var request = $http.delete('/cron/task/delete/' + id);
        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.getTask = function(id, success, error) {
        var get = $http.get('/cron/task/get/' + id);
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.saveCron = function(task, success, error) {
        var request;

        if (task.hasOwnProperty('id')) {
            request = $http.put('/cron/task/update/' + task.id, task);
        } else {
            request = $http.post('/cron/task/create', task);
        }

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.getMenusActions = function(success, error) {
        var get = $http.get('/cron/task/menus/actions');
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.getGearmanStatus = function(success, error) {
        var get = $http.get('/settings/gearman/status');
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.getGearmanWorkers = function(success, error) {
        var get = $http.get('/settings/gearman/workers');
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.getWorker = function(id, success, error) {
        var get = $http.get('/settings/gearman/worker/get/' + id);
        AbstractService._assignFunctions(get, success, error);

        return get;
    };

    this.saveWorker = function(worker, success, error) {
        var request;

        if (worker.hasOwnProperty('id')) {
            request = $http.put('/settings/gearman/worker/update/' + worker.id, worker);
        } else {
            request = $http.post('/settings/gearman/worker/create', worker);
        }

        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.deleteWorker = function(id, success, error) {
        var request = $http.delete('/settings/gearman/worker/delete/' + id);
        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.stopAllWorkers = function(status, worker, success, error) {
        var request = $http.post('/settings/gearman/worker/stopAll', {status: status, worker: worker});
        AbstractService._assignFunctions(request, success, error);

        return request;
    };

    this.controlWorkers = function(control, success, error) {
        var request = $http.post('/settings/gearman/worker/control', {control: control});
        AbstractService._assignFunctions(request, success, error);

        return request;
    };
});