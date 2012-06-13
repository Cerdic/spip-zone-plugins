<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Modification de la géolocalisation d'un objet : formulaire affiché
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Affichage du formulaire de géolocalisation d'un objet SPIP
function formulaires_outil_geoloc_geocoder_dist($args)
{
	$mapId = $args['mapId'];
	
	$parts = array();
	$parts['html'] = '';
	$parts['script'] = '';
	$parts['script_ready'] = '';

	$parts['html'] .= '
	<div class="geoedit_subform">
		<input type="text" class="text empty-edit" size="50" name="'.$mapId.'_address" id="'.$mapId.'_address" value="'._T('gmap:address_explic').'" style="width:360px; margin-right:10px;" /><input type="button" name="'.$mapId.'_geocode" id="'.$mapId.'_geocode" value="'._T('gmap:address_btn_find').'" disabled="disabled" />
	</div>';

	$parts['script_ready'] = '
	GeolocGeocoder.obj("'.$mapId.'").initialize({
			address_explic: "'.html_entity_decode(_T('gmap:address_explic'), ENT_COMPAT, $GLOBALS['meta']['charset']).'",
			geocoder_name: "'._T('gmap:geocoder_name').'",
			latitude: "'._T('gmap:latitude').'",
			longitude: "'._T('gmap:longitude').'"
		});';
	
	return $parts; 
}

?>
