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

// Ajout de la carte clicable
function gmap_ajoute_carte_edit($parts, $table, $id, $mapId, $divId)
{
	// Ajouter un DIV qui va recevoir la carte
	$parts['html'] .= '
<div id="'.$divId.'" class="carte_editer_gmap"></div>';
	
	// Lecture des paramètres de la carte
	$parts['script'] .= gmap_definir_parametre_carte($table, $id, $mapId.'.mapParams', null);
	
	// Partie de script sans code PHP
	$parts['script'] .= '
// Créer les icones pour la partie privée
'.$mapId.'.createIcons = function(map)
{
	map.setIcon("editMarker", '.
		gmap_definir_parametre_icon(array('file'=>_DIR_PLUGIN_GMAP . 'images/priveEdit.png', 'xAnchor'=>11, 'yAnchor'=>32, 'xOffset'=>11, 'yOffset'=>10),
									array('file'=>_DIR_PLUGIN_GMAP . 'images/priveEdit-full.png', 'xAnchor'=>11, 'yAnchor'=>32)).');
	map.setIcon("activeMarker", '.
		gmap_definir_parametre_icon(array('file'=>_DIR_PLUGIN_GMAP . 'images/priveActive.png', 'xAnchor'=>11, 'yAnchor'=>32, 'xOffset'=>11, 'yOffset'=>10),
									array('file'=>_DIR_PLUGIN_GMAP . 'images/priveActive-full.png', 'xAnchor'=>11, 'yAnchor'=>32)).');
	map.setIcon("siblingMarker", '.
		gmap_definir_parametre_icon(array('file'=>_DIR_PLUGIN_GMAP . 'images/priveSibling.png', 'xAnchor'=>11, 'yAnchor'=>32, 'xOffset'=>11, 'yOffset'=>10),
									array('file'=>_DIR_PLUGIN_GMAP . 'images/priveSibling-full.png', 'xAnchor'=>11, 'yAnchor'=>32)).');
	map.setIcon("activeSiblingMarker", '.
		gmap_definir_parametre_icon(array('file'=>_DIR_PLUGIN_GMAP . 'images/priveSiblingActive.png', 'xAnchor'=>11, 'yAnchor'=>32, 'xOffset'=>11, 'yOffset'=>10),
									array('file'=>_DIR_PLUGIN_GMAP . 'images/priveSiblingActive-full.png', 'xAnchor'=>11, 'yAnchor'=>32)).');
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
	this.mapParams.handleResize = true;
	if (!map.load(divId, this.mapParams))
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
			'.$mapId.'.load("'.$mapId.'", "'.$divId.'", "'.$mapId.'.mapParams");
	}
};

// Fermeture de la page
'.$mapId.'.onCarteUnload = function()
{
	if (isObject(gMap("'.$mapId.'")))
		MapWrapper.freeMap("'.$mapId.'");
};
';

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
	<div style="clear:both;">
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
			<tr><td class="markers_set_cmds"><a class="btn_marker_add" href="#">'._T('gmap:add_marker').'</a></td></tr>
			</table>
		</fieldset>
	</div>';

	// Méthodes pratiques pour ajouter et mettre à jour les marqueurs sur la carte
	// Elles pourraient dans une certaine mesure être factorisée dans un fichier .js
	// indépendant, je n'en ai pas pris la peine...)
	$parts['script'] .= '

// ID du marker actif
'.$mapId.'.getActiveMarkerID = function()
{
	var edit = jQuery("#active_marker_'.$mapId.'");
	if (!isObject(edit))
		return 0;
	return parseInt(edit.val());
};
'.$mapId.'.setActiveMarkerID = function(id)
{
	var edit = jQuery("#active_marker_'.$mapId.'");
	if (isObject(edit))
		edit.val(id);
};

// Nombre de marqueurs
'.$mapId.'.getMarkersCount = function()
{
	var edit = jQuery("#markers_count_'.$mapId.'");
	if (!isObject(edit))
		return -1;
	return parseInt(edit.val());
};
'.$mapId.'.setMarkersCount = function(count)
{
	var edit = jQuery("#markers_count_'.$mapId.'");
	if (isObject(edit))
		edit.val(count);
};

// Information sur le marqueur actif
'.$mapId.'.getActiveMarkerInfo = function()
{
	var id = this.getActiveMarkerID();
	var bloc = jQuery("#markers_set_'.$mapId.'");
	var row = bloc.getMarkerRow(id);
	if (isObject(row))
		return row.getMarkerRowInfo();
	else
		return null;
};

// Mise à jour d\'un marqueur
'.$mapId.'.updateMarker = function(row, map, bDoNotUpdatePosition)
{
	var isActive = (row.hasClass("active")) ? true : false;
	var isDeleted = (row.hasClass("deleted")) ? true : false;
	var info = row.getMarkerRowInfo();
	if (isDeleted)
	{
		map.removeMarker(info["id"]);
	}
	else
	{
		var params = {
			latitude: info["latitude"],
			longitude: info["longitude"],
		};
		if (isActive)
		{
			params["title"] = "'.addslashes(_T('gmap:titre_marqueur_actif')).'";
			params["icon"] = "activeMarker";
			params["zorder"] = 20;
			params["draggable"] = true;
		}
		else
		{
			params["title"] = "'.addslashes(_T('gmap:titre_marqueur_edit')).'";
			params["icon"] = "editMarker";
			params["zorder"] = 10;
			params["draggable"] = false;
		}
		if (bDoNotUpdatePosition !== true)
			map.setMarker(info["id"], params);
		if (isActive)
		{
			map.panTo(info["latitude"], info["longitude"]);
			map.setZoom(info["zoom"]);
		}
	}
};

// Ajout de tous les marqueurs définis
'.$mapId.'.updateMarkers = function(bloc, map)
{
	// Mise à jour des marqueurs
	var activeRowID = 0;
	var count = 0;
	jQuery("tr.marker", bloc).each(function()
	{
		var row = jQuery(this);
		if (row.hasClass("active"))
			activeRowID = row.getMarkerRowID();
		count++;
		'.$mapId.'.updateMarker(row, map);
	});
	
	// Mise  jour de l\'information globale
	this.setActiveMarkerID(activeRowID);
	this.setMarkersCount(count);
};

// Modification de la position du marqueur actif
'.$mapId.'.setActiveMarkerPosition = function(bloc, map, latitude, longitude)
{
	var markerID = this.getActiveMarkerID();
	if (markerID != 0)
	{
		var row = bloc.getMarkerRow(markerID);
		if (isObject(row))
		{
			row.setMarkerPosition(latitude, longitude);
			this.updateMarker(row, map);
		}
	}
};

// Modification du zoom du marqueur actif
'.$mapId.'.setActiveMarkerZoom = function(bloc, map, zoom)
{
	var markerID = this.getActiveMarkerID();
	if (markerID != 0)
	{
		var row = bloc.getMarkerRow(markerID);
		if (isObject(row))
		{
			row.setMarkerZoom(zoom);
			this.updateMarker(row, map);
		}
	}
};
';
	
	// Mise en place des listeners sur la carte et les marqueurs
	$parts['script'] .= '
// Activation des listeners
// Sur gmapReady, mais à positionné seulement quand le document est prêt
// sinon le div n\'existe pas encore en retour de Ajax.
'.$mapId.'.onMarkersDocumentReady = function()
{
	jQuery("#'.$divId.'").gmapReady(function()
	{
		var bloc = jQuery("#markers_set_'.$mapId.'");
		var map = gMap("'.$mapId.'");
		if (!isObject(bloc) || !isObject(map))
			return;
		
		// Fonctionnement de la liste de marqueurs
		bloc.setMarkerTriggers(null, bloc);
		bloc.checkForActiveRow(bloc);
		jQuery(\'.btn_marker_add\').setAddMarkerAction(bloc, function()
		{
			return map.getViewport();
		}, "markers_template_'.$mapId.'", bloc);

		// Créer les marqueurs
		'.$mapId.'.updateMarkers(bloc, map);
		
		// Évènements de la liste des marqueurs vers la carte
		bloc.bind(\'marker_created\', function(event, markerID)
		{
			'.$mapId.'.updateMarkers(bloc, map);
		});
		bloc.bind(\'marker_deleted\', function(event, markerID)
		{
			'.$mapId.'.updateMarkers(bloc, map);
		});
		bloc.bind(\'active_marker_changed\', function(event, ids)
		{
			'.$mapId.'.setActiveMarkerID(ids.active);
			'.$mapId.'.updateMarkers(bloc, map);
		});
		
		// Évènements de la carte vers la liste de marqueurs
		map.addListener("click-on-map", function(event, latitude, longitude)
		{
			'.$mapId.'.setActiveMarkerPosition(bloc, map, latitude, longitude);
		});
		map.addListener("zoom", function(event, zoom)
		{
			var markerID = '.$mapId.'.getActiveMarkerID();
			if (markerID != 0)
			{
				var row = bloc.getMarkerRow(markerID);
				if (isObject(row))
					row.setMarkerZoom(zoom);
			}
		});';
	if (gmap_capability('dragmarkers'))
		$parts['script'] .= '
		map.addListener("drop-marker", function(event, id, latitude, longitude)
		{
			var row = bloc.getMarkerRow(id);
			if (isObject(row))
			{
				row.setMarkerPosition(latitude, longitude);
				'.$mapId.'.updateMarker(row, map, true);
			}
		});';
	$parts['script'] .= '
	});
};';
	
	return $parts;
}

// Recherche des marqueurs des objets voisins
function gmap_get_siblings_markers($id, $table, $limit = 6, $bSameParent = false)
{
    // Initialisation du retour
    $markers = NULL;
    
    // Pour les articles, sélectionner les articles de la même rubrique et espacés de moins d'une semaine
    if ($table == 'article')
    {
        // Récupérer les infos sur l'article
	    if (($rowset = sql_select("id_rubrique, date", "spip_articles", "id_article = $id")) &&
            ($row = sql_fetch($rowset)))
        {
            // Requête pour récupérer les articles géoréférencés et espacés de moins d'une semaine
			$where = array("articles.id_article<>" . $id);
			if ($bSameParent === true)
				$where[] = "articles.id_rubrique=" . $row['id_rubrique'];
			$rowset = sql_select(
				array(
					"articles.id_article AS id", "articles.titre AS titre", "articles.date AS date",
					"points.latitude AS coord_lat", "points.longitude AS coord_long", "points.zoom AS zoom", "types.nom AS type",
					"ABS(DATEDIFF('" . $row['date'] . "', articles.date)) AS distdate"),
				"spip_articles AS articles".
				" JOIN spip_gmap_points_liens AS liens ON (articles.id_article = liens.id_objet AND liens.objet = 'article')".
				" JOIN spip_gmap_points AS points ON points.id_point = liens.id_point".
				" JOIN spip_gmap_types AS types ON points.id_type_point = types.id_type_point",
				$where,
				"", "distdate ASC", $limit);
			// L'alias sur les noms des tables est nécessaire pour contourner une faille de spip dans la
			// transposition des nom de tables : un nom de table précédent d'une parenthèse n'est pas
			// transposé (cf. _SQL_PREFIXE_TABLE dans ecrire/req/mysql.php).
			while ($row = sql_fetch($rowset))
			{
				if ($markers == NULL)
				{
					$markers = array();
					$keys = array();
				}
				$keys[] = $row['date'];
				$markers[] = array(
					'objet'=>'article',
					'id'=>$row['id'],
					'desc'=>$row['titre'],
					'lat'=>$row['coord_lat'],
					'long'=>$row['coord_long'],
					'zoom'=>$row['zoom'],
					'type'=>$row['type'],
					'html'=>gmap_get_object_info_contents('article', $row['id'], $row['type'])
					);
			}
			if ($keys && $markers)
				array_multisort($keys, SORT_ASC, $markers);
        }
    }
    
    // Pour les documents, sélectionner les document du même article et espacés de moins d'une heure
    else if ($table == 'document')
    {
        // Récupérer les infos du documents
	    if (($rowset = sql_select("spip_documents_liens.id_objet AS id_article, spip_documents.date AS date", "spip_documents_liens JOIN spip_documents ON spip_documents_liens.id_document = spip_documents.id_document", "spip_documents_liens.id_document=$id AND spip_documents_liens.objet='article'")) &&
            ($row = sql_fetch($rowset)))
        {
            // Requête pour récupérer les documents géoréférencés et espacés de moins de deux heures
			$where = array("spip_documents_liens.id_document<>" . $id);
			if ($bSameParent === true)
				$where[] = "spip_documents_liens.objet='article' AND spip_documents_liens.id_objet=" . $row['id_article'];
			$rowset = sql_select(
				array(
					"spip_documents_liens.id_document AS id", "spip_documents.titre AS titre", "spip_documents.fichier AS fichier", "spip_documents.date AS date",
					"spip_gmap_points.latitude AS coord_lat", "spip_gmap_points.longitude AS coord_long", "spip_gmap_points.zoom AS zoom", "spip_gmap_types.nom AS type",
					"ABS(TIMEDIFF('" . $row['date'] . "', spip_documents.date)) AS distdate"),
				"spip_documents_liens JOIN spip_documents ON spip_documents_liens.id_document = spip_documents.id_document JOIN spip_gmap_points_liens ON (spip_documents_liens.id_document = spip_gmap_points_liens.id_objet AND spip_gmap_points_liens.objet = 'document') JOIN spip_gmap_points ON spip_gmap_points.id_point = spip_gmap_points_liens.id_point JOIN spip_gmap_types ON spip_gmap_points.id_type_point = spip_gmap_types.id_type_point",
				$where,
				"",	"distdate ASC", $limit);
			while ($row = sql_fetch($rowset))
    	    {
				if ($markers == NULL)
				{
					$markers = array();
					$keys = array();
				}
				$keys[] = $row['date'];
				$markers[] = array(
					'objet'=>'document',
					'id'=>$row['id'],
					'desc'=>$row['titre'] ? $row['titre']." (".$row['fichier'].")" : $row['fichier'],
					'lat'=>$row['coord_lat'],
					'long'=>$row['coord_long'],
					'zoom'=>$row['zoom'],
					'type'=>$row['type'],
					'html'=>gmap_get_object_info_contents('document', $row['id'], $row['type'])
				   );
            }
			if ($keys && $markers)
				array_multisort($keys, SORT_ASC, $markers);
        }
    }
    
    return $markers;
}

// Ajout du formulaire de choix des voisins
function gmap_ajoute_siblings_copy($parts, $table, $id, $mapId, $divId)
{
	// Récupérer les voisins
	$sibling_same_parent = (gmap_lire_config('gmap_edit_params', 'sibling_same_parent', "oui") === "oui") ? true : false;
	$siblings_limit = intval(gmap_lire_config('gmap_edit_params', 'siblings_limit', "5"));
	$markers = gmap_get_siblings_markers($id, $table, $siblings_limit, $sibling_same_parent);
	if (!$markers || (count($markers) == 0))
		return $parts;

	// Ajout du formulaire
	$markers_script = '';
	$html = '
<div class="geoedit_subform">
	<table id="siblings_set_'.$mapId.'" class="edit_markers edit_siblings" cellspacing="0" cellpadding="0" align="right">
		<tbody>
			<tr class="header"><th>&nbsp;</th><th>'._T('gmap:titre').'</th><th>'._T('gmap:latitude').'</th><th>'._T('gmap:longitude').'</th><th>'._T('gmap:zoom').'</th><th>&nbsp;</th></tr>';
		foreach ($markers as $index => $marker)
		{
			$html .= '
			<tr class="marker sibling">
				<td nowrap><input name="sibling_id[]" class="marker_id" type="hidden" value="'.$marker['id'].'"/><span class="btn_activate"></span></td>
				<td class="sibling_desc">'.htmlentities($marker['desc']).'</td>
				<td class="marker_lat" nowrap>'.$marker['lat'].'</td>
				<td class="marker_long" nowrap>'.$marker['long'].'</td>
				<td class="marker_zoom" nowrap>'.$marker['zoom'].'</td>
				<td nowrap><span class="'.$mapId.'_sibling_copy btn_marker_copy" nowrap></span></td>
			</tr>';
			$markers_script .= '
			map.setMarker("sibling_'.$marker['id'].'", {
				icon: "siblingMarker",
				zorder: 0,
				click: "custom",
				title: "'.addslashes(_T('gmap:titre_marqueur_voisin').$marker['desc']).'",
				latitude: '.$marker['lat'].',
				longitude: '.$marker['long'].',
				html: "'.$marker['html'].'"
			});' . "\n";
			
		}
	$html .= '
		</tbody>
	</table>
</div>'."\n";

	// Mécanisme du bouton dépliable sur la partie HTML
	$parts['html'] .= gmap_sous_bloc_depliable("siblings_".$mapId, _T('gmap:formulaire_voisins'), $html);
	
	// Scripts
	$parts['script'] .= '
// ID du voisin actif
'.$mapId.'.getActiveSiblingID = function()
{
	var bloc = jQuery("#siblings_set_'.$mapId.'");
	return "sibling_"+bloc.getActiveRowID();
};
'.$mapId.'.setActiveSiblingID = function(id)
{
	var bloc = jQuery("#siblings_set_'.$mapId.'");
	if (typeof(id) === "string")
	{
		var pos = id.search(/[0-9]/);
		if (pos < 0)
			return false;
		id = parseInt(id.substring(pos));
	}
	bloc.setActiveRow(id, bloc);
};

// Initialisation
'.$mapId.'.onSiblingsDocumentReady = function()
{
	// Carte prête : ajouter les handlers d\'évènement sur les marqueurs
	jQuery("#'.$divId.'").gmapReady(function()
	{
		var map = gMap("'.$mapId.'");
		if (isObject(map))
		{
			var id = 0;';
	$parts['script'] .= $markers_script;
	$parts['script'] .= '

			// Évènements de la carte vers la liste de marqueurs
			map.addListener("click-on-marker", function(event, markerId)
			{
				// Déplier le bloc s\'il était plié
				jQuery("#siblings_'.$mapId.'").each(function()
				{
					if (jQuery(this).hasClass("sbd_replie"))
					{
						jQuery(this).removeClass("sbd_replie");
						jQuery(this).addClass("sbd_deplie");
					}
				});
				
				// Rendre la ligne active
				'.$mapId.'.setActiveSiblingID(markerId);
			});
		}
	});
	
	// Changement de marqueur actif
	jQuery("#siblings_set_'.$mapId.'").bind("active_marker_changed", function(event, ids)
	{
		var map = gMap("'.$mapId.'");
		if (isObject(map))
		{
			// Changer l\'icone du marqueur actif
			if (isObject(ids.previous))
				map.setMarker("sibling_"+ids.previous, { icon: "siblingMarker", zorder: 0 });
			var markerID = "sibling_"+ids.active;
			map.setMarker(markerID, { icon: "activeSiblingMarker", zorder: 1 });
			
			// Afficher la bulle
			var params = map.getMarkerDefinition(markerID);
			map.showInfoWindow(markerID);
		}
	});
	
	// Action sur la sélection d\'une ligne active
	jQuery("#siblings_set_'.$mapId.' .btn_activate").click(function()
	{
		var row = jQuery(this).parents("tr.sibling");
		var id = row.getMarkerRowID();
		var bloc = jQuery("#siblings_set_'.$mapId.'");
		bloc.setActiveRow(id, bloc);
	});
	
	// Action sur le bouton copier sur les coordonnées
	jQuery(".'.$mapId.'_sibling_copy").click(function()
	{
		var map = gMap("'.$mapId.'");
		if (isObject(map))
			map.closeInfoWindow();
		var row = jQuery(this).parents("tr.sibling");
		var latitude = jQuery("td.marker_lat", row).text();
		var longitude =	jQuery("td.marker_long", row).text();
		var zoom =	jQuery("td.marker_zoom", row).text();
		if ((isObject(latitude) && (latitude != "")) && (isObject(longitude) && (longitude != "")))
		{
			'.$mapId.'.setActiveMarkerPosition(jQuery("#markers_set_'.$mapId.'"), gMap("'.$mapId.'"), Number(latitude), Number(longitude));
			'.$mapId.'.setActiveMarkerZoom(jQuery("#markers_set_'.$mapId.'"), gMap("'.$mapId.'"), Number(zoom));
		}
	});
};' . "\n";
	
	return $parts;
}

// Ajout du formulaire de choix par recherche sur le geocoder
function gmap_ajoute_geocoder($parts, $mapId)
{
	$parts['script'] .= '
'.$mapId.'.setMarkerPosition = function(mapId, latitude, longitude)
{
	this.setActiveMarkerPosition(jQuery("#markers_set_'.$mapId.'"), gMap(mapId), latitude, longitude);
};';
	$parts['html'] .= gmap_sous_bloc_geocoder($mapId, $mapId.".setMarkerPosition");
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
	
	// Clic sur les voisins
	if (($table == 'article') || ($table == 'document'))
		$parts = gmap_ajoute_siblings_copy($parts, $table, $id, $mapId, $divId);
	
	// Recherche par géocoder
	if (gmap_capability('geocoder'))
		$parts = gmap_ajoute_geocoder($parts, $mapId);
	
	// Liste des marqueurs de l'objet
	$parts = gmap_ajoute_liste_marqueurs_edit($parts, $table, $id, $mapId, $divId);


	//// Association des éléments
	
	// Ajouter à la fin du script la partie sur document.ready
	$parts['script'] .= '
'.$mapId.'.onGeolocShow = function()
{
	// Enregistrement des évènements carte pour les marqueurs et les voisins (avant la création de la carte)
	if (typeof('.$mapId.'.onMarkersDocumentReady) === "function")
		'.$mapId.'.onMarkersDocumentReady();
	if (typeof('.$mapId.'.onSiblingsDocumentReady) === "function")
		'.$mapId.'.onSiblingsDocumentReady();

	// Chargement de la carte
	if (typeof('.$mapId.'.onCarteDocumentReady) === "function")
		'.$mapId.'.onCarteDocumentReady();
	
	// Quand la demande jQuery part, il faut détruire la carte pour qu\'elle soit
	// correctement recréée sur le document.ready qui interviendra à sa complétion
	jQuery("#'.$ajaxDivId.'").ajaxSend(function(evt, request, settings)
	{
		if (jQuery(this).isAjaxTarget(settings) && (typeof('.$mapId.'.onCarteUnload) === "function"))
			'.$mapId.'.onCarteUnload();
	});
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
			var info = '.$mapId.'.getActiveMarkerInfo();
			if (isObject(info))
				gMap("'.$mapId.'").setCenter(info["latitude"], info["longitude"]);
		}
		setUIState("ui_state_'.$mapId.'", "deplie", 1);
	});
});
jQuery(document).unload(function()
{
	// Nettoyer proprement la carte
	if (typeof('.$mapId.'.onCarteUnload) === "function")
		'.$mapId.'.onCarteUnload();
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
