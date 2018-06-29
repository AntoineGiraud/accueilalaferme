app.controller('UserListCtrl', function($scope){
    $scope.users = users;
    $scope.count_users = $scope.users.length;
    console.log($scope.users.length)
    console.log(users.length)
});