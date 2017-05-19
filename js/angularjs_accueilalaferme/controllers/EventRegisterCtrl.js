app.controller('EventRegisterCtrl', function($scope, $timeout, $window, $rootScope, $interval){
    for (k in groupData.persons) {
        groupData.persons[k].will_come = Boolean(groupData.persons[k].will_come*1);
    }
    $scope.group = groupData;

    // $scope.addAlert("Message bidon", "danger", 5000);
});
