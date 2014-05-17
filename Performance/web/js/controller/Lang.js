
function LangCtrl($scope, $http) {
    $scope.template = base + '/js/template/lang.html';
    $scope.langs = [];
    $scope.lang;

    $http.get(base + '/translate/langs').success(function(langs) {
        $scope.langs = langs.langs;
        $scope.lang  = _.findWhere($scope.langs, {'value': langs.default});
    });

    $scope.setLang = function(lang) {
        $scope.translate.switchLang(lang.value);
    }
}
