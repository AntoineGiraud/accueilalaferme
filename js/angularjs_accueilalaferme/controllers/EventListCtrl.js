app.controller('EventListCtrl', function($scope){
    function getDate(d) {
        explode = d.split('-');
        d = new Date(explode[0], explode[1]-1, explode[2]);
        return d;
    };
    $scope.persons = persons;
    for (k in $scope.persons) {
        p = $scope.persons[k];
        p.arrival_date_time = getDate(p.arrival_date);
        p.departure_date_time = getDate(p.departure_date);
    }
    console.log("$scope.persons", $scope.persons);
    $scope.count = $scope.persons.length;
    $scope.filter = {
        arrivee: '',
        depart: '',
        present: '',
        age_debut: '',
        age_fin: ''
    };
    $scope.$watch('filter', function(newVals, oldVals) {
        console.log('$scope.filter', $scope.filter);
        $scope.count=0;
        for (k in $scope.persons) {
            // console.log('person', $scope.persons[k]);
            show = showPerson($scope.persons[k]);
            if (show) $scope.count++;
            $scope.persons[k].show = show;
        }
    }, true);
    function showPerson(person){
        f = $scope.filter;
        console.log(f.present, person.arrival_date_time, person.departure_date_time, person);
        return (
            (!f.age_debut && !f.age_fin)
            || (!f.age_debut && f.age_fin >= person['age']*1)
            || (!f.age_fin && f.age_debut <= person['age']*1)
            || (f.age_debut <= person.age && f.age_fin >= person.age)
        ) && (
            (!f.arrivee && !f.depart)
            || (!f.arrivee && 1*f.depart == 1*person.departure_date_time)
            || (!f.depart && 1*f.arrivee == 1*person.arrival_date_time)
            || (1*f.arrivee == 1*person.arrival_date_time && 1*f.depart == 1*person.departure_date_time)
        ) && (
            !f.present ||
            (1*f.present >= 1*person.arrival_date_time && 1*f.present <= 1*person.departure_date_time)
        );

// (!age_debut && !age_fin)
// || (!age_debut && age_fin >= <?= $person['age']*1 ?>)
// || (!age_fin && age_debut <= <?= $person['age']*1 ?>)
// || (age_debut <= <?= $person['age']*1 ?> && age_fin >= <?= $person['age']*1 ?>)
// )
// &&
// (
// (!arrivee && !depart)
// || (!arrivee && 1*depart == 1*getDate('<?=$person['departure_date']?>'))
// || (!depart && 1*arrivee == 1*getDate('<?=$person['arrival_date']?>'))
// || (1*arrivee == 1*getDate('<?=$person['arrival_date']?>') && 1*depart == 1*getDate('<?=$person['departure_date']?>'))
// )
// &&
// (
// !present ||
// (1*present >= 1*getDate('<?=$person['arrival_date']?>') && 1*present <= 1*getDate('<?=$person['departure_date']?>'))
// )


    }
});
