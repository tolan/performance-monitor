'use strict';


/* App Module */
var perfModule = angular.module(
    'Perf',
    ['ui.bootstrap', '$strap.directives', 'ngRoute']
).config(
    function($interpolateProvider) {
        $interpolateProvider.startSymbol('[[').endSymbol(']]');
    }
);

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
    function($http) {
        var self = this;
        this._placeholders = {};
        this._table = {};
        $http.get('translate').success(function(translate) {
            self._table = translate;
        });

        this._ = function (key, placeholders) {
            var string = key;
            if (this._table.hasOwnProperty(key)) {
                string = this._table[key];
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
    }
);

perfModule.run(
    ['$rootScope', 'translate', 'validator',
    function (rootScope, translate, validator) {
        rootScope.translate = translate;
        rootScope._         = function (key, placeholders) {
            return translate._(key, placeholders);
        };

        rootScope.validator = validator;
}]);

perfModule.config([
    '$routeProvider', '$locationProvider',
    function($routeProvider, $locationProvider) {
        $routeProvider.
            when('/profiler/list',       {templateUrl: '/Performance/web/js/template/Profiler/list.html',   controller: ProfilerListCtrl}).
            when('/profiler/new',        {templateUrl: '/Performance/web/js/template/Profiler/new.html',    controller: ProfilerCreateCtrl}).
            when('/profiler/edit/:id',   {templateUrl: '/Performance/web/js/template/Profiler/new.html',    controller: ProfilerCreateCtrl}).
            when('/profiler/detail/:id', {templateUrl: '/Performance/web/js/template/Profiler/detail.html', controller: ProfilerDetailCtrl}).
            when(
                '/profiler/detail/:measureId/attempt/:id',
                {templateUrl: '/Performance/web/js/template/Profiler/attemptDetail.html', controller: ProfilerAttemptDetailCtrl}
            ).
            otherwise({redirectTo: '/profiler/list'});

        $locationProvider.html5Mode(false);
    }
]);

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
