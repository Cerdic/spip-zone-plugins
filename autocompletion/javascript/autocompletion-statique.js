
var class_ville   = 'autocompletion_ville';
var element_ville = 'input.'+class_ville;
var class_cp      = 'autocompletion_cp';
var element_cp    = 'input.'+class_cp;
var id_map        = 'map_canvas';
var element_map   = 'div#'+id_map;
    
$(document).ready(function() {
    
    if( $(element_cp).length > 0 && $(element_ville).length > 0){        
        
        // Initialisation de la Google map
        if($(element_map).length > 0){
            initialize();
        }
        
        // Gestion de l'autocomplete par ajax
        $(function (){
            $( element_cp + ", " + element_ville ).autocomplete({
                source: function (request, response){
                    var objData = {};
                    if ($(this.element).hasClass(class_cp)){
                        objData = {
                            codePostal: request.term, 
                            maxRows: 20
                        };
                    }
                    else{
                        objData = {
                            ville: request.term, 
                            maxRows: 30
                        };
                    }
                    $.ajax({
                        url: "plugins/autocompletion/inc/autocompletion.php",
//                        url: "#CHEMIN{inc/autocompletion.php}",
                        dataType: "json",
                        data: objData,
                        type: 'POST',
                        success: function (data){
                            response($.map(data, function (item){
                                
                                return {
                                    label: ""+ item.CodePostal + " - " + item.Ville,
                                    value: function (){
                                        if ($(this).hasClass(class_cp)){
                                            return item.CodePostal;
                                        }
                                        else{
                                            return item.Ville;
                                        }
                                    },
                                    cp: item.CodePostal,
                                    ville: item.Ville,
                                    latitude: item.Latitude,
                                    longitude: item.Longitude
                                }
                            }));
                        }
                    });
                },
                minLength: 2,
                delay: 200, 
                select: function(event, ui) {
                    $(element_ville).val(ui.item.ville);
                    $(element_cp).val(ui.item.cp);
//                    $("input.autocompletion_latitude").val(ui.item.latitude);
//                    $("input.autocompletion_longitude").val(ui.item.longitude);
//                    if($(element_map).length > 0){
                        var location = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
                        marker.setPosition(location);
                        map.setCenter(location);
//                    }
                }
            });
        });
    }
});

function initialize() {          
    
    var latitude_marker  = 45.044881; 
    var longitude_marker = 3.8898673999999573; 
    var latitude_centre  = 46.763056; 
    var longitude_centre = 2.424722; 
    
    var options = {
        center: new google.maps.LatLng( latitude_centre , longitude_centre ),
        zoom: 5,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
            
    map = new google.maps.Map(document.getElementById(id_map), options);
    geocoder = new google.maps.Geocoder();   
    marker = new google.maps.Marker({
        position: new google.maps.LatLng(latitude_marker, longitude_marker),
        map: map,
        draggable: false
    });      
}


