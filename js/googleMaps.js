$(document).ready(function () {
    var viewMapButton = $("#view-map-button");
    var clicked = false;

    //Assign Unobtrusive JS View Map Button click event handler
    viewMapButton.on("click", function () {
        if (clicked) {
            $("#map").animate({width: 'toggle'}, 200);
            viewMapButton.html('<span class="glyphicon glyphicon-map-marker"></span>' + ' View Map')
            clicked = false;
        } else {
            $("#map").animate({width: 'toggle'}, 200);
            viewMapButton.html('<span class="glyphicon glyphicon-map-marker"></span>' + ' Hide Map');
            clicked = true;
        }
        //Must be called after resizing the map
        setTimeout(function () {
            google.maps.event.trigger(map, 'resize');
            map.fitBounds(bounds);
        }, 200);
        $("html, body").animate({scrollTop: $(document).height() - $(window).height()});
    });
});

//API Keys - Please restrain using for any other purposes than this web as they are not domain specific (Roman's)
var GoogleMapsGeocodingAPIKey = "AIzaSyAxuYc9T5Asu1udrhXE7DWjTRSCOiEc9Q8";
var GoogleMapsAPIKey = "AIzaSyBYJpoSa9lV98hqJIOmBL_3VrIeAl7yoOc";

var map;
var geocoder;
var infoWindow;

function initMap() {
    var myLatLng = {lat: -25.363, lng: 131.044};

    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 4,
        center: myLatLng
    });

    geocoder = new google.maps.Geocoder();

    infoWindow = new google.maps.InfoWindow();

    resizeMap();
    resizeMap();
}

$(window).resize(function(){
    resizeMap();
});

function resizeMap(){
    var tableWidth = $(".table.table-hover.custab").width();
    $("#map").css({"width": tableWidth, "height" : tableWidth/1.8});
    google.maps.event.trigger(map, 'resize');
    if(alreadyInitialized){
        map.fitBounds(bounds);
    }
}

//Essential Map variables
var infoWindowArray = [];
var bounds;
var alreadyInitialized = false;

function addMarkers(JSON){
    //Completely useless array for closure/callback BS...
    var temp = new Array();

    bounds = new google.maps.LatLngBounds();

    for(i = 0; i < JSON.length; i++){
        (function(address, adventureInfoWindowContent) {
            address = JSON[i].City + " " + JSON[i].Country;
            adventureInfoWindowContent =
                '<h3><a href="adventure.php?id=' + JSON[i].PostID + '">' + JSON[i].Title + '</a></h3>' +
                '<p>' + JSON[i].DatePosted + '</p>';

            geocoder.geocode({'address': address}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    var marker = new google.maps.Marker({
                        position: results[0].geometry.location,
                        map: map,
                        title: address
                    });

                    bounds.extend(marker.position);

                    google.maps.event.addListener(marker, 'click', function () {
                        infoWindow.setContent(adventureInfoWindowContent);
                        infoWindow.open(map, marker);
                    });
                } else {
                    console.log('Geocode was not successful for the following reason: ' + status);
                    console.log('Address: ' + address);
                }
            });
        })(temp[i]);
    }

    /*        google.maps.event.addListener(marker, 'click', (function(marker, content, infoWindow){
     return function() {
     //Close any other open infoWindows
     closeInfoWindows();

     infoWindow.setContent(content);
     infoWindow.open(map, marker);

     infoWindowArray[0] = infoWindow;
     };
     })(marker, adventureInfoWindowContent, infoWindow));*/

    map.fitBounds(bounds);
    alreadyInitialized = true;
}

//Detach the info-window from the marker. Undocumented in the API docs
function closeInfoWindows(){
    if(infoWindowArray.length > 0){
        infoWindowArray[0].set("marker", null);
        infoWindowArray[0].close();
        infoWindowArray.length = 0;
    }
}