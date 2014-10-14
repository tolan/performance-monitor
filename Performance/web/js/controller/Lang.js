
function LangCtrl($scope, $http) {
    $scope.template = '/js/template/lang.html';
    $scope.langs = [];
    $scope.lang;

    $http.get('/translate/langs').success(function(langs) {
        $scope.lang  = langs.default;
        $scope.langs = langs.langs;
    });

    $scope.setLang = function(lang) {
        $scope.translate.switchLang(lang);
    };
}
