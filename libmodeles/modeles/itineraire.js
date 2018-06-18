var map;
var panel;
var initialize_map_itineraire;
var calculate_itineraire;
var direction;

initialize_map_itineraire = function(){
  var latLng = new google.maps.LatLng($('#itineraire_map').data("lat") || 44.330642, $('#itineraire_map').data("lng") || -1.225703); // Correspond au coordonnées de Lille
  var myOptions = {
    zoom      : 14, // Zoom par défaut
    center    : latLng, // Coordonnées de départ de la carte de type latLng 
    mapTypeId : google.maps.MapTypeId.TERRAIN, // Type de carte, différentes valeurs possible HYBRID, ROADMAP, SATELLITE, TERRAIN
    maxZoom   : 20
  };
  
  map      = new google.maps.Map(document.getElementById('itineraire_map'), myOptions);
  panel    = document.getElementById('itineraire_panel');
  
  var marker = new google.maps.Marker({
    position : latLng,
    map      : map,
    title    : $('#itineraire_map').data("title") || 'Perdu'
    //icon     : "marker_lille.gif" // Chemin de l'image du marqueur pour surcharger celui par défaut
  });
  
  
  direction = new google.maps.DirectionsRenderer({
    map   : map,
    panel : panel // Dom element pour afficher les instructions d'itinéraire
  });

};

calculate_itineraire = function(){
    origin      = document.getElementById('itineraire_origin').value; // Le point départ
    destination = document.getElementById('itineraire_destination').value; // Le point d'arrivé
    if(origin && destination){
        var request = {
            origin      : origin,
            destination : destination,
            travelMode  : google.maps.DirectionsTravelMode.DRIVING // Mode de conduite
        }
        var directionsService = new google.maps.DirectionsService(); // Service de calcul d'itinéraire
        directionsService.route(request, function(response, status){ // Envoie de la requête pour calculer le parcours
            if(status == google.maps.DirectionsStatus.OK){
                direction.setDirections(response); // Trace l'itinéraire sur la carte et les différentes étapes du parcours
            }
        });
    }
};