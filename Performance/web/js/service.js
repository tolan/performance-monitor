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

        this._lang          = null;
        this._placeholders  = {};
        this._table         = {};
        this._loadedModules = [];

        this.switchLang = function(lang) {
            var i,
                modules = this._loadedModules,
                doneFunction = _.after(modules.length, function() {
                    $rootScope.$broadcast('translate:switchLang:done');
                });

            if (lang === this._lang) {
                return this;
            }

            this._lang = lang;
            this._loadedModules = [];

            $rootScope.$broadcast('translate:switchLang:begin');

            for(i = 0; i < modules.length; i++) {
                this.loadModule(modules[i], doneFunction);
            }

            return this;
        };

        this.loadModule = function (module, whenDone) {
            if (_.indexOf(this._loadedModules, module) === -1 && module.length) {
                this._loadedModules.push(module);
                $http.get('/translate/module/' + module + (this._lang ? '/' + this._lang : '')).success(function(translate) {
                    self._lang = translate.lang;
                    self._table = _.extend(self._table, translate.translate);

                    $rootScope.$broadcast('translate:module:done', module);
                    if (_.isFunction(whenDone)) {
                        whenDone(module);
                    }
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

perfModule.factory('myCache', function ($cacheFactory) {
    return $cacheFactory('myCache');
});

perfModule.service('AbstractService', function($rootScope) {
    this._assignFunctions = function(request, success, error) {
        if (_.isUndefined(success) === false) {
            request.success(success);
        }

        if (_.isUndefined(error) === false) {
            request.error(error);
        }

        return request;
    };

    this.synchronize = function(requests, event) {
        var completed = 0, iterator;

        event    = event || 'synced';
        iterator = function() {
            completed++;

            if (completed === _.values(requests).length) {
                $rootScope.$broadcast(event, requests);
            }
        };

        _.each(requests, function(request) {
            request.success(iterator);
            request.error(iterator);
        });

        return this;
    };

    this.on = function(event, callback) {
        $rootScope.$on(event, callback);

        return this;
    };
});