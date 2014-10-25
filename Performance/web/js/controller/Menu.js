angular.module('PM')
    .controller('MenuCtrl', MenuCtrl);

/**
 * Controller for show navigation menu.
 *
 * @param $scope Scope
 * @param $http  Http provider
 *
 * @returns void
 */
function MenuCtrl($scope, $http) {
    $scope.template = '/js/template/menu.html';
    $scope.item     = {};

    $http.get('menu').success(function(menu) {
        $scope.item.submenu = menu;
    });
}
