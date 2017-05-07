
String.prototype.trunc = String.prototype.trunc || function(n){
    return this.length>n ? this.substr(0, n-1) + '...' : this;
};
function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}
function clone(obj) {
    var target = {};
    for (var i in obj) {
        if (obj.hasOwnProperty(i)) target[i] = obj[i];
    }
    return target;
}

// proj4.defs["EPSG:2950"] = "+proj=tmerc +lat_0=0 +lon_0=-73.5 +k=0.9999 +x_0=304800 +y_0=0 +ellps=GRS80 +units=m +no_defs";
// console.log(proj4(proj4.defs["EPSG:2950"]).forward([-71, 41]));
// console.log(proj4(proj4.defs["EPSG:2950"]).inverse([515126, 4543130]));

var app = angular.module('app', ['ngSanitize', 'ui.bootstrap']);

app.config(['$compileProvider', function ($compileProvider) {
  $compileProvider.debugInfoEnabled(false);
}]);

// Launch bootstrap component
$( document ).ready(function() {
    $('[data-toggle="tooltip"]').tooltip({html: true});
    $('[data-toggle="popover"]').popover({html: true});
});