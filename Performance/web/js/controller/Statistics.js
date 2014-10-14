
function StatisticTemplateList($scope, $modal, StatisticService) {
    $scope.initList('name');
    $scope.templates = [];
    $scope.deleteTemplateId;

    StatisticService.getTemplates(
        function (response) {
            $scope.templates = response;
        }
    );

    $scope.deleteTemplate = function(id) {
        StatisticService.deleteTemplate(
            id,
            function() {
                $scope.templates = _.without($scope.templates, _.findWhere($scope.templates, {id : id}));
            }
        );
    };

    $scope.deleteTemplateDialog = function(id) {
        $scope.deleteTemplateId = id;
        $modal({
            template : 'statistic-template-modal-delete.html',
            persist  : true,
            show     : true,
            scope    : $scope
        });
    };
}

function StatisticTemplateCreate($scope, StatisticService, $routeParams) {
    $scope.cachePrefix        = arguments.callee.name;
    $scope.searchErrorMessage = null;
    $scope.template = {};
    $scope.cacheObject('template');

    _.extend($scope.template, {
        name        : null,
        description : null,
        source      : {
            type     : 'template',
            template : {},
            items    : [],
            target   : null
        },
        views       : []
    });

    // It means that it is edit UC.
    if ($routeParams.hasOwnProperty('id')) {
        $scope.$on('search-ready', function() {
            StatisticService.getTemplate(
                $routeParams.id,
                function(template) {
                    _.extend($scope.template, template);
                    $scope.$broadcast('search-template-select', $scope.template.source.template);
                    $scope.$broadcast('statistic-template-loaded', $scope.template);
                }
            );
        });
    }

    $scope.$on('search-ready', function() {
        if (!_.isEmpty($scope.template.source.template)) {
            $scope.$broadcast('search-template-select', $scope.template.source.template);
        }
    });

    $scope.save = function() {
        if ($scope.isValid()) {
            StatisticService.saveTemplate(
                $scope.template,
                function() {
                    $scope.cleanCache('template');
                    $scope.back('/statistic/template');
                }
            );
        }
    };

    $scope.isValid = function() {
        $scope.validation = {};

        delete($scope.template.validation);

        if (_.isEmpty($scope.template.name)) {
            $scope.validation.name = 'error';
        }

        if (_.isFunction($scope.isValidSource)) {
            $scope.isValidSource($scope.validation);
        }

        if (_.isFunction($scope.isValidViews)) {
            $scope.isValidViews($scope.validation);
        }

        return _.values($scope.validation).length === 0;
    };
}

function StatisticTemplateSource($scope) {
    $scope.initList('id');
    $scope.htmlTemplate         = '/js/template/Statistics/Template/create/source.html';
    $scope.templateSearchPrefix = '/js/template/Search/Result/';
    $scope.searchTemplates      = {
        'scenario' : $scope.templateSearchPrefix + 'scenario.html',
        'test'     : $scope.templateSearchPrefix + 'test.html',
        'measure'  : $scope.templateSearchPrefix + 'measure.html',
        'call'     : $scope.templateSearchPrefix + 'call.html'
    };
    $scope.validation   = {};
    $scope.template     = $scope.$parent.template;
    $scope.items        = [];
    $scope.previewItems = [];
    $scope.selected     = [];
    $scope.target;

    $scope.$watch('template.source.target', function() {
        $scope.target = $scope.template.source.target;

        if ($scope.template.source.hasOwnProperty('items')) {
            $scope.selected = $scope.template.source.items;
        }
    });

    $scope._fillTemplateSource = function() {
        var type = $scope.template.source.type;

        switch (type) {
            case 'template':
                $scope.template.source.items  = [];
                break;
            case 'all':
                $scope.template.source.items  = _.pluck($scope.items.length > 0 ? $scope.items : $scope.previewItems, 'id');
                break;
            case 'manual':
                $scope.template.source.items  = $scope.selected;
                break;
        };
    };

    var hideResult = function(event) {
        $scope.template.source.template = event.targetScope.getTemplate ? event.targetScope.getTemplate() : $scope.template.source.template;

        if (event.targetScope.template.hasOwnProperty('target')) {
            $scope.template.source.target = event.targetScope.template.target || null;
        }

        $scope.items              = [];
        $scope.searchErrorMessage = null;
        $scope.isValidSource($scope.validation);
    };

    $scope.$watch('template.source.type', $scope._fillTemplateSource);

    $scope.$on('search-done', function(event, result) {
        hideResult(event);

        $scope.template.source.target = result.target;
        $scope.items                  = result.result.result;
        $scope.totalItems             = $scope.items.length;;
        $scope._fillTemplateSource();
    });

    $scope.$on('search-preview', function(event, result) {
        hideResult(event);
        $scope.template.source.target = result.target;
        $scope.previewItems           = result.result.result;
        $scope._fillTemplateSource();
    });

    $scope.$on('search-group-add', hideResult);
    $scope.$on('search-group-drop', hideResult);
    $scope.$on('search-filter-drop', hideResult);
    $scope.$on('search-filter-add', hideResult);
    $scope.$on('search-template-reset', hideResult);
    $scope.$on('search-template-select', hideResult);

    $scope.$on('search-error', function(event) {
        $scope.searchErrorMessage = 'main.server.error';
    });

    $scope.select = function(item) {
        if ($scope.template.source.type === 'manual') {
            if (_.indexOf($scope.selected, item.id) !== -1) {
                $scope.selected = _.without($scope.selected, item.id);
            } else {
                $scope.selected.push(item.id);
            }

            $scope._fillTemplateSource();
        }
    };

    $scope.selectPage = function(page, pageSize) {
        var i, forSelect = [];
        for(i = (page - 1)* pageSize; i < page * pageSize && i < $scope.items.length; i++) {
            forSelect.push($scope.items[i].id);
        }

        if (_.difference(forSelect, $scope.selected).length === 0) {
            $scope.selected = _.difference($scope.selected, forSelect);
        } else {
            $scope.selected = _.union($scope.selected, forSelect);
        }

        $scope._fillTemplateSource();
    };

    $scope.cleanSelect = function() {
        $scope.selected = [];
        $scope._fillTemplateSource();
    };

    $scope.isValidSource = function(validation) {
        $scope.validation = validation;

        delete(validation.source);
        if (_.isEmpty($scope.template.source.template)) {
            validation.source = 'error';
        }

        return validation;
    };

    $scope.$parent.isValidSource = $scope.isValidSource;
}

function StatisticTemplateViews($scope, StatisticService) {
    $scope.htmlTemplate        = '/js/template/Statistics/Template/create/views.html';
    $scope.lineTemplatesPrefix = '/js/template/Statistics/Template/create/line/';

    $scope.template   = $scope.$parent.template;
    $scope.validation = {};

    $scope.entitiesMenu = {};
    $scope.linesMenu    = {};
    $scope.lineTypeMap  = {};
    $scope.enumMap      = {};
    $scope.graphTypes   = {};

    $scope.menus = {};

    var initMenus = function() {
        var entity, line;

        if ($scope.template.views.length > 0 && !_.isEmpty($scope.linesMenu)) {
            _.each($scope.template.views, function(view, pos) {
                entity = view.target;
                $scope.menus[pos] = $scope.linesMenu[entity];
                if (view.hasOwnProperty('lines') && view.lines.length > 0) {
                    line = _.first(view.lines);
                    $scope.menus[pos] = _.filter($scope.menus[pos], function(item) {
                        return _.where(item.submenu, {type : line.type}).length > 0;
                    });
                }
            });
        } else {
            $scope.menus = {};
        }

        $scope.isValidViews($scope.validation);
    };

    $scope.$on('statistic-template-loaded', initMenus);

    StatisticService.getViewsConfig(
        function(response) {
            $scope.entitiesMenu = response.entitiesMenu;
            $scope.linesMenu    = response.linesMenu;
            $scope.lineTypeMap  = response.lineTypeMap;
            $scope.enumMap      = response.enumMap;
            $scope.graphTypes   = response.graphTypes;
            initMenus();
        }
    );

    $scope.$watch('template.source.target', function(target) {
        if (_.isEmpty(target)) {
            $scope.template.views = [];
            initMenus();
        }
    });

    $scope.$on('menu-selected-view', function(event, entity) {
        $scope.addView(entity.value);
    });

    $scope.$on('menu-selected-line', function(event, line, view) {
        $scope.addLine(line, view);
    });

    $scope.addView = function(entity) {
        var view = {
            target : entity,
            type   : _.first($scope.graphTypes).value,
            lines  : []
        };

        $scope.template.views.push(view);
        initMenus();
    };

    $scope.dropView = function(view) {
        $scope.template.views = _.without($scope.template.views, view);
        initMenus();
    };

    $scope.addLine = function(line, view) {
        if (line.hasOwnProperty('function')) {
            var line = {
                type     : line.type,
                function : line.function,
                value    : null
            };

            switch ($scope.lineTypeMap[line.type]) {
                case 'reg_exp':
                    line.value = '';
                    break;
                case 'range_int':
                    line.value = {
                        from : '',
                        to   : ''
                    };
                    break;
                case 'enum':
                    line.value = _.first($scope.enumMap[line.type]).value;
            }

            view.lines.push(line);
            initMenus();
        }
    };

    $scope.dropLine = function(line, view) {
        view.lines = _.without(view.lines, line);
        initMenus();
    };

    $scope.isValidViews = function(validation) {
        var error = {};
        $scope.validation = validation;

        delete(validation.views);
        validation.views = {};

        if ($scope.template.views.length === 0) {
            validation.views.empty = 'error';
        } else {
            _.each($scope.template.views, function(view, index) {
                error = {};
                $scope.isValidGroup(error, view);

                if (!_.isEmpty(error)) {
                    validation.views[index] = error;
                }
            });
        }

        if (_.isEmpty(validation.views)) {
            delete(validation.views);
        }

        return validation;
    };

    $scope.isValidGroup = function(validation, group) {
        var error;
        if (group.lines.length === 0) {
            validation.empty = 'error';
        } else {
            _.each(group.lines, function(line, index) {
                error = {};
                $scope.isValidLine(error, line);

                if (!_.isEmpty(error)) {
                    validation[index] = error;
                }
            });
        }

        return validation;
    };

    $scope.isValidLine = function(error, line) {
        var isValid = true;

        switch ($scope.lineTypeMap[line.type]) {
            case 'reg_exp':
                isValid = line.value !== '';
                break;
            case 'range_int':
                isValid = line.value.from !== '' || line.value.to !== '';
                break;
        }

        if (isValid === false) {
            error.required = 'error';
        }

        return error;
    };

    $scope.$parent.isValidViews = $scope.isValidViews;
}

function StatisticSetList($scope, StatisticService, $modal) {
    $scope.initList('name');
    $scope.sets = [];
    $scope.deleteSetId;

    StatisticService.getSets(
        function (response) {
            $scope.sets = response;
        }
    );

    $scope.deleteSet = function(id) {
        StatisticService.deleteSet(
            id,
            function() {
                $scope.sets = _.without($scope.sets, _.findWhere($scope.sets, {id : id}));
            }
        );
    };

    $scope.deleteSetDialog = function(id) {
        $scope.deleteSetId = id;
        $modal({
            template : 'statistic-set-modal-delete.html',
            persist  : true,
            show     : true,
            scope    : $scope
        });
    };
}

function StatisticSetCreate($scope, StatisticService, $routeParams, $modal) {
    $scope.initList('name');
    $scope.validation = {};
    $scope.templates  = [];
    $scope.set        = {
        name        : null,
        description : null,
        templates   : []
    };

    $scope.dialog = $modal({
            template : '/js/template/Statistics/Set/create/templateDialog.html',
            persist  : true,
            show     : false,
            scope    : $scope
        });

    StatisticService.getTemplates(
        function (templates) {
            $scope.templates = templates;

            // It means that it is edit UC.
            if ($routeParams.hasOwnProperty('id')) {
                StatisticService.getSet(
                    $routeParams.id,
                    false,
                    function(set) {
                        $scope.set = set;
                    }
                );
            }
        }
    );

    $scope.save = function() {
        if ($scope.isValid()) {
            StatisticService.saveSet(
                $scope.set,
                function() {
                    $scope.back('/statistic/set');
                }
            );
        }
    };

    $scope.isValid = function() {
        $scope.validation = {};

        if (_.isEmpty($scope.set.name)) {
            $scope.validation.name = 'error';
        }

        if (_.isEmpty($scope.set.templates)) {
            $scope.validation.templates = 'error';
        }

        return _.values($scope.validation).length === 0;
    };

    $scope.openTemplateDialog = function() {
        $scope.dialog.show();
    };

    $scope.addTemplate = function(templateId) {
        if (_.indexOf($scope.set.templates, templateId) === -1) {
            $scope.set.templates.push(templateId);
        } else {
            $scope.set.templates = _.without($scope.set.templates, templateId);
        }

        $scope.isValid();
    };
}

function StatisticSetDetail($scope, StatisticService, $routeParams, $modal, $timeout) {
    $scope.initList('name');
    $scope.deleteRunId;
    $scope.templates  = [];
    $scope.set        = {
        name        : null,
        description : null,
        templates   : [],
        runs        : []
    };

    $scope.runTimer = {};
    $scope.refreshInterval = 1000;

    $scope.getSet = function() {
        $scope.mask();
        StatisticService.synchronize({
            templates : StatisticService.getTemplates(
                function (templates) {
                    $scope.templates = templates;
                }
            ),
            set : StatisticService.getSet(
                $routeParams.id,
                true,
                function(set) {
                    $scope.set = set;
                    $scope._processRuns();
                }
            )
        }).on('synced', function() {
            $scope.templates = _.filter(
                $scope.templates,
                function(template) {
                    return _.indexOf($scope.set.templates, template.id) > -1;
                }
            );

            $scope.unmask();
        });
    };

    $scope._loadRuns = function(gotoLastPage) {
        StatisticService.getSet(
            $routeParams.id,
            true,
            function(set) {
                $scope.set = set;
                $scope._processRuns();
            }
        );

        if(gotoLastPage) {
            $scope.currentPage = parseInt($scope.set.runs.length / $scope.pageSize) + 1;
        }
    };

    $scope._processRuns = function() {
        _.each($scope.set.runs, function(run) {
            $scope._unsetWatcher(run.id);
            if (run.state !== 'done' && run.state !== 'error' ) {
                $scope._setWatcher(run.id);
            }
        }, this);
    } ;

    $scope._unsetWatcher = function(id) {
        if ($scope.runTimer.hasOwnProperty(id)) {
            $timeout.cancel($scope.runTimer[id]);
            delete($scope.runTimer[id]);
        }
    };

    $scope._setWatcher = function(id) {
        $scope.unmask().blockLoad(true);
        $scope.runTimer[id] = $timeout(function() {
            StatisticService.getRun(
                id,
                false,
                false,
                function(response) {
                    var key = _.indexOf($scope.set.runs, _.findWhere($scope.set.runs, {id : id}));
                    if (key !== -1) {
                        $scope.set.runs[key] = response;
                        $scope._unsetWatcher(id);

                        if (response.state !== 'done' && response.state !== 'error') {
                            $scope._setWatcher(id);
                        }
                    }
                }
            );
        }, $scope.refreshInterval);
    };

    $scope.startRun = function(id) {
        StatisticService.startRun(
            id,
            function() {
                $scope._loadRuns(true);
            },
            $scope.getSet
        );
    };

    $scope.deleteRun = function(id) {
        StatisticService.deleteRun(
            id,
            function() {
                $scope.set.runs = _.without($scope.set.runs, _.findWhere($scope.set.runs, {id : id}));
            }
        );
    };

    $scope.deleteRunDialog = function(id) {
        $scope.deleteRunId = id;
        $modal({
            template : 'statistic-set-run-modal-delete.html',
            persist  : true,
            show     : true,
            scope    : $scope
        });
    };

    $scope.$on('$locationChangeSuccess', function() {
        $scope.blockLoad(false);

        for(var id in $scope.runTimer) {
            if ($scope.runTimer.hasOwnProperty(id)) {
                $scope._unsetWatcher(id);
            }
        }
    });

    $scope.getSet();
}

function StatisticRunDetail($scope, StatisticService, $routeParams) {
    $scope.runId     = $routeParams.id;
    $scope.run       = {
        templates: []
    };

    $scope.EMPTY   = 'empty';
    $scope.INVALID = 'invalid';


    StatisticService.getRun(
        $scope.runId,
        true,
        true,
        function(response) {
            $scope.run = $scope._prepareTemplates(response);
        }
    );

    $scope.$on('translate:module:done', function(event, module) {
        if (module === 'statistic' && !_.isEmpty($scope.run.templates)) {
            var run = $scope.run;

            $scope.run = null;
            $scope.run = $scope._prepareTemplates(run);
        }
    });

    $scope._prepareTemplates = function(run) {
        var data;

        _.each(run.templates, function(template, templateKey) {
            _.each(template.views, function(view, viewKey) {
                data = $scope._processChartData(view, run.data);
                if (data === $scope.EMPTY) {
                    delete(template.views[viewKey]);
                } else {
                    view.chartData = data;
                }
            }, this);

            if (_.isEmpty(template.views)) {
                delete(run.templates[templateKey]);
            } else {
                template.views = _.values(template.views);
            }
        }, this);

        run.templates = _.values(run.templates);

        return run;
    };

    $scope._processChartData = function(view, data) {
        var processed;

        if (view.type === 'line') {
            processed = $scope._processChartDataLine(view, data);
        } else {
            processed = $scope._processChartDataTime(view, data);
        }

        return processed;
    };

    $scope._processChartDataLine = function(view, data) {
        var value,
            validate = $scope.EMPTY,
            chart    = {
                type : "PieChart",
                data : [
                    ['Line', 'value']
                ],
                options : {
                    displayExactValues: true,
                    is3D: true,
                    sliceVisibilityThreshold: 0,
                    legend: { position: 'bottom' },
                    chartArea: {top: 0, bottom: 0, height: '90%', width: '90%'}
                },
                view: {}
            };

        _.each(view.lines, function(line, key) {
            value = _.findWhere(data, {statisticViewLineId : line.id});

            if (value !== undefined) {
                chart.data.push(
                    [$scope._getLineName(line, key + 1), value.value]
                );

                validate = (validate === null || value.value !== 0) ? null : $scope.INVALID;
            }
        }, this);

        if (validate) {
            chart = validate;
        }

        return chart;
    };

    $scope._processChartDataTime = function(view, data) {
        var
            value,
            times    = [],
            lines    = view.lines,
            viewData = {},
            timeData = [],
            validate = $scope.EMPTY,
            chart    = {
                type: 'LineChart',
                data : {
                    cols : [{
                        label: 'Date',
                        type: 'string'
                    }],
                    rows : []
                },
                options: {
                    displayExactValues: true,
                    curveType: 'none',
                    legend: { position: 'bottom' },
                    chartArea: {top: 10, bottom: 10, height: '70%', width: '80%'},
                    hAxis : {
                        textPosition: 'none'
                    }
                },
                view: {}
            };

        _.each(lines, function(line, key) {
            chart.data.cols.push({
                label: $scope._getLineName(line, key + 1),
                type: 'number'
            });

            viewData[line.id] = _.where(data, {statisticViewLineId: line.id});
            times             = _.unique(
                _.union(times, _.pluck(viewData[line.id], 'time'))
            );
        }, this);

        times = _.sortBy(times);

        _.each(times, function(time) {
            timeData = [{
                v: $scope.$filter('date')(time, 'd.M.yyyy H:mm:ss')
            }];

            _.each(lines, function(line) {
                value   = _.findWhere(viewData[line.id], {time: time});
                validate = (validate === null || (value !== undefined && value.value !== 0)) ? null : $scope.INVALID;

                timeData.push({
                    v: value === undefined ? undefined : value.value
                });
            }, this);

            chart.data.rows.push({c: timeData});
        }, this);

        if (validate) {
            chart = validate;
        }

        return chart;
    };

    $scope.selectChartLine = function(view, chart, line) {
        if (view.type === 'line') {
            $scope.selectPieChartLine(view, chart, line);
        } else {
            $scope.selectLineChartLine(view, chart, line);
        }

        return this;
    };

    $scope.selectPieChartLine = function(view, chart, line) {
        var i,
            index    = _.indexOf(view.lines, line) + 1,
            newChart = $scope._processChartDataLine(view, $scope.run.data),
            lineData = newChart.data[index];

        index = -1;
        for(i = 0; i < chart.data.length; i++) {
            if (_.isEqual(chart.data[i], lineData)) {
                index = i;
            }
        }

        if (index === -1) {
            for(i = 0; i < newChart.data.length; i++) {
                if (!_.isEqual(newChart.data[i], chart.data[i])) {
                    break;
                }
            }

            chart.data.splice(i, 0, lineData);
            line.selected = true;
        } else if (chart.data.length > 2) {
            chart.data.splice(index, 1);
            line.selected = false;
        }

        return this;
    };

    $scope.selectLineChartLine = function(view, chart, line) {
        var actual  = chart.view.columns || [],
            all     = _.union([0], _.map(_.keys(view.lines), function(key) { return parseInt(key) + 1;})),
            index   = _.indexOf(view.lines, line) + 1,
            visible = _.isEmpty(actual) ? all : _.intersection(actual, all);

        if (_.indexOf(visible, index) === -1) {
            line.selected = true;
            visible.push(index);
            visible = _.sortBy(visible);
        } else {
            visible = _.without(visible, index);
            line.selected = visible.length > 1 ? false : true;
        }

        if (visible.length > 1) {
            chart.view = {columns: visible};
        }

        return this;
    };

    $scope._getLineName = function(line, order) {
        var name,
            value = line.value;

        if (value.hasOwnProperty('to') && value.hasOwnProperty('from')) {
            value = (value.from ? value.from : '-Inf') + ' - ' + (value.to ? value.to : 'Inf');
        }

        name = order + '. '
            + $scope._('statistic.view.line.type.' + line.type)
            + ' (' + $scope._('statistic.view.line.function.' + line.function).toLowerCase() + '): '
            + '"' + value + '"';
        return name;
    };
}
