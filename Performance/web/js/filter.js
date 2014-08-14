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
