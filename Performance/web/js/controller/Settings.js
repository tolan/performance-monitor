angular.module('PM')
    .controller('SettingsCronCtrl', SettingsCronCtrl)
    .controller('SettingsCronCreateCtrl', SettingsCronCreateCtrl)
    .controller('SettingsCronTaskTrigger', SettingsCronTaskTrigger)
    .controller('SettingsCronTaskTriggerAction', SettingsCronTaskTriggerAction)
    .controller('SettingsGearmanCtrl', SettingsGearmanCtrl)
    .controller('SettingsGearmanWorkersCtrl', SettingsGearmanWorkersCtrl)
    .controller('SettingsGearmanCreateCtrl', SettingsGearmanCreateCtrl);

/**
 * Controller for show list of crons.
 *
 * @param $scope          Scope
 * @param $modal          Modal component
 * @param SettingsService Settings service
 *
 * @returns void
 */
function SettingsCronCtrl ($scope, $modal, SettingsService) {
    $scope.initList('name');
    $scope.tasks = [];
    $scope.deleteTaskId;

    SettingsService.getTasks(
        function (response) {
            $scope.tasks = response;
        }
    );

    $scope.deleteTaskDialog = function(id) {
        $scope.deleteTaskId = id;
        $modal({
            template : 'settings-cron-modal-delete.html',
            persist  : true,
            show     : true,
            scope    : $scope
        });
    };

    $scope.deleteTask = function(id) {
        SettingsService.deleteTask(
            id,
            function() {
                $scope.tasks = _.without($scope.tasks, _.findWhere($scope.tasks, {id : id}));
            }
        );
    };
}

/**
 * Controller for create/edit cron.
 *
 * @param $scope          Scope
 * @param $routeParams    Route params
 * @param SettingsService Settings service
 *
 * @returns void
 */
function SettingsCronCreateCtrl ($scope, $routeParams, SettingsService) {
    $scope.task = {
        name        : null,
        description : null,
        triggers    : [{
            timer : {},
            actions : [{}]
        }]
    };

    $scope.validation = {
        name     : ['required'],
        triggers : ['required']
    };


    if ($routeParams.hasOwnProperty('id')) {
        SettingsService.getTask(
            $routeParams.id,
            function(task) {
                _.extend($scope.task, task);
            }
        );
    } else {
        $scope.cacheObject('task');
    }

    $scope.addTrigger = function() {
        $scope.task.triggers.push({
            timer : {},
            actions : []
        });

        $scope.isValid();
    };

    $scope.removeTrigger = function(trigger) {
        $scope.task.triggers = _.without($scope.task.triggers, trigger);
        $scope.isValid();
    };

    $scope.save = function() {
        if ($scope.isValid()) {
            SettingsService.saveCron(
                $scope.task,
                function() {
                    $scope.cleanCache('task');
                    $scope.back('/settings/cron');
                },
                function() {
                    $scope.back('/settings/cron');
                }
            );
        }
    };

    $scope.isValid = function() {
        var validation = $scope.validator.validate($scope.task, $scope.validation, $scope),
            triggers   = [];

        $scope.$broadcast('validate:trigger', triggers);

        return _.isEmpty(validation) && _.isEmpty(triggers);
    };
}

/**
 * Controller for create/edit trigger of cron.
 *
 * @param $scope Scope
 *
 * @returns void
 */
function SettingsCronTaskTrigger ($scope) {
    $scope.htmlTemplate = '/js/template/Settings/Cron/trigger.html';

    _.defaults($scope.trigger.timer, {
        minute    : '*',
        hour      : '*',
        day       : '*',
        month     : '*',
        dayOfWeek : '*'
    });

    $scope.validation = {
        timer : function(value) {
            var regexFunc = function(value) {
                    var valid   = null,
                        pattern = /^(?:[1-9]?\d|\*)(?:(?:[\/-][1-9]?\d)|(?:,[1-9]?\d)+)?$/;

                    valid = value.match(pattern) ? valid : 'settings.cron.task.trigger.timer.invalidCron';

                    return valid;
                },
                validation = {
                    minute    : ['required', regexFunc],
                    hour      : ['required', regexFunc],
                    day       : ['required', regexFunc],
                    month     : ['required', regexFunc],
                    dayOfWeek : ['required', regexFunc]
                },
                valid = $scope.validator.validate(value, validation);

            return _.isEmpty(valid) ? null : valid;
        },
        actions : 'required'
    };

    $scope.$on('validate:trigger', function(event, triggersValidation) {
        var validation = $scope.isValid();

        if (!_.isEmpty(validation)) {
            triggersValidation.push(validation);
        }
    }, this);

    $scope.addAction = function() {
        $scope.trigger.actions.push({});
        $scope.isValid();
    };

    $scope.removeAction = function(action) {
        $scope.trigger.actions = _.without($scope.trigger.actions, action);
        $scope.isValid();
    };

    $scope.isValid = function() {
        var validation = $scope.validator.validate($scope.trigger, $scope.validation, $scope),
            actions    = [];
            $scope.$broadcast('validate:action', actions);

        if (!_.isEmpty(actions)) {
            validation.actions = actions;
        }

        return validation;
    };
}

/**
 * Controller for create/edit task action of trigger..
 *
 * @param $scope          Scope
 * @param SettingsService Settings service
 *
 * @returns void
 */
function SettingsCronTaskTriggerAction ($scope, SettingsService) {
    $scope.htmlTemplate = '/js/template/Settings/Cron/action.html';
    $scope.menu         = {};

    $scope.action.tasks           = $scope.action.tasks || [];
    $scope.action.source          = $scope.action.source || {};
    _.defaults($scope.action.source, {
        type     : 'template',
        items    : [],
        template : {},
        target   : null
    });

    $scope.searchSource = $scope.action.source;

    $scope.validation = {};

    $scope.cacheObject('menu');
    if (_.isEmpty($scope.menu)) {
        SettingsService.getMenusActions(
            function(menus) {
                $scope.menu = menus;
            }
        );
    }

    $scope.removeTask = function(task) {
        $scope.action.tasks = _.without($scope.action.tasks, task);
    };

    $scope.$on('menu-selected-action', function(event, action) {
        $scope.action.tasks.push({
            target : action.target,
            action : action.action
        });
    });

    $scope.$on('validate:action', function(event, actionsValidation) {
        var validation = $scope.isValid();

        if (!_.isEmpty(validation)) {
            actionsValidation.push(validation);
        }
    });

    $scope._cleanActions = function(oldTarget, newTarget) {
        if (oldTarget !== newTarget) {
            $scope.action.tasks = [];
        }
    };

    $scope.$watch('action.source.target', $scope._cleanActions);

    $scope.isValid = function() {
        var validation = $scope.validator.validate($scope.action, $scope.validation, $scope);

        if (_.isFunction($scope.isValidSource)) {
            $scope.isValidSource(validation);
        }

        return validation;
    };

    $scope.$on('search-ready', function() {
        if (!_.isEmpty($scope.action.source.template)) {
            $scope.$broadcast('search-template-select', $scope.action.source.template);
        }
    });
}

/**
 * Controller for show list of gearman worker statuses.
 *
 * @param $scope          Scope
 * @param $timeout        Timeout
 * @param SettingsService Settings service
 *
 * @returns void
 */
function SettingsGearmanCtrl ($scope, $timeout, SettingsService) {
    $scope.initList('name');
    $scope.loading = 0;
    $scope.timer;

    $scope.MODE_MANUAL    = 'manual'
    $scope.MODE_KEEP      = 'keep'
    $scope.MODE_ON_DEMAND = 'on_demand'

    $scope.interval = 2000;
    $scope.workers  = [];
    $scope.control  = [];

    $scope.modeMenu = [{
        text: 'settings.gearman.list.mode.' + $scope.MODE_MANUAL,
        value: $scope.MODE_MANUAL
    }, {
        text: 'settings.gearman.list.mode.' + $scope.MODE_KEEP,
        value: $scope.MODE_KEEP
    }, {
        text: 'settings.gearman.list.mode.' + $scope.MODE_ON_DEMAND,
        value: $scope.MODE_ON_DEMAND
    }];

    var _prepareControl = function(statuses, workers) {
        var actual,
            control = [];

        _.each(
            statuses,
            function(status) {
                actual           = _.findWhere($scope.control, {name: status.name});
                status.requested = status.available;
                status.mode      = $scope.MODE_MANUAL;

                if (actual) {
                    status.requested = actual.status.requested;
                    status.mode      = actual.status.mode;
                }

                if (status.mode === $scope.MODE_MANUAL) {
                    status.requested = 0;
                }

                control.push({
                    name   : status.name,
                    status : status,
                    worker : $scope.getWorkerForStatus(status, workers)
                });
            }
        );

        return control;
    };

    var _initData = function() {
        $scope.mask();
        SettingsService.synchronize({
            statuses : SettingsService.getGearmanStatus(),
            workers  : SettingsService.getGearmanWorkers()
        }).on('synced', function(event, requests, responses) {
            $scope.workers = responses.workers;
            $scope.control = _prepareControl(responses.statuses, responses.workers);

            $scope.unmask();
        });
    };

    var _isChanged = function(statuses, control) {
        var status,
            isChanged = statuses.length !== control.length,
            _isSame   = function(first, second) {
                var isSame = true;

                isSame = first.queue === second.queue;
                isSame = isSame && first.running === second.running;
                isSame = isSame && first.available === second.available;

                return isSame;
            };

        if (!isChanged) {
            _.each(control, function(item) {
                if (!isChanged) {
                    status = _.findWhere(statuses, {name: item.status.name});
                    if (!status || !_isSame(status, item.status)) {
                        isChanged = true;
                    };
                }
            });
        }

        return isChanged;
    };

    $scope.getWorkerForStatus = function(status, workers) {
        var pattern,
            result = _.findWhere(workers, {name: status.name}) || null;

        if (!result) {
            _.each(workers, function(worker) {
                pattern = new RegExp(worker.name);

                if (!result && pattern.exec(status.name)) {
                    result = worker;
                }
            });
        }

        return result;
    };

    $scope.deleteWorker = function(id) {
        SettingsService.deleteWorker(id, _initData, _initData);
    };

    $scope.plusWorker = function(status) {
        status.requested++;
    };

    $scope.minusWorker = function(status) {
        var min = (status.mode === $scope.MODE_MANUAL) ? -status.available : 0;

        if (status.requested > min) {
            status.requested--;
        }
    };

    $scope.stopWorkers = function(status) {
        status.requested = (status.mode === $scope.MODE_MANUAL) ? -status.available : 0;
    };

    var _refresh = function() {
        $scope.unmask().blockLoad(true);
        $scope.loading = 100;
        SettingsService.controlWorkers(
            $scope.control,
            function(response) {
                if (_isChanged(response, $scope.control)) {
                    $scope.control = _prepareControl(response, $scope.workers);
                }

                $scope.blockLoad(false);

                $timeout(function() {
                    $scope.loading = 0;
                }, 250);

                if (_refresh) {
                    $scope.timer = $timeout(_refresh, $scope.interval);
                }
            }
        );
    };

    $scope.refreshDelay = function() {
        $scope.interval = Math.max($scope.interval || 0, 500);
        var value       = $scope.interval;

        $timeout(function() {
            if (value === $scope.interval) {
                $timeout.cancel($scope.timer);
                $scope.timer = $timeout(_refresh, $scope.interval);
            }
        }, 500);
    };

    $scope.timer = $timeout(_refresh, $scope.interval);

    $scope.$on('$locationChangeSuccess', function() {
        $scope.blockLoad(false);
        _refresh = undefined;
    });

    $scope.$on('menu-selected-item', function(event, item, status) {
        status.mode = item.value;

        if (status.mode !== $scope.MODE_MANUAL) {
            status.requested = status.available;
        }
    });

    _initData();
};

/**
 * Controller for show list of gearman wokrer templates.
 *
 * @param $scope          Scope
 * @param $modal          Modal component
 * @param SettingsService Settings service
 *
 * @returns void
 */
function SettingsGearmanWorkersCtrl ($scope, $modal, SettingsService) {
    $scope.initList('name');
    $scope.workers = [];
    $scope.deleteWorkerId;

    SettingsService.getGearmanWorkers(
        function (response) {
            $scope.workers = response;
        }
    );

    $scope.deleteWorkerDialog = function(id) {
        $scope.deleteWorkerId = id;
        $modal({
            template : 'settings-worker-modal-delete.html',
            persist  : true,
            show     : true,
            scope    : $scope
        });
    };

    $scope.deleteWorker = function(id) {
        SettingsService.deleteWorker(
            id,
            function() {
                $scope.workers = _.without($scope.workers, _.findWhere($scope.workers, {id : id}));
            }
        );
    };
}

/**
 * Controller for create/edit gearman worker template.
 *
 * @param $scope          Scope
 * @param $routeParams    Route params
 * @param SettingsService Settings service
 *
 * @returns void
 */
function SettingsGearmanCreateCtrl ($scope, $routeParams, SettingsService) {
    $scope.worker = {
        name   : null,
        script : null
    };

    $scope.validation = {
        name   : ['required'],
        script : ['required']
    };

    if ($routeParams.hasOwnProperty('id')) {
        SettingsService.getWorker(
            $routeParams.id,
            function(worker) {
                $scope.worker = worker;
            }
        );
    } else if ($routeParams.hasOwnProperty('name')) {
        $scope.worker.name = $routeParams.name;
    }

    $scope.save = function() {
        if ($scope.isValid()) {
            SettingsService.saveWorker(
                $scope.worker,
                function() {
                    $scope.back('/settings/gearman');
                },
                function() {
                    $scope.back('/settings/gearman');
                }
            );
        }
    };

    $scope.isValid = function() {
        var validation = $scope.validator.validate($scope.worker, $scope.validation, $scope);

        return _.isEmpty(validation);
    };
};