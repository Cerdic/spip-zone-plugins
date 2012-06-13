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

// Helpers
function _outil_geoloc_siblings_lire_config($config, $objet, $default)
{
	$value = gmap_lire_config('gmap_geoloc_params', $config.'_'.$objet, "notset");
	if ($value === "notset")
		$value = gmap_lire_config('gmap_edit_params', $config, $default);
	return $value;
}

// Affichage du formulaire de géolocalisation d'un objet SPIP
function formulaires_outil_geoloc_siblings_dist($args)
{
	$mapId = $args['mapId'];
	
	$objet = $args['objet'];
	$limiteViewport = (_outil_geoloc_siblings_lire_config('siblings_geo_interval', $objet, "non") === 'oui') ? true : false;
	$interval = (_outil_geoloc_siblings_lire_config('siblings_time_interval', $objet, "non") === 'oui') ? true : false;
	$timeInterval = _outil_geoloc_siblings_lire_config('siblings_interval', $objet, (($objet == 'document') ? "2" : "7"));
	$timeIntervalUnit = _outil_geoloc_siblings_lire_config('siblings_unite_interval', $objet, (($objet == 'document') ? "heure" : "jour"));
	$meme_parent = (_outil_geoloc_siblings_lire_config('siblings_same_parent', $objet, "oui") === 'oui') ? true : false;
	$limite = _outil_geoloc_siblings_lire_config('siblings_limit', $objet, "5");
	
	$parts = array();
	$parts['html'] = '';
	$parts['script'] = '';
	$parts['script_ready'] = '';

	$parts['html'] .= '
	<div class="siblings_subform" id="'.$mapId.'_SiblingsSubForm">
		<input type="checkbox" name="limite_vue" id="'.$mapId.'_siblings_limite_vue" value="oui"'.($limiteViewport ? ' checked="checked"' : '').' />&nbsp;<label for="limite_vue">'._T('gmap:choix_siblings_limite_vue').'</label><br />
		<input type="checkbox" name="interval_temps" id="'.$mapId.'_siblings_interval_temps" value="oui"'.($interval ? ' checked="checked"' : '').' />&nbsp;<label for="interval_temps">'._T('gmap:choix_siblings_interval_temps').'</label>
			&nbsp;<input type="text" class="text" size="5" name="interval" id="'.$mapId.'_sibling_interval" value="'.$timeInterval.'" />
			&nbsp;	<select name="unite_interval" id="'.$mapId.'_sibling_unite_interval" size="1">
						<option value="heure"'.(($timeIntervalUnit === 'heure') ? ' selected="selected"' : '').'>'._T('gmap:heure').'</option>
						<option value="jour"'.(($timeIntervalUnit === 'jour') ? ' selected="selected"' : '').'>'._T('gmap:jour').'</option>
						<option value="semaine"'.(($timeIntervalUnit === 'semaine') ? ' selected="selected"' : '').'>'._T('gmap:semaine').'</option>
						<option value="mois"'.(($timeIntervalUnit === 'mois') ? ' selected="selected"' : '').'>'._T('gmap:mois').'</option>
					</select><br />
		<input type="checkbox" name="meme_parent" id="'.$mapId.'_siblings_same_parent" value="oui"'.($meme_parent ? ' checked="checked"' : '').' />&nbsp;<label for="meme_parent">'._T('gmap:choix_siblings_same_parent').'</label><br /><br />
		<label for="limite">'._T('gmap:choix_siblings_limit').'</label>&nbsp;<input type="text" class="text" size="5" name="limite" id="'.$mapId.'_sibling_limit" value="'.$limite.'" />
		<input type="button" name="'.$mapId.'_search_siblings" id="'.$mapId.'_search_siblings" value="'._T('gmap:address_btn_find').'" style="float:right;" />
	</div>';

	$parts['script'] = '
	function DDX_'.$mapId.'_SiblingsSubForm(tool, mapId, params)
	{
		var root = jQuery("#"+mapId+"_SiblingsSubForm");
		params["limite_vue"] = (jQuery(\'input[name="limite_vue"]:checked\', root).val() == "oui") ? "oui" : "non";
		if (params["limite_vue"] === "oui")
		{
			var tools = GeolocTools.obj("'.$mapId.'");
			if (isObject(tools))
			{
				var bounds = tools.getViewportBounds();
				if (bounds)
				{
					params["bounds_min_lat"] = bounds.min_lat;
					params["bounds_min_lng"] = bounds.min_lng;
					params["bounds_max_lat"] = bounds.max_lat;
					params["bounds_max_lng"] = bounds.max_lng;
				}
			}
		}
		params["interval_temps"] = (jQuery(\'input[name="interval_temps"]:checked\', root).val() == "oui") ? "oui" : "non";
		params["interval"] = jQuery(\'input[name="interval"]\', root).val();
		params["unite_interval"] = jQuery(\'select[name="unite_interval"] option:selected\', root).val();
		params["meme_parent"] = (jQuery(\'input[name="meme_parent"]:checked\', root).val() == "oui") ? "oui" : "non";
		params["limite"] = jQuery(\'input[name="limite"]\', root).val();
		params["focus"] = (params["limite_vue"] === "oui") ? false : true;
	}';

	$parts['script_ready'] = '
	GeolocGenericTool.obj("'.$mapId.'", "siblings").initialize({
					},{
						title: { name: "'._T('gmap:titre').'", numeric: false },
						distance: { name: "'._T('gmap:distance_temporelle').'", numeric: false },
						latitude: { name: "'._T('gmap:latitude').'", numeric: true },
						longitude: { name: "'._T('gmap:longitude').'", numeric: true },
						zoom: { name: "'._T('gmap:zoom').'", numeric: true },
						type_point: { name: "'._T('gmap:marker_type').'", numeric: false }
					},
					"'.generer_url_ecrire("geoloc_requete_siblings").'",
					{
						objet: "'.$args['objet'].'",
						id_objet: '.$args['id_objet'].'
					}, DDX_'.$mapId.'_SiblingsSubForm);';
	
	return $parts;
}

?>
