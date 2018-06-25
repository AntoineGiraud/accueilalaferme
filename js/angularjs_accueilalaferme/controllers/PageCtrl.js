app.controller('PageCtrl', function($scope, $timeout, $window, $rootScope, $interval){

    ////////////////////////////////
    // Variables & Initialisation //
    ////////////////////////////////
    $scope.getDate = function(d){d = new Date(d); d.setHours(0,0,0);return d;};

    $scope.loader = true;
    $scope.errorLoading = false;
    $scope.errorMessage = "Chargement ...";

    /////////////////////////
    // Gestion des alertes //
    /////////////////////////
    $scope.alerts = [
        // { type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.', 'timeout':false },
        // { type: 'black', msg: 'Oh snap! Change a few things up and try submitting again.', 'timeout':false },
        // { type: 'success', msg: 'Well done! You successfully read this important alert message.', 'timeout':false }
    ];

    $scope.addAlert = function(msg, type, timeout) {
        type = type || ''
        timeout = timeout || false
        msg = msg || 'Another alert!'
        $scope.alerts.push({type:type, msg:msg, timeout:timeout});
    };

    $scope.closeAlert = function(index) {
        $scope.alerts.splice(index, 1);
    };

    // $scope.addAlert("Message bidon", "danger", 5000);
});
