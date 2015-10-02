yamba.App
.controller('AddGroupController', ['$scope', 'close', function($scope, close) {

  $scope.display = true;

  $scope.close = function() {
    $scope.display = false;
 	  close();
 };

}]);