'use strict';

/* App Module */
var global = {};
var countLoad = 0;
var blockLoad = false;
var timerLoad;
var perfModule = angular.module(
    'Perf',
    ['ui.bootstrap', 'mgcrea.ngStrap', 'ngRoute', 'googlechart']
).config(
    function($interpolateProvider) {
        $interpolateProvider.startSymbol('[[').endSymbol(']]');
    }
).config(function ($httpProvider) {
    $httpProvider.interceptors.push(function ($q) {
        var responseFunction = function (response) {
            countLoad = countLoad === 0 ? 0 : countLoad - 1;
            if (countLoad === 0 && blockLoad === false) {
                clearInterval(timerLoad);
                timerLoad = setInterval(function() {

                    if (countLoad === 0 && blockLoad === false) {
                        $('#loader').fadeOut();
                        clearInterval(timerLoad);
                    }
                }, 300);
            }

            return response;
        };

        return {
            'request': function (config) {
                if (!global.templateCache.get(config.url) && config.url.search(base) !== 0) {
                    config.url = base + '/' + config.url.replace(/^\/+/, '');
                    if (countLoad === 0 && blockLoad === false) {
                        $('#loader').fadeIn();
                    }

                    countLoad++;
                }

                return config;
            },
            'response':      responseFunction,
            'responseError': function(response) {
                response = responseFunction(response);

                return $q.reject(response);
            }
        };
    });
});

perfModule.run(
    ['$rootScope', '$templateCache', 'translate', 'validator', 'myCache', '$window', '$location', '$filter',
    function (rootScope, templateCache, translate, validator, myCache, $window, location, $filter) {
        rootScope.$filter = $filter;

        rootScope.$once = function(name, listener) {
            var firedOnce = false;

            return rootScope.$on(name, function() {
                if (firedOnce) {
                    listener.apply(this, arguments);
                }

                firedOnce = true;
            });
        };

        global.templateCache = templateCache;
        rootScope.translate  = translate;
        rootScope.__         = _;
        rootScope._          = function (key, placeholders) {
            return translate._(key, placeholders);
        };

        rootScope.cache = myCache;
        rootScope.cacheObject = function(name, object) {
            if (_.isObject(object) === false) {
                if (this.hasOwnProperty(name) && _.isObject(this[name]) === true) {
                    object = this[name];
                } else {
                    throw 'Parameter "object" must be an object.';
                }
            }

            var cacheName = (this.cachePrefix || 'cache') + '.' + name;

            if (myCache.get(cacheName) !== undefined) {
                this[name] = myCache.get(cacheName);
            } else {
                this[name] = object;
            }

            myCache.put(cacheName, this[name]);
        };

        rootScope.cleanCache = function(name) {
            var cacheName = (this.cachePrefix || 'cache') + '.' + name;

            if (myCache.get(cacheName) !== undefined) {
                myCache.remove(cacheName);
            }
        };

        rootScope.initList = function(predicate) {
            var self         = this;
            this.predicate   = predicate;
            this.reverse     = false;
            this.totalItems  = 0;
            this.currentPage = 1;
            this.pageSize    = 10;
            this.pageSizes   = [10, 20, 50, 100];

            this.refresh = function(input) {
                if (_.isArray(input)) {
                    self.totalItems = input.length;
                }

                return input;
            };
        };

        rootScope.selectSortClass = function(column, predicate, reverse) {
            var style = 'glyphicon glyphicon-minus';
            if (column === predicate) {
                style = 'glyphicon glyphicon-chevron-' + ((reverse === undefined || reverse === true) ? 'up' : 'down');
            }

            return style;
        };

        rootScope.indexOf = function(items, item) {
            return _.indexOf(items, item);
        };

        rootScope.mask = function() {
            blockLoad = true;
            countLoad++;
            $('#loader').fadeIn();

            return this;
        };

        rootScope.unmask = function() {
            countLoad = countLoad === 0 ? 0 : countLoad - 1;
            blockLoad = false;
            setTimeout(function() {
                if (countLoad === 0) {
                    $('#loader').fadeOut();
                }
            }, 100);

            return this;
        };

        rootScope.blockLoad = function(block) {
            blockLoad = !!block;

            return this;
        };

        rootScope.back = function(url) {
            if($window.history.length > 0) {
                $window.history.back();
            } else {
                location.path(url);
            }

            return this;
        };

        rootScope.validator = validator;
}]);
