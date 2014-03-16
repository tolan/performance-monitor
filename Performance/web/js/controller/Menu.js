
function MenuCtrl($scope, $http) {
    $scope.template = '/js/template/menu.html';

    $http.get('menu').success(function(menu) {
        $scope.menu = menu;
    });
}
