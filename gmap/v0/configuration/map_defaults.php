<?php
/*
 * Plugin GMap
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Page de param�trage du plugin : interface des cartes pour Google Maps v3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');
include_spip('inc/gmap_saisie_utils');

function generic_show_map_defaults(&$uiElements, &$getParams, $profile = 'interface')
{
	$uiElements = '';
	$getParams = '
// Lire les param�tres de la carte dans les �l�ments de formulaire
function getParams(bIncludeViewport)
{
	var params = new Object();
	
	// Position par d�faut
	if (bIncludeViewport)
	{
		params["viewLatitude"] = parseFloat(jQuery("#map_center_latitude").val());
		params["viewLongitude"] = parseFloat(jQuery("#map_center_longitude").val());
		params["viewZoom"] = parseFloat(jQuery("#map_zoom").val());
	}
	
	return params;
}
';
	return true;
}

function configuration_map_defaults_dist($exec = 'configurer_gmap_ui', $profile = 'interface')
{
	$corps = "";
	
	// Quand le bloc est appel� en ajax, il ne re�oit pas les param�tres, puisque
	if (_AJAX)
	{
		$exec = _request('script');
		$profile = _request('map_defaults_profile');
	}

	// Si on a le r�sultat d'un traitement, l'afficher ici
	$corps .= gmap_decode_result("msg_result");

	// La clef � laquelle se trouvent les configurations d�pend du profile et de l'api
	if (!isset($profile))
		$profile = 'interface';
	$api = gmap_lire_config('gmap_api', 'api', 'gma3');
	$apiConfigKey = 'gmap_'.$api.'_'.$profile;
	
	// Charger ce qui est sp�cifique � l'impl�mentation
	$show_map_defaults = charger_fonction("show_map_defaults", "mapimpl/".$api."/prive", true);
	$uiElements = '';
	$getParams = '';
	if (!$show_map_defaults || !$show_map_defaults($uiElements, $getParams, $profile))
		generic_show_map_defaults($uiElements, $getParams, $profile);
		
	// Si le profile n'est pas 'interface', les autres (donc le priv�) peuvent si
	// r�f�rer. Donc il y a une case � cocher pour faire afficher le reste.
	if ($profile != 'interface')
	{
		$toInterface = gmap_lire_config($apiConfigKey, 'redirect_to_interface', "oui");
		$corps .= '
		<div class="padding"><div class="interior">
			<input type="hidden" name="map_defaults_profile" value="'.$profile.'" />
			<input type="checkbox" name="map_defaults_auto" id="map_defaults_auto" value="oui"'.(($toInterface==="oui")?'checked="checked"':'').' />&nbsp;<label for="map_defaults_auto">'._T('gmap:utilise_param_interface').'</label>
		</div></div>';
		$corps .= '
<script type="text/javascript">'."\n".'//<![CDATA[
	jQuery(document).ready(function() {
		jQuery("#map_defaults_auto").change(function() {
			if (jQuery(this).attr("checked"))
				jQuery("#profile_content").hide();
			else
			{
				jQuery("#profile_content").show();
				jQuery("#carte_config").trigger("resize");
			}
		});
	});
//]]>'."\n".'</script>';
		$corps .= '
		<div class="profile_content" id="profile_content"'.(($toInterface==="oui")?' style="display: none;"':'').'>';
	}

	// �l�ments d'interface sp�cifiques
	$corps .= $uiElements;
		
	// �l�ment DOM qui re�oit la carte
	$corps .= '<div id="carte_config" class="carte_configurer_gmap"></div>'."\n";
	
	// R�cup�ration des infos sur le marqueur de centre enregistr�
	$isMarker = gmap_config_existe($apiConfigKey, 'default_latitude') && gmap_config_existe($apiConfigKey, 'default_longitude') && gmap_config_existe($apiConfigKey, 'default_zoom');
	$latitude = gmap_lire_config($apiConfigKey, 'default_latitude', "0.0");
	$longitude = gmap_lire_config($apiConfigKey, 'default_longitude', "0.0");
	$zoom = gmap_lire_config($apiConfigKey, 'default_zoom', "1");
	
	// Script de mise � jour des marqueurs
	$corps .= '<script type="text/javascript">'."\n".'//<![CDATA[
// Anti-recursivit� sur la modification interne des valeurs
var bIsModifyingValues = false;

// Mise � jour de la position du marqueur
function updateMarker(mapId, latitude, longitude)
{
	// R�cup�rer la carte
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

	// Interface de la position par d�faut & geocoder
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
	</div>' . "\n";
	if (gmap_capability('GeoCoder'))
	{
		$corps .= '	<div class="sub-fieldset">
			<p class="label">'._T('gmap:geocoder_label').'</p>' . "\n";
		$corps .= gmap_sous_bloc_geocoder("CarteConfig", "setAddress", false);
		$corps .= '	</div>' . "\n";
	}
	$corps .= '</div></div>
</fieldset>' . "\n";

	// Script de gestion de la mise � jour manuelle du marqueur
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
// Chargement de la carte et mise en place des gestionnaire d\'�v�nement
function loadCarteConfig(mapId, divId)
{
	// R�cup�rer de la carte
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
	// Cr�ation du marqueur
	updateMarker(mapId, '.$latitude.', '.$longitude.');';
	if (gmap_capability('dragmarkers'))
	$corps .= '
	
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

	// Quand la demande jQuery part, il faut d�truire la carte pour qu\'elle soit
	// correctement recr��e sur le document.ready qui interviendra � sa compl�tion
	jQuery("#config_bloc_gmap-map_defaults").ajaxSend(function(evt, request, settings)
	{
		if (jQuery(this).isAjaxTarget(settings) && isObject(gMap("CarteConfig")))
			MapWrapper.freeMap("CarteConfig");
	});

	// R�cup�ration des modifications sur les champs pour mettre � jour le param�trage de la carte
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

	// Fermer la div suppl�mentaire si on n'est pas en profil 'interface'
	if ($profile != 'interface')
	{
		$corps .= '
		</div>';
	}
	// Renvoyer le formulaire
	return gmap_formulaire_ajax('config_bloc_gmap', 'map_defaults', $exec, $corps,
		find_in_path('images/logo-config-map_defaults.png'),
		_T('gmap:configuration_defaults'));
}

?>