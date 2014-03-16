
function LangCtrl($scope, $http) {
    $scope.template = '/js/template/lang.html';
    $scope.langs = [];
    $scope.lang;

    $http.get('/translate/langs').success(function(langs) {
        $scope.langs = langs.langs;
        $scope.lang  = _.findWhere($scope.langs, {'value': langs.default});
    });

    $scope.setLang = function(lang) {
        $scope.translate.switchLang(lang.value);
    }
}
