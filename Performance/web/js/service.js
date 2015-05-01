angular.module('PM')
.service(
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
)
.service(
    'validator',
    function(translate) {
        this._selected   = {};
        this._validators = {
            required : function (value) {
                var valid = null;
                if (_.isEmpty(value)) {
                    valid = 'main.validate.required';
                }

                return valid;
            }
        };

        this._ = function(message) {
            if (message) {
                return translate._(message);
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

        this.validate = function (values, validators, scope) {
            var result = {}, message;
            this.selectValidators(validators);

            _.each(validators, function(validator, key) {
                message = this._validateProperty(values[key], validator);

                if (message !== null) {
                    result[key] = message;
                }
            }, this);

            if (scope) {
                scope.validate = result;
            }

            return result;
        };

        this._validateProperty = function(value, validator) {
            var message = null, index;

            if (_.isFunction(validator)) {
                message = this._getMessage(
                    validator(value)
                );
            } else if (_.isArray(validator)) {
                for(index = 0; index < validator.length && message === null; index++) {
                    message = this._validateProperty(value, validator[index]);
                };
            } else if (_.has(this._validators, validator)) {
                message = this._getMessage(
                    this._validators[validator](value)
                );
            }

            return message;
        };

        this._getMessage = function(text) {
            var message = null;

            if (_.isObject(text)) {
                message = text;
            } else if (_.isString(text)) {
                message = {
                    message : text,
                    valid   : false
                };
            }

            return message;
        };
    }
)
.factory('myCache', function ($cacheFactory) {
    return $cacheFactory('myCache');
})
.service('AbstractService', function($rootScope) {
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
        var completed = 0, iterator, result = {};

        event    = event || 'synced';
        iterator = function(response, key) {
            completed++;
            result[key] = response;

            if (completed === _.values(requests).length) {
                if (_.isArray(requests)) {
                    result = _.values(result);
                }

                $rootScope.$broadcast(event, requests, result);
            }
        };

        _.each(requests, function(request, key) {
            var aa = function(response) {
                iterator(response, key);
            };

            request.success(aa);
            request.error(aa);
        });

        return this;
    };

    this.on = function(event, callback) {
        $rootScope.$on(event, callback);

        return this;
    };
});
