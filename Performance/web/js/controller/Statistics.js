
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

function StatisticDataList($scope) {

}