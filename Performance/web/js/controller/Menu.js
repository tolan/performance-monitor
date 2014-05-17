
function MenuCtrl($scope, $http) {
    $scope.template = base + '/js/template/menu.html';
    $scope.item = {};

    $http.get('menu').success(function(menu) {
        $scope.item.submenu = menu;
    });
}
