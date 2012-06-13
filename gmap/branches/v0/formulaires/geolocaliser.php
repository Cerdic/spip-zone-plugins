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

include_spip('inc/gmap_presentation');
include_spip('inc/gmap_db_utils');
include_spip('inc/gmap_saisie_utils');
include_spip('inc/gmap_geoloc');
include_spip('gmap_filtres');

// Ajout de la carte clicable
function gmap_ajoute_carte_edit($parts, $table, $id, $mapId, $divId)
{
	// Ajouter un DIV qui va recevoir la carte
	$parts['html'] .= '
<div id="'.$divId.'" class="carte_editer_gmap"></div>';
	
	// Lecture des paramètres de la carte
	$parts['script'] .= gmap_definir_parametre_carte($table, $id, $mapId.'.mapParams', null, 'prive');
	
	// Partie de script sans code PHP
	$parts['script'] .= '
// Créer les icones pour la partie privée
'.$mapId.'.createIcons = function(map)
{
	map.setIcon("editMarker", '.gmap_definition_icone('priveEdit').');
	map.setIcon("activeMarker", '.gmap_definition_icone('priveActive').');
	map.setIcon("siblingMarker", '.gmap_definition_icone('priveSibling').');
	map.setIcon("activeSiblingMarker", '.gmap_definition_icone('priveSiblingActive').');
};

// Chargement de la carte et mise en place des gestionnaire d\'évènement
'.$mapId.'.load = function(mapId, divId, mapParams)
{
	// Récupérer de la carte
	var map = MapWrapper.getMap(mapId, true);
	if (!isObject(map))
		return false;
	
	// Déclarer les icones qui vont être utilisées
	this.createIcons(map);
		
	// Chargement de la carte
	mapParams.handleResize = true;
	mapParams.mergeInfoWindows = false;
	if (!map.load(divId, mapParams))
		return false;

	return true;
};

// Gestion de document.ready
'.$mapId.'.onCarteDocumentReady = function()
{
	// Vérifier que la div est bien là pour éviter une erreur javascript...
	var host = document.getElementById("'.$divId.'");
	if (host)
	{
		// Charger la carte
		if (!isObject(gMap("'.$mapId.'")) || !gMap("'.$mapId.'").isLoaded())
			'.$mapId.'.load("'.$mapId.'", "'.$divId.'", '.$mapId.'.mapParams);
	}
};

// Fermeture de la page
'.$mapId.'.onCarteUnload = function()
{
	if (isObject(gMap("'.$mapId.'")))
		MapWrapper.freeMap("'.$mapId.'");
};
';

	$parts['script_ready'] .= '
	if (typeof('.$mapId.'.onCarteDocumentReady) === "function")
		'.$mapId.'.onCarteDocumentReady();';
	$parts['script_unload'] .= '
	if (typeof('.$mapId.'.onCarteUnload) === "function")
		'.$mapId.'.onCarteUnload();';

	return $parts;
}

// Ajout de la liste de marqueurs éditables, et des évènements sur la carte
function _fillTypesSelect($name, $style, $types, $current, $indent)
{
	$out = "";
	$out .= $indent . '<select name="'.$name.'" '.$style.' size="1">' . "\n";
	foreach ($types as $type)
		$out .= $indent . '	<option value="'.$type['nom'].'"'.(!strcmp($type['nom'], $current) ? ' selected="selected"' : '').'>'.$type['nom'].'</option>' . "\n";
	$out .= $indent . '</select>' . "\n";
	return $out;
}
function gmap_ajoute_liste_marqueurs_edit($parts, $table, $id, $mapId, $divId)
{
	// Récupérer les points sur l'objet
	$markers = gmap_get_points($table, $id);
	if (!$markers || !is_array($markers))
		$markers = array();
	$markers_count = 0;
	$active_marker = -1; // S'il n'y en a aucun, ce sera le premier créé, i.e. l'ID -1
	foreach ($markers as $marker)
	{
		$markers_count++;
		if ($active_marker == -1)
			$active_marker = $marker['id'];
	}
	
	// Récupérer la liste des types
	$types = gmap_get_types($table);

	// Code HTML des marqueurs
	$parts['html'] .= '
	<div class="geoloc-sous-bloc">
		<fieldset>
			<legend>Nouvelles coordonn&eacute;es</legend>
			<input type="hidden" name="markers_count" id="markers_count_'.$mapId.'" value="'.$markers_count.'" />
			<input type="hidden" name="active_marker" id="active_marker_'.$mapId.'" value="'.$active_marker.'" />
			<table cellspacing="0" cellpadding="0" align="center">
			<tr><td>
				<table id="markers_set_'.$mapId.'" class="edit_markers" cellspacing="0" cellpadding="0" align="center">
					<tbody>
						<tr class="header"><th>&nbsp;</th><th>'._T('gmap:latitude').'</th><th>'._T('gmap:longitude').'</th><th>'._T('gmap:zoom').'</th><th>'._T('gmap:marker_type').'</th><th>&nbsp;</th><th>&nbsp;</th></tr>';
	foreach ($markers as $marker)
	{
		$parts['html'] .= '
						<tr class="marker">
							<td><input class="marker_id" type="hidden" name="marker_id[]" value="'.$marker['id'].'" /><input class="marker_state" type="hidden" name="marker_state[]" value="" /><span class="btn_activate"></span></td>
							<td><input class="marker_lat text" type="text" name="marker_lat[]" value="'.$marker['latitude'].'" size="12" style="width:80px;" /></td>
							<td><input class="marker_long text" type="text" name="marker_long[]" value="'.$marker['longitude'].'" size="12" style="width:80px;" /></td>
							<td><input class="marker_zoom text" type="text" name="marker_zoom[]" value="'.$marker['zoom'].'" size="6" style="width:30px;" /></td>
							<td>'._fillTypesSelect('marker_type[]', 'class="marker_type text" style="width:80px;"', $types, $marker['type'], '							').'</td>
							<td><span class="btn_marker_delete"></span></td>
							<td><span class="btn_marker_validate"></span></td>
						</tr>';
	}
	$parts['html'] .= '
					</tbody>
				</table>
			</td></tr>
			<tr><td>
				<table id="markers_template_'.$mapId.'" style="display:none;">
					<tbody>
						<tr class="marker">
							<td><input class="marker_id" type="hidden" name="marker_id[]" value="-1" /><input class="marker_state" type="hidden" name="marker_state[]" value="created" /><span class="btn_activate"></span></td>
							<td><input class="marker_lat text" type="text" name="marker_lat[]" value="0.0" size="12" style="width:80px;" /></td>
							<td><input class="marker_long text" type="text" name="marker_long[]" value="0.0" size="12" style="width:80px;" /></td>
							<td><input class="marker_zoom text" type="text" name="marker_zoom[]" value="1" size="6" style="width:30px;" /></td>
							<td>'._fillTypesSelect('marker_type[]', 'class="marker_type text" style="width:80px;"', $types, 'defaut', '							').'</td>
							<td><span class="btn_marker_delete"></span></td>
							<td><span class="btn_marker_validate"></span></td>
						</tr>
					</tbody>
				</table>
			</td></tr>
			<tr><td id="markers_set_cmds_'.$mapId.'" class="markers_set_cmds"><a class="btn_marker_add" href="#">'._T('gmap:add_marker').'</a></td></tr>
			</table>
		</fieldset>
	</div>';

	// Méthodes pratiques pour ajouter et mettre à jour les marqueurs sur la carte
	// Elles pourraient dans une certaine mesure être factorisée dans un fichier .js
	// indépendant, je n'en ai pas pris la peine...)
	$parts['script'] .= '';
	
	// Mise en place des listeners sur la carte et les marqueurs
	$parts['script'] .= '';
	
	$parts['script_ready'] .= '
	EditMarkers.obj("'.$mapId.'").initialize("'.$divId.'", '.(gmap_capability('dragmarkers') ? 'true' : 'false').', {
			titre_marqueur_actif: "'.protege_titre(_T('gmap:titre_marqueur_actif')).'",
			titre_marqueur_edit: "'.protege_titre(_T('gmap:titre_marqueur_edit')).'"
		});';
		
	return $parts;
}

// Ajout des outils de copie
function gmap_ajoute_copy_tools($parts, $table, $id, $mapId, $divId)
{
	// Récupérer les outils disponibles
	$tools = pipeline('gmap_outils_geoloc', array(
			'args'=>array('objet'=>$table, 'id_objet'=>$id, 'mapId'=>$mapId, 'divId'=>$divId),
			'data'=>array()));
	if (!$tools || !is_array($tools))
		return $parts;
	$options = '';
	$html = '';
	$script = '';
	$script_ready = '';
	foreach ($tools as $tool=>$params)
	{
		if (!strlen($params['name']) || !strlen($params['html']))
			continue;
		$options .= '
		<option value="'.$tool.'">'.$params['name'].'</option>';
		$html .= '
	<div id="tool_'.$tool.'_content_'.$mapId.'" class="tool-content tool-'.$tool.'" style="display: none;">'.$params['html'].'
	</div>';
		$script .= $params['script'];
		$script_ready .= $params['script_ready'];
	}
	if (!strlen($options) || !strlen($html))
		return $parts;
		
	// Script simple
	$parts['script'] .= $script;

	// Script dans le document.ready
	$parts['script_ready'] .= '
	GeolocTools.obj("'.$mapId.'").initialize({
		tool_no_results: "'._T('gmap:geocoder_no_results').'",
		copier_point: "'._T('gmap:copier_point').'",
		lier_point: "'._T('gmap:lier_point').'"});'.$script_ready;
	
	// Code HTML
	$choix = '<select id="toolId'.$mapId.'" name="toolId'.$mapId.'" class="toolsId" size="1">';
	$choix .= '
		<option value="none" selected="selected"></option>';
	$choix .= $options;
	$choix .= '</select>';
	$html = '
	<div id="tools_list_'.$mapId.'" class="tool-container">'.$html.'
	</div>
	<div id="tool_result_'.$mapId.'" class="tool-results"></div>';
	
	// Mécanisme du bouton dépliable sur la partie HTML
	$parts['html'] .= gmap_sous_bloc_depliable("tools_".$mapId, _T('gmap:formulaire_outils')." ".$choix, $html, $mapId, "geoloc-sous-bloc");
	
	return $parts;
}



// Affichage du formulaire de géolocalisation d'un objet SPIP
function formulaires_geolocaliser_dist($id = NULL, $table = NULL, $exec = NULL, $deplie = 0)
{
	// La carte comprend plusieurs parties : la carte, les siblings, le geocoder..
	// Si on fait tout écrire d'un bloc, il y a des problèmes de synchronisation
	// (des handlers d'évènements sont inscrits après que les évènements aient
	// été envoyés), donc, pour simplifier le problème, on va décomposer la 
	// sortie : code HTML, fonctions javascript, comportement sur document ready...
	// Le comportement sur document ready est un peu spécial car l'enchaînement
	// des opérations est critique, c'est donc géré dans cette fonction, à partir
	// de fonctions globales délcarées avant...
	$parts = array('html' => "", 'script' => "");
	
	
	//// Mécanique de début, liée à la fonction formulaires_geolocaliser_dist

	// Récupérer le contexte de la requête (s'il n'est pas passé en paramètres)
	if ($table == NULL)
		$table = _request('geoobject');
	if ($id == NULL)
		$id = _request('geoobject_id');
	if ($exec == NULL)
		$exec = _request('source_exec');
	
	// État de l'interface
	if (_request('ui_state'))
	{
		$strStateUI = @html_entity_decode(_request('ui_state'));
		$stateUI = @unserialize($strStateUI);
	}
	else
	{
		$stateUI = array();
		$stateUI['deplie'] = $deplie;
		$strStateUI = @serialize($stateUI);
	}
	
	// Sanity check : on doit avoir un objet, un id
	if (!$table || !$id)
		return "";

	// Déterminer ici les identifiants uniques de la carte et des divs
	$mapId = "CarteEdit".$table.$id;
	$divId = "mapEditerGmap".$table.$id;
	$ajaxDivId = "geolocaliser-".$table.$id;
	
	// Début de la partie HTML liée à GMap
	$parts['html'] .= '
<!-- Plugin GMap : géolocalisation de l\'objet '.$table.' '.$id.' -->';
	
	// Si on a le résultat d'un traitement, l'afficher ici
	$parts['html'] .= gmap_decode_result("msg_result");
	
	// Transmettre les paramètres de la méthode pour les retrouver quand elle sera réappellée
	$parts['html'] .= '
<input name="geoobject" type="hidden" value="'.$table.'" />
<input name="geoobject_id" type="hidden" value="'.$id.'" />
<input name="source_exec" type="hidden" value="'.$exec.'" />
<input id="ui_state_'.$mapId.'" name="ui_state" type="hidden" value="'.@htmlentities($strStateUI).'" />';

	// Initialiser le script en déclarant un espace de nom dédié à cette carte
	$parts['script'] .= '
<script type="text/javascript">
//<![CDATA[
	function '.$mapId.'() // espace de nom de cette instance de carte
	{
	};
	'.$mapId.'.mapId = "'.$mapId.'";
	'.$mapId.'.divId = "'.$divId.'";';
	
	// Carte
	$parts = gmap_ajoute_carte_edit($parts, $table, $id, $mapId, $divId);
	
	// Ajout des outils génériques 
	$parts = gmap_ajoute_copy_tools($parts, $table, $id, $mapId, $divId);
	
	// Liste des marqueurs de l'objet
	$parts = gmap_ajoute_liste_marqueurs_edit($parts, $table, $id, $mapId, $divId);


	//// Association des éléments
	
	// Ajouter à la fin du script la partie sur document.ready
	$parts['script'] .= '
'.$mapId.'.onGeolocShow = function()
{';
	if ($parts['script_ready'] && strlen($parts['script_ready']))
		$parts['script'] .= $parts['script_ready'];
	$parts['script'] .= '
	
	// Quand la demande jQuery part, il faut détruire la carte pour qu\'elle soit
	// correctement recréée sur le document.ready qui interviendra à sa complétion
	jQuery("#'.$ajaxDivId.'").ajaxSend(function(evt, request, settings)
	{
		if (jQuery(this).isAjaxTarget(settings))
			'.$mapId.'.onGeolocUnload();
	});
};
'.$mapId.'.onGeolocUnload = function()
{';
	if ($parts['script_unload'] && strlen($parts['script_unload']))
		$parts['script'] .= $parts['script_unload'];
	$parts['script'] .= '
};
jQuery(document).ready(function()
{
	// Afficher la carte
	'.$mapId.'.onGeolocShow();
	
	// Gestion des sous-sections dépliantes
	jQuery("#'.$divId.'").bind("gmap_depliantHide", function()
	{
		setUIState("ui_state_'.$mapId.'", "deplie", 0);
	});
	jQuery("#'.$divId.'").bind("gmap_depliantShow", function()
	{
		if (isObject(gMap("'.$mapId.'")))
		{
			gMap("'.$mapId.'").onResize();
			var info = EditMarkers.obj("'.$mapId.'").getActiveMarkerInfo();
			if (isObject(info))
				gMap("'.$mapId.'").setCenter(info["latitude"], info["longitude"]);
		}
		setUIState("ui_state_'.$mapId.'", "deplie", 1);
	});
});
jQuery(document).unload(function()
{
	'.$mapId.'.onGeolocUnload();
});';

	// Finaliser la partie script
	$parts['script'] .= '
//]]>
</script>'."\n";

	// Initialiser le source final
	$corps = $parts['html'] . "\n" . $parts['script'];
	$corps .= '
<!-- Fin de la géolocalisation de l\'objet '.$table.' '.$id.' -->
';
	
	// Renvoyer le tout dans un formulaire ajax
//	return gmap_formulaire_ajax('editer_gmap', 'geolocaliser', $exec, $corps,
//		find_in_path('images/logo-formulaire.png'),
//		"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"._T('gmap:formulaire_geolocaliser'), _T('gmap:bouton_geolocaliser'),
//		$stateUI['deplie'], $divId);
// cf. dater.php
	$corps = ajax_action_post("geolocaliser", $table.$id, $exec, "id_$table=$id", $corps, _T('gmap:bouton_geolocaliser'));
	$corps = gmap_cadre_depliable(find_in_path('images/logo-formulaire.png'),
		"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"._T('gmap:formulaire_geolocaliser'), $stateUI['deplie'],
		$corps, "geolocaliser", $divId);
	return ajax_action_greffe("geolocaliser", $table.$id, $corps);
}

?>
