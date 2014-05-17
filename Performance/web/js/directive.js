perfModule.directive('menu', function() {
    return {
        restrict: 'E',
        transclude: true,
        scope: {
            data: '=data',
            title: '=dtitle',
            class: '=dclass',
            scope: '=dscope'
        },
        templateUrl: 'js/template/directive/menu.html',
        link: function(scope) {
            // For translate
            scope._ = scope.$parent._;
        },
        controller: function($scope) {
            $scope.onClick = function(item) {
                $scope.$emit('menu-selected-item', item, $scope.scope);
            };
        }
    };
});

perfModule.constant('datetimepickerConfig', {
    yearStep: 1,
    monthStep: 1,
    dayStep: 1,
    hourStep: 1,
    minuteStep: 1,
    showMeridian: false,
    meridians: ['AM', 'PM'],
    readonlyInput: false,
    mousewheel: true
}).directive('datetimepicker', ['$parse', '$log', 'datetimepickerConfig', function($parse, $log, datetimepickerConfig) {
    return {
        restrict: 'EA',
        require: '?^ngModel',
        replace: true,
        scope: {},
        templateUrl: 'js/template/directive/datetimepicker.html',
        link: function(scope, element, attrs, ngModel) {
            if (!ngModel) {
                return; // do nothing if no ng-model
            }

            var
                selected = new Date(),
                meridians = datetimepickerConfig.meridians;

            var yearStep = datetimepickerConfig.yearStep;
            if (attrs.yearStep) {
                scope.$parent.$watch($parse(attrs.yearStep), function(value) {
                    yearStep = parseInt(value, 10);
                });
            }

            var monthStep = datetimepickerConfig.monthStep;
            if (attrs.monthStep) {
                scope.$parent.$watch($parse(attrs.monthStep), function(value) {
                    monthStep = parseInt(value, 10);
                });
            }

            var dayStep = datetimepickerConfig.dayStep;
            if (attrs.dayStep) {
                scope.$parent.$watch($parse(attrs.dayStep), function(value) {
                    dayStep = parseInt(value, 10);
                });
            }

            var hourStep = datetimepickerConfig.hourStep;
            if (attrs.hourStep) {
                scope.$parent.$watch($parse(attrs.hourStep), function(value) {
                    hourStep = parseInt(value, 10);
                });
            }

            var minuteStep = datetimepickerConfig.minuteStep;
            if (attrs.minuteStep) {
                scope.$parent.$watch($parse(attrs.minuteStep), function(value) {
                    minuteStep = parseInt(value, 10);
                });
            }

            // 12H / 24H mode
            scope.showMeridian = datetimepickerConfig.showMeridian;
            if (attrs.showMeridian) {
                scope.$parent.$watch($parse(attrs.showMeridian), function(value) {
                    scope.showMeridian = !!value;

                    if (ngModel.$error.time) {
                        // Evaluate from template
                        var
                            years   = getYearsFromTemplate(),
                            months  = getMonthsFromTemplate(),
                            days    = getDaysFromTemplate(),
                            hours   = getHoursFromTemplate(),
                            minutes = getMinutesFromTemplate();

                        if (angular.isDefined(years) && angular.isDefined(months) && angular.isDefined(days)&& angular.isDefined(hours) && angular.isDefined(minutes)) {
                            selected.setYears(years);
                            selected.setMonth(months-1);
                            selected.setDate(days);
                            selected.setHours(hours);
                            refresh();
                        }
                    } else {
                        updateTemplate();
                    }
                });
            }

            function getYearsFromTemplate( ) {
                var years = parseInt(scope.years, 10);

                return years;
            }

            function getMonthsFromTemplate( ) {
                var months = parseInt(scope.months, 10);

                return (months >= 1 && months <= 12) ? months : undefined;
            }

            function getDaysFromTemplate( ) {
                var days = parseInt(scope.days, 10);

                return (days >= 1 && days <= 31) ? days : undefined;
            }

            // Get scope.hours in 24H mode if valid
            function getHoursFromTemplate( ) {
                var hours = parseInt(scope.hours, 10);
                var valid = (scope.showMeridian) ? (hours > 0 && hours < 13) : (hours >= 0 && hours < 24);
                if (!valid) {
                    return undefined;
                }

                if (scope.showMeridian) {
                    if (hours === 12) {
                        hours = 0;
                    }
                    if (scope.meridian === meridians[1]) {
                        hours = hours + 12;
                    }
                }
                return hours;
            }

            function getMinutesFromTemplate() {
                var minutes = parseInt(scope.minutes, 10);
                return (minutes >= 0 && minutes < 60) ? minutes : undefined;
            }

            function pad(value) {
                return (angular.isDefined(value) && value.toString().length < 2) ? '0' + value : value;
            }

            // Input elements
            var
                inputs = element.find('input'),
                yearsInputEl   = inputs.eq(0),
                monthsInputEl  = inputs.eq(1),
                daysInputEl    = inputs.eq(2),
                hoursInputEl   = inputs.eq(3),
                minutesInputEl = inputs.eq(4);

                // Respond on mousewheel spin
            var mousewheel = (angular.isDefined(attrs.mousewheel)) ? scope.$eval(attrs.mousewheel) : datetimepickerConfig.mousewheel;
            if (mousewheel) {
                var isScrollingUp = function(e) {
                    if (e.originalEvent) {
                        e = e.originalEvent;
                    }
                    //pick correct delta variable depending on event
                    var delta = (e.wheelDelta) ? e.wheelDelta : -e.deltaY;
                    return (e.detail || delta > 0);
                };

                yearsInputEl.bind('mousewheel wheel', function(e) {
                    scope.$apply((isScrollingUp(e)) ? scope.incrementYears() : scope.decrementYears());
                    e.preventDefault();
                });

                monthsInputEl.bind('mousewheel wheel', function(e) {
                    scope.$apply((isScrollingUp(e)) ? scope.incrementMonths() : scope.decrementMonths());
                    e.preventDefault();
                });

                daysInputEl.bind('mousewheel wheel', function(e) {
                    scope.$apply((isScrollingUp(e)) ? scope.incrementDays() : scope.decrementDays());
                    e.preventDefault();
                });

                hoursInputEl.bind('mousewheel wheel', function(e) {
                    scope.$apply((isScrollingUp(e)) ? scope.incrementHours() : scope.decrementHours());
                    e.preventDefault();
                });

                minutesInputEl.bind('mousewheel wheel', function(e) {
                    scope.$apply((isScrollingUp(e)) ? scope.incrementMinutes() : scope.decrementMinutes());
                    e.preventDefault();
                });
            }

            scope.readonlyInput = (angular.isDefined(attrs.readonlyInput)) ? scope.$eval(attrs.readonlyInput) : datetimepickerConfig.readonlyInput;
            if (!scope.readonlyInput) {
                var invalidate = function(invalidYears, invalidMonths, invalidDays, invalidHours, invalidMinutes) {
                    ngModel.$setViewValue(null);
                    ngModel.$setValidity('time', false);
                    if (angular.isDefined(invalidYears)) {
                        scope.invalidYears = invalidYears;
                    }
                    if (angular.isDefined(invalidMonths)) {
                        scope.invalidMonths = invalidMonths;
                    }
                    if (angular.isDefined(invalidDays)) {
                        scope.invalidDays = invalidDays;
                    }
                    if (angular.isDefined(invalidHours)) {
                        scope.invalidHours = invalidHours;
                    }
                    if (angular.isDefined(invalidMinutes)) {
                        scope.invalidMinutes = invalidMinutes;
                    }
                };

                scope.updateYears = function() {
                    var years = getYearsFromTemplate();

                    if (angular.isDefined(years)) {
                        selected.setYears(years);
                        refresh('y');
                    } else {
                        invalidate(true);
                    }
                };

                scope.updateMonths = function() {
                    var months = getMonthsFromTemplate();

                    if (angular.isDefined(months)) {
                        console.log(months-1);

                        selected.setMonth(months-1);
                        refresh('m');
                    } else {
                        invalidate(true);
                    }
                };

                scope.updateDays = function() {
                    var days = getDaysFromTemplate();

                    if (angular.isDefined(days)) {
                        selected.setDate(days);
                        refresh('m');
                    } else {
                        invalidate(true);
                    }
                };

                scope.updateHours = function() {
                    var hours = getHoursFromTemplate();

                    if (angular.isDefined(hours)) {
                        selected.setHours(hours);
                        refresh('h');
                    } else {
                        invalidate(true);
                    }
                };

                hoursInputEl.bind('blur', function(e) {
                    if (!scope.validHours && scope.hours < 10) {
                        scope.$apply(function() {
                            scope.hours = pad(scope.hours);
                        });
                    }
                });

                scope.updateMinutes = function() {
                    var minutes = getMinutesFromTemplate();

                    if (angular.isDefined(minutes)) {
                        selected.setMinutes(minutes);
                        refresh('m');
                    } else {
                        invalidate(undefined, true);
                    }
                };

                minutesInputEl.bind('blur', function(e) {
                    if (!scope.invalidMinutes && scope.minutes < 10) {
                        scope.$apply(function() {
                            scope.minutes = pad(scope.minutes);
                        });
                    }
                });
            } else {
                scope.updateYears   = angular.noop;
                scope.updateMonths  = angular.noop;
                scope.updateDays    = angular.noop;
                scope.updateHours   = angular.noop;
                scope.updateMinutes = angular.noop;
            }

            ngModel.$render = function() {
                var date = ngModel.$modelValue ? new Date(ngModel.$modelValue) : null;

                if (isNaN(date)) {
                    ngModel.$setValidity('time', false);
                    $log.error('Timepicker directive: "ng-model" value must be a Date object, a number of milliseconds since 01.01.1970 or a string representing an RFC2822 or ISO 8601 date.');
                } else {
                    if (date) {
                        selected = date;
                    }

                    makeValid();
                    updateTemplate();
                }
            };

            // Call internally when we know that model is valid.
            function refresh(keyboardChange) {
                makeValid();
                ngModel.$setViewValue(new Date(selected));
                updateTemplate(keyboardChange);
            }

            function makeValid() {
                ngModel.$setValidity('time', true);
                scope.invalidYears   = false;
                scope.invalidMonths  = false;
                scope.invalidDays    = false;
                scope.invalidHours   = false;
                scope.invalidMinutes = false;
            }

            function updateTemplate(keyboardChange) {
                var
                    years   = selected.getFullYear(),
                    months  = selected.getMonth()+1,
                    days    = selected.getDate(),
                    hours   = selected.getHours(),
                    minutes = selected.getMinutes();

                if (scope.showMeridian) {
                    hours = (hours === 0 || hours === 12) ? 12 : hours % 12; // Convert 24 to 12 hour system
                }

                scope.years    = years;
                scope.months   = months;
                scope.days     = days;
                scope.hours    = keyboardChange === 'h' ? hours : pad(hours);
                scope.minutes  = keyboardChange === 'm' ? minutes : pad(minutes);
                scope.meridian = selected.getHours() < 12 ? meridians[0] : meridians[1];
            }

            function addYears(years) {
                var dt = selected.getFullYear() + years;
                selected.setFullYear(dt);
                refresh();
            }

            function addMonths(months) {
                var
                    day = selected.getDate(),
                    dt = selected.getMonth() + months;

                selected.setDate(1);
                selected.setMonth(dt);

                var month = selected.getMonth();
                selected.setDate(day);

                if (month !== selected.getMonth()) {
                    if (months > 0) {
                        selected.setMonth(selected.getMonth()+1);
                        selected.setDate(0);
                    }

                    selected.setDate(0);
                }

                refresh();
            }

            function addDays(days) {
                var dt = selected.getDate() + days;
                selected.setDate(dt);
                refresh();
            }

            function addHours(hours) {
                var dt = selected.getHours() + hours;
                selected.setHours(dt);
                refresh();
            }

            function addMinutes(minutes) {
                var dt = selected.getMinutes() + minutes;
                selected.setMinutes(dt);
                refresh();
            }

            scope.incrementYears = function() {
                addYears(yearStep);
            };
            scope.decrementYears = function() {
                addYears(-yearStep);
            };
            scope.incrementMonths = function() {
                addMonths(monthStep);
            };
            scope.decrementMonths = function() {
                addMonths(-monthStep);
            };
            scope.incrementDays = function() {
                addDays(dayStep);
            };
            scope.decrementDays = function() {
                addDays(-dayStep);
            };
            scope.incrementHours = function() {
                addHours(hourStep);
            };
            scope.decrementHours = function() {
                addHours(-hourStep);
            };
            scope.incrementMinutes = function() {
                addMinutes(minuteStep);
            };
            scope.decrementMinutes = function() {
                addMinutes(-minuteStep);
            };
            scope.toggleMeridian = function() {
                addMinutes(12 * 60 * ((selected.getHours() < 12) ? 1 : -1));
            };

            refresh();
        }
    };
}]);