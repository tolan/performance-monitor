angular.module('PM')
    .controller('SearchMainCtrl', SearchMainCtrl)
    .controller('SearchResultCtrl', SearchResultCtrl)
    .controller('SearchFiltersCtrl', SearchFiltersCtrl)
    .controller('SearchSourceCtrl', SearchSourceCtrl)
    .controller('SearchTemplateCtrl', SearchTemplateCtrl);

/**
 * Controller for manage connection between filters and result.
 *
 * @param $scope Scope
 *
 * @returns void
 */
function SearchMainCtrl($scope) {
    $scope.errorMessage = null;

    $scope.$on('search-done', function(event, result) {
        $scope.$broadcast('search-show-result', result);
        $scope.errorMessage = null;
    });

    $scope.$on('search-preview', function() {
        $scope.errorMessage = null;
    });

    var hideResult = function() {
        $scope.$broadcast('search-hide-result');
        $scope.errorMessage = null;
    };

    $scope.$on('search-group-add', hideResult);
    $scope.$on('search-group-drop', hideResult);
    $scope.$on('search-filter-drop', hideResult);
    $scope.$on('search-filter-add', hideResult);
    $scope.$on('search-template-reset', hideResult);
    $scope.$on('search-template-select', hideResult);

    $scope.$on('search-error', function(event) {
        $scope.errorMessage = 'main.server.error';
    });
}

/**
 * Controller for show list of entities from filters.
 *
 * @param $scope Scope
 *
 * @returns void
 */
function SearchResultCtrl($scope) {
    $scope.initList('id');
    $scope.templatePrefix = '/js/template/Search/Result/';
    $scope.template       = $scope.templatePrefix + 'main.html';
    $scope.templates      = {
        'scenario'      : $scope.templatePrefix + 'scenario.html',
        'test'          : $scope.templatePrefix + 'test.html',
        'measure'       : $scope.templatePrefix + 'measure.html',
        'call'          : $scope.templatePrefix + 'call.html',
        'statistic_set' : $scope.templatePrefix + 'statisticSet.html',
        'statistic_run' : $scope.templatePrefix + 'statisticRun.html'
    };

    $scope.target;
    $scope.items = [];

    $scope.$on('search-show-result', function(event, result) {
        $scope.target     = result.target;
        $scope.items      = result.result.result;
        $scope.totalItems = $scope.items.length;;
    });

    $scope.$on('search-hide-result', function(event) {
        $scope.target = null;
        $scope.items  = [];
    });
}

/**
 * Controller for manage search filters and finding entities.
 *
 * @param $scope   Scope
 * @param $http    Http provider
 * @param $timeout Timeout provider
 * @param $modal   Modal component
 * @param $attrs   Attributes provider
 *
 * @returns void
 */
function SearchFiltersCtrl($scope, $http, $timeout, $modal, $attrs) {
    $scope.usage          = ($attrs.usage || $scope.usage || 'search' );
    $scope.cache          = (($attrs.cache || $scope.cache) === undefined ? true : ($attrs.cache || $scope.cache)).toString() !== 'false';
    $scope.cachePrefix    = arguments.callee.name + $scope.usage;
    $scope.templatePrefix = '/js/template/Search/Filters/';
    $scope.templateSearch = $scope.templatePrefix + 'main.html';
    $scope.templates      = {
        'query'  : $scope.templatePrefix + 'query.html',
        'string' : $scope.templatePrefix + 'string.html',
        'date'   : $scope.templatePrefix + 'date.html',
        'enum'   : $scope.templatePrefix + 'enum.html',
        'int'    : $scope.templatePrefix + 'string.html',
        'float'  : $scope.templatePrefix + 'string.html'
    };
    $scope.isAllowedLogic = false;
    $scope.filterCache    = {};
    $scope.menu           = [];
    $scope.originalMenu   = {};
    $scope.timerInterval  = 800;
    $scope.timer;
    $scope.resultTotal;

    $scope.originalMenu[$scope.usage] = {};

    $scope.template = {};

    var initFunction = function() {
        $scope.resultTotal = undefined;
        $scope.initLogic();
    };

    var reset = function() {
        var index ;
        for (index in $scope.template) {
            if ($scope.template.hasOwnProperty(index)) {
                delete($scope.template[index]);
            }
        }

        $scope.template.groups = {};
        $scope.template.logic  = '';
        $scope.template.target = undefined;

        $scope.menu           = $scope.originalMenu[$scope.usage];
        $scope.showLogic      = false;
        $scope.isAllowedLogic = false;
    };

    var selectTemplate = function(template) {
        var index;
        template = angular.copy(template);

        reset();
        for (index in template) {
            if (template.hasOwnProperty(index)) {
                $scope.template[index] = template[index];
            }
        }

        $scope.template.groups = {};

        _.each(template.groups, function(group) {
            var newGroup = $scope.addGroup(group.identificator);
            _.extend(newGroup, group);
            newGroup.filters = [];

            _.each(group.filters, function(filter) {
                $scope.selectFilter({}, filter, newGroup);
            });
        });

        $scope.showLogic = false;
        if ($scope.template.logic !== template.logic) {
            $scope.showLogic = true;
        }

        $scope.template.logic = template.logic;
        $timeout(function() {
            $scope._sendTemplate(true);
        }, $scope.timerInterval);
    };

    reset();
    $scope.$on('search-group-add', initFunction);
    $scope.$on('search-group-drop', initFunction);
    $scope.$on('search-template-reset', reset);
    $scope.$on('search-template-select', function(event, template) {selectTemplate(template);});
    $scope.$on($scope.cachePrefix + '.template:loaded', function(event, template) {selectTemplate(template);});

    $scope.addGroup = function(key) {
        var group = {
            filters : []
        };

        if (key === undefined) {
            key = (parseInt(_.last(_.keys($scope.template.groups)), 10) || 0) + 1;
        }

        $scope.template.groups[key] = group;
        $scope.$emit('search-group-add', $scope.template);

        return group;
    };

    $scope.dropGroup = function(group) {
        var values  = _.values($scope.template.groups),
            i       = 1,
            filters = 0;

        $scope.template.groups = {};

        _.each(values, function(value) {
            if (value !== group) {
                $scope.template.groups[i] = value;
                filters += value.filters.length;
                i++;
            }
        });

        if (filters === 0) {
            $scope.menu            = $scope.originalMenu[$scope.usage];
            $scope.showLogic       = false;
            $scope.isAllowedLogic  = false;
            $scope.template.target = undefined;
        }

        $scope.$emit('search-group-drop', $scope.template);
    };

    $scope.selectFilter = function(event, item, group) {
        if (item.hasOwnProperty('target')) {
            $scope.template.target = item.target;
            $scope.menu            = $scope.originalMenu[$scope.usage][item.target].submenu;

            if ($scope.filterCache.hasOwnProperty(item.target) && $scope.filterCache[item.target].hasOwnProperty(item.filter)) {
                $scope._createFilter(group, angular.copy($scope.filterCache[item.target][item.filter]), item);
            } else {
                $http.get('search/filter/options/' + item.target + '/' + item.filter).success(function(filter) {
                    if ($scope.filterCache.hasOwnProperty(item.target) === false) {
                        $scope.filterCache[item.target] = {};
                    }

                    $scope.filterCache[item.target][item.filter] = filter;
                    $scope._createFilter(group, angular.copy($scope.filterCache[item.target][item.filter]), item);
                });
            }
        }
    };

    $scope._createFilter = function(group, filter, item) {
        _.extend(filter, item);
        delete(filter.$$hashKey);
        filter.operator = filter.hasOwnProperty('operators') ? _.first(filter.operators).value : null;
        filter.operator = item.hasOwnProperty('operator')    ? item.operator : filter.operator;
        filter.value    = filter.hasOwnProperty('values')    ? _.first(filter.values).value    : null;
        filter.value    = item.hasOwnProperty('value')       ? item.value : filter.value;

        $scope.isAllowedLogic = filter.isAllowedLogic;

        group.filters.push(filter);
        $scope.$emit('search-filter-add', $scope.template);
        if (item.hasOwnProperty('value') && !_.isEmpty(item.value)) {
            $scope.send();
        }
    };

    $scope.dropFilter = function(filter, group) {
        var countFilters = 0;

        group.filters = _.without(group.filters, filter);

        _.each($scope.template.groups, function(group) {
            countFilters += group.filters.length;
        });

        if (countFilters === 0) {
            $scope.template.target = undefined;
            $scope.menu            = $scope.originalMenu[$scope.usage];
        } else {
            $scope.send();
        }

        $scope.$emit('search-filter-drop', $scope.template);
    };

    $scope.initLogic = function() {
        var keys = _.keys($scope.template.groups),
            logic = '';

        if (keys.length > 0) {
            logic = keys.join(' OR ');
        }

        $scope.template.logic = logic;
    };

    $scope.send = function() {
        $timeout.cancel($scope.timer);
        $scope.timer = $timeout(function() {
            $scope._sendTemplate(false);
        }, $scope.timerInterval);
    };

    $scope.sendAll = function() {
        $timeout.cancel($scope.timer);
        $scope._sendTemplate(true);
    };

    $scope._sendTemplate = function (show) {
        var request = {
            template : $scope.template
        };

        if ($scope.isValidTemplate()) {
            $timeout.cancel($scope.timer);
            $http.post('search/find/entity/' + $scope.template.target, request).success(function(response) {
                $scope.resultTotal = response.result.result.length;

                if (show) {
                    $scope.$emit('search-done', response);
                } else {
                    $scope.$emit('search-preview', response);
                }
            }).error(function() {
                $scope.$emit('search-error');
            });
        } else {
            $scope.resultTotal = undefined;
        }
    };

    $scope.isValidTemplate = function() {
        var isValid = true;

        if ($scope.template.hasOwnProperty('isValid')) {
            delete($scope.template.isValid);
        }

        if (_.values($scope.template.groups).length === 0) {
            $scope.template.isValid = false;
            isValid                 = false;
        }

        _.each($scope.template.groups, function(group) {
            if ($scope.isValidGroup(group) === false) {
                isValid = false;
            }
        });

        return isValid;
    };

    $scope.isValidGroup = function(group) {
        var isValid = true;

        if (group.filters.length > 0) {
            _.each(group.filters, function(filter) {
                if ($scope.isValidFilter(filter) === false) {
                    isValid = false;
                }
            });
        } else {
            isValid = false;
        }

        if (group.hasOwnProperty('isValid')) {
            delete(group.isValid);
        }

        if (isValid === false) {
            group.isValid = isValid;
        }

        return isValid;
    };

    $scope.isValidFilter = function(filter) {
        var isValid = true;

        switch (filter.type) {
            case 'query':
            case 'string':
                isValid = !_.isEmpty(filter.value);
                break;
            case 'int':
            case 'float':
                isValid = _.isFinite(filter.value);
                break;
        }

        if (filter.hasOwnProperty('isValid')) {
            delete(filter.isValid);
        }

        if (isValid === false) {
            filter.isValid = isValid;
        }

        return isValid;
    };

    $scope.openTemplateDialog = function() {
        $modal({
            template : '/js/template/Search/Template/List.html',
            show     : true,
            scope    : $scope
        });
    };

    $scope.getTemplate = function() {
        var template = angular.copy($scope.template);

        if ($scope.isValidTemplate()) {
            _.each(template.groups, function(group, key) {
                _.each(group.filters, function(filter) {
                    delete(filter.name);
                    delete(filter.type);
                    delete(filter.isValid);
                    delete(filter.isAllowedLogic);
                });

                group.identificator = key;
                group.target        = template.target;

                delete(group.isValid);
                delete(group.operators);
                delete(group.values);
            });

            template.groups = _.values(template.groups);
            template.usage  = $scope.usage;

            delete(template.isValid);
        } else {
            template = false;
        }

        return template;
    };

    $scope.setUsage = function(usage) {
        $scope.usage = usage;
    };

    $scope.$on('menu-selected-item', $scope.selectFilter);

    $scope.cacheObject('originalMenu');

    if ($scope.cache === true) {
        $scope.cacheObject('menu');
        $scope.cacheObject('filterCache');
        $scope.cacheObject('template');
    }

    if (_.isEmpty($scope.originalMenu[$scope.usage])) {
        $http.get('search/filter/menu/' + $scope.usage).success(function(menu) {
            _.each(menu, function(item, key) {
                $scope.originalMenu[$scope.usage][key] = item;
            });

            $scope.menu = menu;

            if ($scope.template.target !== undefined) {
                $scope.menu = $scope.originalMenu[$scope.usage][$scope.template.target].submenu;
            }

            if ($scope.$parent.hasOwnProperty('template') && $scope.$parent.template.hasOwnProperty('groups')) {
                var keys = _.keys($scope.$parent.template.groups);

                _.each(keys, function(key) {
                    var group    = $scope.$parent.template.groups[key],
                        newGroup = $scope.addGroup(key);

                    _.each(group.filters, function(filter) {
                        $scope.selectFilter({}, filter, newGroup);
                    });
                });
            }

            $scope.$emit('search-ready');
        });
    } else {
        $scope.$emit('search-ready');
    }
}

/**
 * Controller for manage search source.
 *
 * @param $scope   Scope
 * @param $attrs   Attributes provider
 *
 * @returns void
 */
function SearchSourceCtrl($scope, $attrs) {
    $scope.usage  = ($attrs.usage || 'search' );
    $scope.cache  = ($attrs.cache || true) !== 'false';
    $scope.source = ($scope.$parent.searchSource || {
        template : {},
        target : null
    });
    $scope.target = null;
    $scope.$parent.isValidSource = $scope.isValidSource;

    $scope.initList('id');
    $scope.htmlTemplate         = '/js/template/Search/source.html';
    $scope.templateSearchPrefix = '/js/template/Search/Result/';
    $scope.searchTemplates      = {
        'scenario'      : $scope.templateSearchPrefix + 'scenario.html',
        'test'          : $scope.templateSearchPrefix + 'test.html',
        'measure'       : $scope.templateSearchPrefix + 'measure.html',
        'call'          : $scope.templateSearchPrefix + 'call.html',
        'statistic_set' : $scope.templateSearchPrefix + 'statistic_set.html',
        'statistic_run' : $scope.templateSearchPrefix + 'statistic_run.html'
    };
    $scope.validation   = {};
    $scope.items        = [];
    $scope.previewItems = [];
    $scope.selected     = [];

    if ($scope.source.hasOwnProperty('items')) {
        $scope.selected = $scope.source.items;
    }

    $scope._fillTemplateSource = function() {
        var type = $scope.source.type;

        switch (type) {
            case 'template':
                $scope.source.items = [];
                break;
            case 'all':
                $scope.source.items = _.pluck($scope.items.length > 0 ? $scope.items : $scope.previewItems, 'id');
                break;
            case 'manual':
                $scope.source.items = $scope.selected;
                break;
        };
    };

    var hideResult = function(event) {
        $scope.source.template = event.targetScope.getTemplate ? event.targetScope.getTemplate() : $scope.source.template;

        if (event.targetScope.template && event.targetScope.template.hasOwnProperty('target')) {
            $scope.source.target = event.targetScope.template.target || null;
        }

        $scope.items              = [];
        $scope.searchErrorMessage = null;
        $scope.isValidSource($scope.validation);
    };

    $scope.$watch('source.type', $scope._fillTemplateSource);
    $scope.$watch('source.target', function() { $scope.target = $scope.source.target; });

    $scope.$on('search-done', function(event, result) {
        hideResult(event);

        $scope.source.target = result.target;
        $scope.items         = result.result.result;
        $scope.totalItems    = $scope.items.length;;
        $scope._fillTemplateSource();
    });

    $scope.$on('search-preview', function(event, result) {
        hideResult(event);
        $scope.source.target = result.target;
        $scope.previewItems  = result.result.result;
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

    $scope.selectItem = function(item) {
        if ($scope.source.type === 'manual') {
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
        for(i = (page - 1) * pageSize; i < page * pageSize && i < $scope.items.length; i++) {
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
        if (_.isEmpty($scope.source.template)) {
            validation.source = 'error';
        }

        return validation;
    };
}

/**
 * Controller for manage search templates.
 *
 * @param $scope   Scope
 * @param $http    Http provider
 * @param $modal   Modal component
 *
 * @returns void
 */
function SearchTemplateCtrl($scope, $http, $modal) {
    $scope.initList('id');
    $scope.cachePrefix = arguments.callee.name;
    $scope.templates   = [];

    $scope.cacheObject('templates');

    var emitError = function() {
        $scope.$hide();
        $scope.$emit('search-error');
    };

    var loadTemplates = function() {
        $http.get('search/template/find/' + $scope.usage).success(function(templates) {
            $scope.templates.splice(0, $scope.templates.length);

            _.each(templates, function(template) {
                $scope.templates.push(template);
            });
        }).error(emitError);
    };

    if ($scope.templates.length === 0) {
        loadTemplates();
    } else {
        var isSame = true;
        for (var i = 0; i < $scope.templates.length && isSame; i++) {
            isSame = $scope.templates[i].usage === $scope.usage;
        }

        if (isSame === false) {
            $scope.templates.splice(0, $scope.templates.length);
            loadTemplates();
        }
    }

    $scope.save = function() {
        $scope.template = $scope._getTemplate();
        $scope._openSaveAsDialog();
    };

    $scope.saveAs = function() {
        $scope.template = $scope._getTemplate();

        if ($scope.template.hasOwnProperty('id')) {
            delete($scope.template.id);
        }

        $scope._openSaveAsDialog();
    };

    $scope._openSaveAsDialog = function() {
        $scope.$hide();

        if ($scope.template !== false) {
            $scope.hideSaveAs = function () {
                $scope.modalSaveAs.hide();
                $scope.$show();
            };

            $scope.modalSaveAs = $modal({
                template    : 'search-template-modal-saveAs.html',
                show        : true,
                scope       : $scope,
                prefixEvent : 'modal-saveAs'
            });
        }
    };

    $scope._send = function(template) {
        if (template !== false) {
            if (template.hasOwnProperty('id')) {
                $http.put('/search/template/update/' + template.id, template).success(function() {
                    $scope._openSuccessMessage();
                    $scope.cleanCache('templates');
                }).error(emitError);
            } else {
                $http.post('/search/template/create', template).success(function() {
                    $scope._openSuccessMessage();
                    $scope.cleanCache('templates');
                }).error(emitError);
            }
        }
    };

    $scope._openSuccessMessage = function() {
        $scope.$hide();
        $scope.hideSuccess = function () {
            $scope.modalSuccess.hide();
            $scope.$show();
        };

        $scope.modalSuccess = $modal({
            template    : 'search-template-modal-success.html',
            show        : true,
            scope       : $scope,
            prefixEvent : 'modal-success'
        });
    };

    $scope.deleteTemplateDialog = function(template) {
        $scope.$hide();
        $scope.hideDelete = function () {
            $scope.modalDelete.hide();
            $scope.$show();
        };

        $scope.deleteTemplate = function() {
            $http.delete('/search/template/delete/' + template.id, template).success(function() {
                $scope._openSuccessMessage();
                $scope.cleanCache('templates');
            }).error(emitError);
        };

        $scope.modalDelete = $modal({
            template    : 'search-template-modal-delete.html',
            show        : true,
            scope       : $scope,
            prefixEvent : 'modal-delete'
        });
    };

    $scope.clean = function() {
        delete($scope.template);
        $scope.$hide();
        $scope.$emit('search-template-reset');
    };

    $scope._getTemplate = function() {
        return $scope.$parent.getTemplate();
    };

    $scope.select = function(template) {
        $http.get('search/template/get/' + template.id).success(function(template) {
            $scope.$emit('search-template-select', template);
            $scope.$hide();
        }).error(emitError);
    };
}