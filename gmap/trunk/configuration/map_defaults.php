<?php
/*
 * Plugin GMap
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Page de paramétrage du plugin : interface des cartes pour Google Maps v3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');
include_spip('inc/gmap_saisie_utils');

function configuration_map_defaults_dist()
{
	$corps = "";
	
	// Si on a le résultat d'un traitement, l'afficher ici
	$corps .= gmap_decode_result("msg_result");

	// Lire l'API utilisée
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	$apiConfigKey = 'gmap_'.$api.'_interface';
	
	// Charger ce qui est spécifique à l'implémentation
	$show_map_defaults = charger_fonction("show_map_defaults", "mapimpl/".$api."/prive");
	$uiElements = '';
	$getParams = '';
	if (!$show_map_defaults($uiElements, $getParams))
		return '';

	// Récupération des infos sur le centre
	$isMarker = gmap_config_existe($apiConfigKey, 'default_latitude') && gmap_config_existe($apiConfigKey, 'default_longitude') && gmap_config_existe($apiConfigKey, 'default_zoom');
	$latitude = gmap_lire_config($apiConfigKey, 'default_latitude', "0.0");
	$longitude = gmap_lire_config($apiConfigKey, 'default_longitude', "0.0");
	$zoom = gmap_lire_config($apiConfigKey, 'default_zoom', "1");
	
	// Éléments d'interface spécifiques
	$corps .= $uiElements;
		
	// Élément DOM qui reçoit la carte
	$corps .= '<div id="carte_config" class="carte_configurer_gmap"></div>'."\n";
	
	// Script de mise à jour des marqueurs
	$corps .= '<script type="text/javascript">'."\n".'//<![CDATA[
// Anti-recursivité sur la modification interne des valeurs
var bIsModifyingValues = false;

// Mise à jour de la position du marqueur
function updateMarker(mapId, latitude, longitude)
{
	// Récupérer la carte
	var map = gMap(mapId);
	if (!isObject(map))
		return false;
	if (map.existMarker("defaut"))
		map.setMarkerPosition("defaut", latitude, longitude);
	else
	{
		var markerDef = {
			latitude: latitude,
			longitude: longitude,
			title: "'._T('gmap:position_defaut').'",
			draggable: true
		};
		map.setMarker("defaut", markerDef);
	}
	return true;
}

// Modification de la position par programme
function setAddress(mapId, latitude, longitude, zoom)
{
	updateMarker(mapId, latitude, longitude);
	bIsModifyingValues = true;
	jQuery("#map_center_latitude").val(latitude);
	jQuery("#map_center_longitude").val(longitude);
	_setMarkerChanged(false);
	bIsModifyingValues = false;
	gMap(mapId).panTo(latitude, longitude);
	if (isObject(zoom))
		gMap(mapId).setZoom(zoom);
}
//]]>'."\n".'</script>'."\n";

	// Interface de la position par défaut & geocoder
	$corps .= '<fieldset id="config_carte_center" class="config_group">
<legend>'._T('gmap:configuration_defaults_center').'</legend>
<div class="padding"><div class="interior">
	<p class="texte">'._T('gmap:explication_defaults_center').'</p>
	<div class="sub-fieldset">
		<table id="marker" class="edit_markers">
			<tbody>
				<tr class="header"><th>'._T('gmap:latitude').'</th><th>'._T('gmap:longitude').'</th><th>'._T('gmap:zoom').'</th><th>&nbsp;</th></tr>
				<tr class="marker">
					<td><input class="marker_lat track_marker_changes text" type="text" name="map_center_latitude" id="map_center_latitude" value="'.$latitude.'" size="12" style="width:80px;" /></td>
					<td><input class="marker_long track_marker_changes text" type="text" name="map_center_longitude" id="map_center_longitude" value="'.$longitude.'" size="12" style="width:80px;" /></td>
					<td><input class="marker_zoom track_marker_changes text" type="text" name="map_zoom" id="map_zoom" value="'.$zoom.'" size="6" style="width:30px;" /></td>
					<td><span id="validate" /></td>
				</tr>
			</tbody>
		</table>
		<p id="marker-warning"></p>
		<br class="nettoyeur" />
	</div>
	<div class="sub-fieldset">
		<p class="label">'._T('gmap:geocoder_label').'</p>' . "\n";
	$corps .= gmap_sous_bloc_geocoder("CarteConfig", "setAddress", false);
	$corps .= '	</div>
</div></div>
</fieldset>' . "\n";

	// Script de gestion de la mise à jour manuelle du marqueur
	$corps .= '<script type="text/javascript">'."\n".'//<![CDATA['."\n";
	$corps .= '
// Activer l\'interface de sauvegarde manuelle
function _setMarkerChanged(bChanged)
{
	if (bChanged)
	{
		jQuery("table#marker #validate").addClass("active");
		jQuery("#marker-warning").html("'._T('gmap:marqueur_modifie_manuellement').'");
	}
	else
	{
		jQuery("table#marker #validate").removeClass("active");
		jQuery("#marker-warning").html("");
	}
}

// Mise en place des listeners sur la modification manuelle des champs
jQuery(document).ready(function() {
	var bloc = jQuery("table#marker");
	jQuery("input.track_marker_changes", bloc).keydown(function() {
		_setMarkerChanged(true);
	});
	jQuery("#validate", bloc).click(function() {
		var lat = Number(jQuery("#map_center_latitude").val());
		var lng = Number(jQuery("#map_center_longitude").val());
		var zoom = Number(jQuery("#map_zoom").val());
		setAddress("CarteConfig", lat, lng, zoom);
	});
});
';
	$corps .= '//]]>'."\n".'</script>'."\n";

	// Script d'initialisation de la carte
	$corps .= '<script type="text/javascript">'."\n".'//<![CDATA['."\n";
	$corps .= $getParams;
	$corps .= '
// Il y a une erreur "undefined" sous IE8, le hack ci-dssous semble règler
// le problème...
var IE8NamespaceHack = document.namespaces;

// Chargement de la carte et mise en place des gestionnaire d\'évènement
function loadCarteConfig(mapId, divId)
{
	// Récupérer de la carte
	var map = MapWrapper.getMap(mapId, true);
	if (!isObject(map))
		return false;
		
	// Chargement de la carte
	if (!map.load(divId, getParams(true)))
		return false;
		
	// Mise en place des listeners sur le clic et le zoom
	map.addListener("click-on-map", function(event, latitude, longitude)
	{
		setAddress(mapId, latitude, longitude);
	});
	map.addListener("zoom", function(event, zoom)
	{
		bIsModifyingValues = true;
		jQuery("#map_zoom").val(zoom);
		bIsModifyingValues = false;
	});
	';
	if ($isMarker && $latitude && $longitude)
	{
		$corps .= '
	// Création du marqueur
	updateMarker(mapId, '.$latitude.', '.$longitude.');
	
	// Mise en place sur le listener de drop du marqueur
	map.addListener("drop-marker", function(event, id, latitude, longitude)
	{
		if (id === "defaut")
			setAddress(mapId, latitude, longitude);
	});';
	}
	$corps .= '
	
	return true;
}

// Chargement du document
jQuery(document).ready(function()
{
	// Charger la carte
	if (!isObject(gMap("CarteConfig")) || !gMap("CarteConfig").isLoaded())
		loadCarteConfig("CarteConfig", "carte_config");

	// Quand la demande jQuery part, il faut détruire la carte pour qu\'elle soit
	// correctement recréée sur le document.ready qui interviendra à sa complétion
	jQuery("#config_bloc_gmap-map_defaults").ajaxSend(function(evt, request, settings)
	{
		if (jQuery(this).isAjaxTarget(settings) && isObject(gMap("CarteConfig")))
			MapWrapper.freeMap("CarteConfig");
	});

	// Récupération des modifications sur les champs pour mettre à jour le paramétrage de la carte
	jQuery("#config_bloc_gmap-map_defaults input.tracked").change(function()
	{
		if (!bIsModifyingValues)
			gMap("CarteConfig").update(getParams(false));
	});
	jQuery("#config_bloc_gmap-map_defaults select.tracked").change(function()
	{
		if (!bIsModifyingValues)
			gMap("CarteConfig").update(getParams(false));
	});
});

// Fermeture de la page
jQuery(document).unload(function()
{
	if (isObject(gMap("CarteConfig")))
		MapWrapper.freeMap("CarteConfig");
});

';

	$corps .= '//]]>'."\n".'</script>'."\n";
	
	// Renvoyer le formulaire
	return gmap_formulaire_ajax('config_bloc_gmap', 'map_defaults', 'configurer_gmap_ui', $corps,
		find_in_path('images/logo-config-map_defaults.png'),
		_T('gmap:configuration_defaults'));
}



?>
