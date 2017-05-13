app.controller('FamilyCtrl', function($scope, $timeout, $window, $rootScope, $interval){
    groupData.prop.is_family = String(groupData.prop.is_family*1);
    for (k in groupData.persons) {
        bd = groupData.persons[k].birthday != null ? groupData.persons[k].birthday.split('-') : '';
        groupData.persons[k].birthday = bd[2] ? new Date(bd[0], bd[1]-1, bd[2]):null;
        groupData.persons[k].can_manage = Boolean(groupData.persons[k].can_manage*1);
    }
    $scope.group = groupData;
    $scope.removePerson = function(key) {
        res = confirm('Êtes vous sur de retirer cette personne ?');
        if (res) {
            console.log($scope.group);
            console.log("on suprime "+key, $scope.group.persons[key]);
            $scope.group.persons.splice(key, 1);
            console.log("on suprime "+key, $scope.group.persons[key]);
        }
    };
    $scope.addPerson = function() {
        $scope.group.persons.push({
            'pk' : null,
            'firstname' : '',
            'lastname' : '',
            'email' : '',
            'phone' : '',
            'birthday' : new Date(''),
            'can_manage' : 0,
            'link' : $scope.group.is_family*1 ? 'fils' : 'homme'
        });
    }

    // $scope.addAlert("Message bidon", "danger", 5000);
});
