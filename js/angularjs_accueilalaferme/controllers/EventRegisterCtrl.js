app.controller('EventRegisterCtrl', function($scope, $timeout, $window, $rootScope, $interval){
    for (k in groupData.persons) {
        groupData.persons[k].will_come = Boolean(groupData.persons[k].will_come*1);
        if (typeof groupData.persons[k].arrival_date != 'undefined' && groupData.persons[k].arrival_date) {
            console.log(groupData.persons[k].arrival_date);
            startEvent = groupData.persons[k].arrival_date.split('-');
            groupData.persons[k].arrival_date = new Date(startEvent[0], startEvent[1]-1, startEvent[2]);
        }
        if (typeof groupData.persons[k].departure_date != 'undefined' && groupData.persons[k].departure_date) {
            console.log(groupData.persons[k].departure_date);
            endEvent = groupData.persons[k].departure_date.split('-');
            groupData.persons[k].departure_date = new Date(endEvent[0], endEvent[1]-1, endEvent[2]);
        }
    }
    $scope.group = groupData;

    startEvent = start_date.split('-');
    startEvent = new Date(startEvent[0], startEvent[1]-1, startEvent[2]);
    endEvent = end_date.split('-');
    endEvent = new Date(endEvent[0], endEvent[1]-1, endEvent[2]);

    $scope.$watch('group.persons', function(newVals, oldVals) {
        console.log('watch group persons', newVals);
        for (k in newVals) {
            row = newVals[k];
            if (!row.will_come) {
                row.departure_date = null;
                row.arrival_date = null;
            } else {
                if (!row.arrival_date && startEvent)
                    row.arrival_date = startEvent;
                if (!row.departure_date && endEvent)
                    row.departure_date = endEvent;
            }

            if (typeof row.errors == 'undefined') row.errors = {};
            if (row.arrival_date > row.departure_date)
                row.errors.arrival_date = true;
            else
                row.errors.arrival_date = false;
        }
    }, true);

    // $scope.addAlert("Message bidon", "danger", 5000);
});
