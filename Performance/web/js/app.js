'use strict';

/* App Module */
var countLoad = 0;
var perfModule = angular.module(
    'Perf',
    ['ui.bootstrap', '$strap.directives', 'ngRoute']
).config(
    function($interpolateProvider) {
        $interpolateProvider.startSymbol('[[').endSymbol(']]');
    }
).config(function ($httpProvider) {
    $httpProvider.responseInterceptors.push('myHttpInterceptor');
    var spinnerFunction = function (data, headersGetter) {
        if (countLoad === 0) {
            $('#loader').show();
        }

        countLoad++;
        return data;
    };

    $httpProvider.defaults.transformRequest.push(spinnerFunction);
}).factory('myHttpInterceptor', function ($q, $window) {
    return function (promise) {
        return promise.then(function (response) {
            countLoad--;
            if (countLoad === 0) {
                $('#loader').hide();
            }

            return response;
        }, function (response) {
            countLoad--;
            if (countLoad === 0) {
                $('#loader').hide();
            }

            return $q.reject(response);
        });
    };
});


perfModule.service(
    'validator',
    function() {
        this._selected   = {};
        this._validators = {
            required : function (value, test) {
                if (value === undefined || value.length === 0 && test === true) {
                    return 'main.validator.required';
                }

                return null;
            },

            minLength : function (value, test) {
                if (value === undefined || value.length < test) {
                    return 'main.validator.minLength';
                }

                return null;
            },

            maxLength : function (value, test) {
                if (value.length > test) {
                    return 'main.validator.maxLength';
                }

                return null;
            },

            password : function (value, test) {
                if (value === undefined || test === true && value.match(/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).+/) === null) {
                    // with special chars /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[/*-+&#@]).+/
                    return 'main.validator.password';
                } else if (angular.isString(test) && value.match(test) === null) {
                    return 'main.validator.password';
                }

                return null;
            },

            regex : function (value, test) {
                if (value === undefined || value.match(test) === null) {
                    return 'main.validator.regex';
                }

                return null;
            }
        };

        this.addValidator = function (type, validator) {
            this._validators[type] = validator;

            return this;
        };

        this.selectValidators = function (validators) {
            if (angular.isObject(validators)) {
                this._selected = validators;
            } else if (validators !== undefined && validators !== null) {
                throw 'Validators must be object.';
            }

            return this;
        };

        this.validate = function (value, validators) {
            this.selectValidators(validators);
            var message = null, test, type,
                result = {
                    message : null,
                    valid   : true
                };

            for(type in this._selected) {
                if (this._selected.hasOwnProperty(type)) {
                    test    = this._selected[type];
                    message = this._validators[type](value, test);

                    if (message !== null) {
                        result.message = message;
                        result.valid   = false;
                    }
                }
            }
            return result;
        };
    }
);

perfModule.service(
    'translate',
    function($rootScope, $http) {
        var self = this;
        this._lang = null;
        this._placeholders = {};
        this._table = {};
        this._loadedModules = [];

        this.switchLang = function(lang) {
            var i, modules = this._loadedModules;

            if (lang === this._lang) {
                return this;
            }

            this._lang = lang;
            this._loadedModules = [];
            //this._table = {};

            for(i in modules) {
                if (modules.hasOwnProperty(i)) {
                    this.loadModule(modules[i]);
                }
            }

            $rootScope.$broadcast('translate:switchLang');
            return this;
        }

        this.loadModule = function (module) {
            if (_.indexOf(this._loadedModules, module) === -1 && module.length) {
                this._loadedModules.push(module);
                $http.get('translate/module/' + module + (this._lang ? '/' + this._lang : '')).success(function(translate) {
                    self._lang = translate.lang;
                    self._table = _.extend(self._table, translate.translate);
                });
            }
        };

        this._ = function (key, placeholders) {
            var string = key;
            if (this._table.hasOwnProperty(key)) {
                string = this._table[key];
            } else {
                var module = key.substring(0, _.indexOf(key, '.'));
                this.loadModule(module);
            }

            string = this._replace(string, placeholders);
            string = this._replace(string, this._placeholders);

            return string;
        };

        this.setPlaceholders = function (placeholders) {
            this._placeholders = placeholders;

            return this;
        };

        this._replace = function(string, placeholders) {
            if (string === null || (angular.isString(string) && string.search('#') === -1) || angular.isObject(placeholders) === false) {
                return string;
            }

            var key, placeholder;
            for(key in placeholders) {
                if (placeholders.hasOwnProperty(key)) {
                    placeholder = placeholders[key];
                    string = string.replace(key, placeholder);
                }
            }

            return string;
        };

        this.loadModule('main');
    }
);

perfModule.run(
    ['$rootScope', 'translate', 'validator',
    function (rootScope, translate, validator) {
        rootScope.translate = translate;
        rootScope._         = function (key, placeholders) {
            return translate._(key, placeholders);
        };

        rootScope.selectSortClass = function(column, predicate, reverse) {
            var style = 'icon-minus';
            if (column === predicate) {
                style = 'icon-chevron-' + ((reverse === undefined || reverse === true) ? 'up' : 'down');
            }

            return style;
        };

        rootScope.indexOf = function(items, item) {
            return _.indexOf(items, item);
        };

        rootScope.validator = validator;
}]);

perfModule.filter('startFrom', function() {
    return function(input, start) {
        start = +start; //parse to int
        return _.rest(input, start);
    };
});

perfModule.filter('customFilter', function() {
    return function(input, filter) {
        return filter(input);
    };
});

perfModule.filter('round', function() {
    return function(input, precision) {
        precision = precision || 0;
        var exp = Math.pow(10, precision);

        return  Math.round(input*exp)/exp;
    };
});
