angular.module('PM')
    .controller('LangCtrl', LangCtrl);

/**
 * Controller for switching language.
 *
 * @param $scope Scope
 * @param $http  Http provider
 *
 * @returns void
 */
function LangCtrl($scope, $http) {
    $scope.template = '/js/template/lang.html';
    $scope.langs    = [];
    $scope.lang;

    $http.get('/translate/language/get').success(function(langs) {
        $scope.lang  = langs.default;
        $scope.langs = langs.langs;
    });

    $scope.setLang = function(lang) {
        $scope.translate.switchLang(lang);
    };
}
