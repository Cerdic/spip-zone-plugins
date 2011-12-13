/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Scripts additionnels utilisés dans la partie privée uniquement
 *
 */



/*****************************************************************************/
/** GESTION DES TABLEAUX DE MARQUEURS                                       **/
/*****************************************************************************/

//// Fonctions de navigation

// Récupérer la ligne de l'objet jQuery appelant
jQuery.fn.getMarkerParentRow = function()
{
	return rowItem = this.parents('tr.marker');
};

// Récupérer l'ID de marqueur de l'objet jQuery appelant
jQuery.fn.getMarkerID = function()
{
	var rowItem = this.getMarkerParentRow();
	if ((typeof(rowItem) == 'undefined') || (rowItem == null) || (rowItem.length == 0))
		return 0;
	var idInput = jQuery('.marker_id', rowItem);
	if ((typeof(idInput) == 'undefined') || (idInput == null) || (idInput.length == 0))
		return 0;
	return idInput.val();
};

// Récupérer la ligne correspondant au marqueur spécifié
jQuery.fn.getMarkerRow = function(markerID)
{
	var row = null;
	jQuery('tr.marker', this).each(function()
	{
		var idInput = jQuery('.marker_id', jQuery(this));
		if ((typeof(idInput) != 'undefined') && (idInput != null) && (idInput.length > 0))
		{
			var test = idInput.val();
			if (parseInt(idInput.val()) == markerID)
			{
				row = jQuery(this);
				return false;
			}
		}
	});
	return row;
};

// Récupérer l'état d'une ligne
jQuery.fn.getMarkerRowState = function()
{
	var state = "";
	jQuery('.marker_state', this).each(function()
	{
		state = jQuery(this).val();
	});
	return state;
};

// Récupérer l'ID d'une ligne
jQuery.fn.getMarkerRowID = function()
{
	var id = "";
	jQuery('.marker_id', this).each(function()
	{
		id = parseInt(jQuery(this).val());
	});
	return id;
};

// Récupérer toutes les infos d'une ligne
jQuery.fn.getMarkerRowInfo = function()
{
	var info = new Array();
	info['id'] = this.getMarkerRowID();
	info['state'] = this.getMarkerRowState();
	var edit = null;
	if (isObject(edit = jQuery('.marker_lat', this).eq(0)))
		info['latitude'] = parseFloat(edit.val());
	if (isObject(edit = jQuery('.marker_long', this).eq(0)))
		info['longitude'] = parseFloat(edit.val());
	if (isObject(edit = jQuery('.marker_zoom', this).eq(0)))
		info['zoom'] = parseInt(edit.val());
	if (isObject(edit = jQuery('.marker_type', this).eq(0)))
		info['type'] = edit.val();
	return info;
};

// Fixer la position d'un marqueur
jQuery.fn.setMarkerGeoInfo = function(latitude, longitude, zoom)
{
	var edit = null;
	if (isObject(edit = jQuery('.marker_lat', this).eq(0)))
		edit.val(latitude);
	if (isObject(edit = jQuery('.marker_long', this).eq(0)))
		edit.val(longitude);
	if (isObject(edit = jQuery('.marker_zoom', this).eq(0)))
		edit.val(zoom);
};


//// Fonctions d'action

// Vérifier qu'il reste une ligne éditable et active, sinon en créer
// une.
jQuery.fn.checkForActiveRow = function(eventTarget, cmds)
{
	// Parcourir les lignes et en sortir la dernière éditable et celle qui
	// est active
	var lastRow = null;
	var activeRow = null;
	jQuery('tr.marker', this).each(function()
	{
		var row = jQuery(this);
		if (row.hasClass('active'))
			activeRow = row;
		var state = row.getMarkerRowState();
		if (state != "deleted")
			lastRow = row;
	});
	
	// S'il n'y a pas de ligne active, mais une éditable, mettre celle-ci
	// en active
	if ((activeRow == null) && (lastRow != null))
		this.setActiveRow(lastRow.getMarkerRowID(), eventTarget);
		
	// Sinon, si on n'a pas de ligne éditable, en ajouter une
	else if ((lastRow == null) && cmds && cmds.length)
		jQuery('.btn_marker_add', cmds).triggerHandler('click');
};

// Récupérer la ligne active et l'id du marqueur actif
jQuery.fn.getActiveRow = function()
{
	var activeRow = null;
	jQuery('tr.marker', this).each(function()
	{
		var row = jQuery(this);
		if (row.hasClass('active'))
			activeRow = row;
	});
	return activeRow;
};
jQuery.fn.getActiveRowID = function()
{
	var row = this.getActiveRow();
	if (!row)
		return null;
	return row.getMarkerRowID();
};

// Rendre une ligne active
jQuery.fn.setActiveRow = function(markerID, eventTarget)
{
	var row = this.getMarkerRow(markerID);
	if (row && row.length > 0)
	{
		var prevID = this.getActiveRowID();
		jQuery('tr.marker', this).removeClass('active');
		row.addClass('active');
		eventTarget.trigger('active_marker_changed', { active: markerID, previous: prevID });
	}
};

// Marquer une ligne comme détruite
jQuery.fn.setDeletedRow = function(markerID, eventTarget)
{
	// Récupérer la ligne
	var row = this.getMarkerRow(markerID);
	if ((row == undefined) || (row.length == 0))
		return false;
		
	// Récupérer l'état du marqueur
	var info = row.getMarkerRowInfo();
	var state = info['state'];

	// Si le marqueur est créé, on détruit toute la ligne
	if (state == "created")
	{
		// Supprimer la ligne
		row.remove();
		
		// Signaler la création
		eventTarget.trigger('marker_deleted', [markerID, state]);
	}
		
	// Sinon, on marque détruit, ou on démarque
	else
	{
		// Si la ligne est active, supprimer le flag "active"
		if (row.hasClass('active'))
			row.removeClass('active');
	
		// Gérer l'aspect de la ligne détruite
		row.toggleClass('deleted');
		jQuery('input.text', row).each(function()
		{
			if (row.hasClass('deleted'))
				jQuery(this).attr('disabled', 'disabled');
			else
				jQuery(this).removeAttr('disabled');
		});
		jQuery('select.text', row).each(function()
		{
			if (row.hasClass('deleted'))
				jQuery(this).attr('disabled', 'disabled');
			else
				jQuery(this).removeAttr('disabled');
		});
	
		// Gérer l'état du marqueur dans le formulaire
		jQuery('.marker_state', row).each(function()
		{
			if (row.hasClass('deleted'))
				jQuery(this).val('deleted');
			else
				jQuery(this).val('');
		});
		
		// Signaler
		if (row.hasClass('deleted'))
			eventTarget.trigger('marker_deleted', [markerID, state]);
		else
			eventTarget.trigger('marker_created', markerID);
	}

	// Mettre à jour la ligne active
	this.checkForActiveRow(eventTarget);
};

// Ajouter une ligne
jQuery.fn.addNewRow = function(cbInit, templateId, eventTarget)
{
	if (typeof(jQuery.fn.addNewRow.lastMarkerID) == 'undefined')
		jQuery.fn.addNewRow.lastMarkerID = 0;
		
	// Recopier le template
	this.append(jQuery('#'+templateId+' > tbody').contents().clone());
	
	// Récupérer la ligne et la mettre à jour
	var row = jQuery("tr.marker:last", this);
	jQuery.fn.addNewRow.lastMarkerID++;
	var markerID = -jQuery.fn.addNewRow.lastMarkerID;
	jQuery('.marker_id', row).each(function()
	{
		jQuery(this).val(markerID);
	});
	
	// Récupérer les valeurs par défaut du marqueur
	if (cbInit != null)
	{
		var vp = cbInit();
		row.setMarkerGeoInfo(vp['latitude'], vp['longitude'], vp['zoom']);
	}

	// Signaler la création
	eventTarget.trigger('marker_created', markerID);
	
	// Rendre la nouvelle ligne active
	this.setActiveRow(markerID, eventTarget);
	
	// Enregistrer les triggers
	this.setMarkerTriggers(row, eventTarget);
};

// Enregistrement des triggers
jQuery.fn.setMarkerTriggers = function(container, eventTarget)
{
	var bloc = this;
	if (!isObject(container))
		container = bloc;
	
	// Activation d'un marqueur
    jQuery('.btn_activate', container).click(function()
    {
		var markerID = jQuery(this).getMarkerID();
		bloc.setActiveRow(markerID, eventTarget);
		return false;
    });
	
	// Marquer le marqueur à détruire
    jQuery('.btn_marker_delete', container).click(function()
    {
		var markerID = jQuery(this).getMarkerID();
		bloc.setDeletedRow(markerID, eventTarget);
		return false;
    });
	
	// Valider la position du marqueur sur la carte
    jQuery('.btn_marker_validate', container).click(function()
    {
		var markerID = jQuery(this).getMarkerID();
		return false;
    });
};

// Changement de la position d'un marqueur
jQuery.fn.setMarkerPosition = function(latitude, longitude)
{
	var edit = null;
	if (isObject(edit = jQuery('.marker_lat', this).eq(0)))
		edit.val(latitude.toString());
	if (isObject(edit = jQuery('.marker_long', this).eq(0)))
		edit.val(longitude.toString());
};
jQuery.fn.setMarkerZoom = function(zoom)
{
	var edit = null;
	if (isObject(edit = jQuery('.marker_zoom', this).eq(0)))
		edit.val(zoom.toString());
};
jQuery.fn.changeMarkerID = function(id)
{
	var edit = null;
	if (isObject(edit = jQuery('.marker_id', this).eq(0)))
		edit.val(id.toString());
};

// Action d'ajout d'un marker
jQuery.fn.setAddMarkerAction = function(bloc, cbInit, templateId, eventTarget)
{
	jQuery(this)
		.attr("href", "javascript:void( 0 )")
		.click(function(){
			bloc.addNewRow(cbInit, templateId, eventTarget);
			return false;
		});
};



/*****************************************************************************/
/** SYSTÈME DES BLOCS DÉPLIANTS                                             **/
/*****************************************************************************/

function SousBlocDepliant(mapId, nom) {
	this.mapId = mapId;
	this.nom = nom;
}

SousBlocDepliant.blocs = new Array();
SousBlocDepliant.bloc = function(mapId, nom)
{
	var entry = mapId+'_'+nom;
	if (!isObject(SousBlocDepliant.blocs[entry]))
		SousBlocDepliant.blocs[entry] = new SousBlocDepliant(mapId, nom);
	return SousBlocDepliant.blocs[entry];
}

SousBlocDepliant.prototype.initialize = function()
{
	if (open = getUIState('ui_state_'+this.mapId, 'sb_'+this.nom, 0))
		this.open();
	else
		setUIState('ui_state_'+this.mapId, 'sb_'+this.nom, 0);

	var objThis = this;
	jQuery('#sbd_btn_'+this.nom+'_open').click(function(event)
	{
		objThis.open();
		event.preventDefault();
		return false;
	});
	
	jQuery('#sbd_btn_'+this.nom+'_close').click(function(event)
	{
		objThis.close();
		event.preventDefault();
		return false;
	});
};

SousBlocDepliant.prototype.open = function()
{
	var objThis = this;
	jQuery('#'+this.nom).each(function(){
		var container = jQuery(this);
		container.removeClass('sbd_replie');
		container.addClass('sbd_deplie');
		var titre = jQuery('.sbd_closed .titre-sous-bloc', container).detach();
		jQuery('.sbd_opened .sbd-titre', container).append(titre);
		setUIState('ui_state_'+objThis.mapId, 'sb_'+objThis.nom, 1);
	});
};

SousBlocDepliant.prototype.close = function()
{
	var objThis = this;
	jQuery('#'+this.nom).each(function(){
		var container = jQuery(this);
		container.removeClass('sbd_deplie');
		container.addClass('sbd_replie');
		var titre = jQuery('.sbd_opened .titre-sous-bloc', container).detach();
		jQuery('.sbd_closed .sbd-titre', container).append(titre);
		setUIState('ui_state_'+objThis.mapId, 'sb_'+objThis.nom, 0);
	});
};

SousBlocDepliant.prototype.checkOpen = function()
{
	var objThis = this;
	jQuery('#'+this.nom+'.sbd_replie').each(function(){
		var container = jQuery(this);
		container.removeClass('sbd_replie');
		container.addClass('sbd_deplie');
		var titre = jQuery('.sbd_closed .titre-sous-bloc', container).detach();
		jQuery('.sbd_opened .sbd-titre', container).append(titre);
		setUIState('ui_state_'+objThis.mapId, 'sb_'+objThis.nom, 1);
	});
};

SousBlocDepliant.prototype.checkClose = function()
{
	var objThis = this;
	jQuery('#'+this.nom+'.sbd_deplie').each(function(){
		var container = jQuery(this);
		container.removeClass('sbd_deplie');
		container.addClass('sbd_replie');
		var titre = jQuery('.sbd_opened .titre-sous-bloc', container).detach();
		jQuery('.sbd_closed .sbd-titre', container).append(titre);
		setUIState('ui_state_'+objThis.mapId, 'sb_'+objThis.nom, 0);
	});
};

SousBlocDepliant.prototype.showCommand = function(bShow)
{
	if (bShow)
	{
		jQuery('#sbd_btn_'+this.nom+'_open').show();
		jQuery('#sbd_btn_'+this.nom+'_close').show();
	}
	else
	{
		jQuery('#sbd_btn_'+this.nom+'_open').hide();
		jQuery('#sbd_btn_'+this.nom+'_close').hide();
	}
};




/*****************************************************************************/
/** GESTION DES OUTILS DE RECHERCHE DES POINTS                              **/
/*****************************************************************************/

function GeolocTools(mapId) {
	this.mapId = mapId;
	this.edit = null;
}

GeolocTools.objs = new Array();
GeolocTools.obj = function(mapId)
{
	if (!isObject(GeolocTools.objs[mapId]))
		GeolocTools.objs[mapId] = new GeolocTools(mapId);
	return GeolocTools.objs[mapId];
};

// Initialisation des scripts (sur document.ready)
GeolocTools.prototype.initialize = function(strings)
{
	this.strings = strings;
	this.edit = EditMarkers.obj(this.mapId);
	
	this.setTool('none', true);
	
	var objThis = this;
	jQuery("#toolId"+this.mapId).change(function()
	{
		var tool = jQuery(this).val();
		objThis.setTool(tool, false);
	});

	var map = gMap(this.mapId);
	map.addListener("click-on-marker", function(event, markerId)
	{
		var bloc = jQuery('#tool_result_'+objThis.mapId+' .geoloc-tool-results');
		var tool = objThis.getTool();
		var template = tool+'-(.*)';
		var matches = markerId.match(template);
		if ((typeof(markerId) === "string") && isObject(matches))
		{
			var id = matches[1];
			bloc.setActiveRow(id, bloc);
			map.showInfoWindow(markerId);
		}
	});
};

// Rechercher l'outil actif
GeolocTools.prototype.getTool = function()
{
	return jQuery("#toolId"+this.mapId+' option:selected').val();
}

// Sélectionner un outil dans la combo et l'afficher en dessous
GeolocTools.prototype.setTool = function(tool, bSelect)
{
	// Vider les marqueurs et les résultats
	this.emptyResults();
	
	// Sélectionner dans la combo (sauf si on en vient)
	if (bSelect)
	{
		jQuery("#toolId"+this.mapId+' option:selected').removeAttr('selected');
		jQuery("#toolId"+this.mapId+' option[value='+tool+']').attr('selected', 'selected');
	}
	
	// Mettre en place les divs
	var container = jQuery("#tools_list_"+this.mapId);
	jQuery(".tool-content", container).hide();
	if (!tool || !tool.length || (tool === 'none'))
	{
		SousBlocDepliant.bloc(this.mapId, 'tools_'+this.mapId).checkClose();
		SousBlocDepliant.bloc(this.mapId, 'tools_'+this.mapId).showCommand(false);
	}
	else
	{
		jQuery("#tool_"+tool+"_content_"+this.mapId, container).show();
		SousBlocDepliant.bloc(this.mapId, 'tools_'+this.mapId).showCommand(true);
		SousBlocDepliant.bloc(this.mapId, 'tools_'+this.mapId).checkOpen();
	}
};

// Récupérer la vue actuelle sur la carte
GeolocTools.prototype.getViewportBounds = function()
{
	var map = gMap(this.mapId);
	if (!isObject(map))
		return false;
	return map.getViewportBounds();
};

// Récupérer les données d'un marqueur
GeolocTools.prototype.getMarkerInfo = function(markerID)
{
	var bloc = jQuery('#tool_result_'+this.mapId+' .geoloc-tool-results');
	var row = bloc ? bloc.getMarkerRow(markerID) : null;
	if (!row)
		return null;
	
	var marker = new Array();
	marker['id'] = row.getMarkerRowID();
	var element;
	
	if ((element = jQuery("input.latitude", row).eq(0)) && element.length)
		marker['latitude'] = parseFloat(element.val());
	else if ((element = jQuery("td.latitude", row).eq(0)) && element.length)
		marker['latitude'] = parseFloat(element.text());
		
	if ((element = jQuery("input.longitude", row).eq(0)) && element.length)
		marker['longitude'] = parseFloat(element.val());
	else if ((element = jQuery("td.longitude", row).eq(0)) && element.length)
		marker['longitude'] = parseFloat(element.text());
		
	if ((element = jQuery("input.zoom", row).eq(0)) && element.length)
		marker['zoom'] = parseInt(element.val());
	else if ((element = jQuery("td.zoom", row).eq(0)) && element.length)
		marker['zoom'] = parseInt(element.text());
		
	return marker;
};

// Copier les données d'une ligne de résultat vers le marker actif en cours d'édition
GeolocTools.prototype.copyMarker = function(markerID)
{
	// Récupérer l'objet d'édition
	if (!isObject(this.edit))
		return false;

	// Récupérer l'info sur le point
	var marker = this.getMarkerInfo(markerID);
	if (!marker)
		return;
		
	// S'assurer qu'il y a un marker en cours d'édition
	this.edit.ensureActiveMarker();

	// Centrer et faire un zoom
	if (isObject(marker['latitude']) && isObject(marker['longitude']))
		this.edit.setActiveMarkerPosition(marker['latitude'], marker['longitude']);
	if (isObject(marker['zoom']))
		this.edit.setActiveMarkerZoom(marker['zoom']);
		
	return true;
};

// Relier le marqueur courant au point en cours d'édition
GeolocTools.prototype.linkToMarker = function(markerID)
{
	// Récupérer l'info sur le point
	var bloc = jQuery('#tool_result_'+this.mapId+' .geoloc-tool-results');
	var row = bloc ? bloc.getMarkerRow(markerID) : null;
	var marker = row ? row.getMarkerRowInfo() : null;
	if (!marker)
		return;
		
	// Il faut avoir un ID, les coordonnées et le zoom
	if (!isObject(marker['id']) ||
		!isObject(marker['latitude']) || !isObject(marker['longitude']) ||
		!isObject(marker['zoom']))
		return false;
		
	// Récupérer l'objet d'édition
	if (!isObject(this.edit))
		return false;
		
	// S'assurer qu'il y a un marker en cours d'édition
	this.edit.ensureActiveMarker();

	// Centrer et faire un zoom
	this.edit.setActiveMarkerPosition(marker['latitude'], marker['longitude']);
	this.edit.setActiveMarkerZoom(marker['zoom']);
	this.edit.changeActiveMarkerID(marker['id']);
		
	return true;
};

// Gestion des marqueurs des résultats
GeolocTools.markerSets = new Array();
GeolocTools.prototype._addMarker = function(tool, id, params)
{
	var map = gMap(this.mapId);
	params['icon'] = 'siblingMarker';
	params['icon_sel'] = 'activeSiblingMarker';
	params['click'] = 'custom';
	map.setMarker(tool+'-'+id, params);
	var markers = GeolocTools.markerSets[tool];
	if (!isObject(markers))
		markers = GeolocTools.markerSets[tool] = new Array();
	markers.push(id);
};
GeolocTools.prototype._dropMarkers = function(tool)
{
	if (!tool || !tool.length)
	{
		for (var tool in GeolocTools.markerSets)
			this._dropMarkers(tool);
		return;
	}
	var markers = GeolocTools.markerSets[tool];
	if (!isObject(markers))
		return;
	var map = gMap(this.mapId);
	for (var index = 0; index < markers.length; index++)
		map.removeMarker(tool+'-'+markers[index]);
	GeolocTools.markerSets[tool] = null;
	delete GeolocTools.markerSets[tool];
};
GeolocTools.prototype._setActiveMarker = function(tool, prevId, activeId, bShowInfo)
{
	var map = gMap(this.mapId);
	if (isObject(map))
	{
		// Change l'icone
		if (isObject(prevId))
			map.setMarker(tool+'-'+prevId, { icon: "siblingMarker", zorder: 0 });
		map.setMarker(tool+'-'+activeId, { icon: "activeSiblingMarker", zorder: 100 });
		
		// Afficher la bulle ?
		if (bShowInfo)
			map.showInfoWindow(tool+'-'+activeId);
	}
};
GeolocTools.prototype._setMarkersTriggers = function(tool, bloc, container)
{
	var objThis = this;
	
	// Fonctionnement de la liste
	bloc.setMarkerTriggers(null, container);
    jQuery('.btn_activate', container).click(function()
	{
		var markerID = jQuery(this).getMarkerID();
		var map = gMap(objThis.mapId);
		if (isObject(map) && isObject(markerID))
			map.showInfoWindow(tool+'-'+markerID);
	});
	
	// Changement du marqueur actif par click sur son icone dans la table
	container.bind('active_marker_changed', function(event, ids)
	{
		objThis._setActiveMarker(tool, ids.previous, ids.active, false);
	});
	
	// Marquer le marqueur à détruire
    jQuery('.btn_marker_copy', container).click(function()
    {
		var markerID = jQuery(this).getMarkerID();
		objThis.copyMarker(markerID);
		return false;
    });
	
	// Valider la position du marqueur sur la carte
    jQuery('.btn_marker_link', container).click(function()
    {
		var markerID = jQuery(this).getMarkerID();
		objThis.linkToMarker(markerID);
		return false;
    });
};

// Relier le marqueur courant au point en cours d'édition
GeolocTools.prototype.emptyResults = function(tool)
{
	for (var tool in GeolocTools.markerSets)
		this._dropMarkers(tool);
	jQuery('#tool_result_'+this.mapId).empty();
};
GeolocTools.prototype.setResults = function(tool, headers, results, bLinkable)
{
	// Supprimer tous les marqueurs
	this._dropMarkers(tool);
	
	// Récupérer le conteneur du résultat, le vider si nécessaire
	var divResults = jQuery('#tool_result_'+this.mapId);
	if (!isObject(results) || !results || !results.length)
	{
		divResults.empty();
		divResults.append('<p>'+this.strings['tool_no_results']+'</p>');
		return;
	}

	// Créer le tableau et l'entête
	var table = jQuery('<table class="edit_markers geoloc-tool-results" align="right"></table>');
	var rowHeader = jQuery('<tr class="header"></tr>');
	rowHeader.append('<th>&nbsp;</th>');
	for (var col in headers)
		rowHeader.append('<th>'+headers[col]['name']+'</th>');
	rowHeader.append('<th>&nbsp;</th>');
	if (bLinkable)
		rowHeader.append('<th>&nbsp;</th>');
	table.append(rowHeader);

	// Parcourir les resultats
	for (var line = 0; line < results.length; line++)
	{
		// Ajouter la ligne dans le tableau
		var row = jQuery('<tr class="marker result"></tr>');
		var id = isObject(results[line]['id']) ? results[line]['id'] : line;
		row.append('<td nowrap><input type="hidden" name="result_id[]" class="marker_id" value="'+id+'" /><span class="btn_activate"></span></td>');
		for (var col in headers)
		{
			var value = results[line][col];
			if ((col == 'latitude') || (col == 'longitude'))
			{
				if (typeof(value) != 'Number')
					value = parseFloat(value);
				value = value.toFixed(4);
			}
			row.append('<td class="'+col+'"'+(headers[col]['numeric'] ? ' nowrap' : '')+'><input type="hidden" class="'+col+'" value="'+results[line][col]+'" />'+value+'</td>');
		}
		row.append('<td class="cmd-copy"><a class="gtr-copy btn_marker_copy" title="'+this.strings['copier_point']+'"></a></td>');
		// Désactivé pour l'instant : attendre que la suppression soit mise à jour
		//if (bLinkable)
		//	row.append('<td class="cmd-link"><a class="gtr-link btn_marker_link" title="'+this.strings['lier_point']+'"></a></td>');
		table.append(row);
		
		// S'il y a des coordonnées, ajouter le point
		if (isObject(results[line]['latitude']) && isObject(results[line]['longitude']))
			this._addMarker(tool, id, results[line]);
	}
	
	divResults.html(table);
	
	var bloc = jQuery('.geoloc-tool-results', divResults);
	this._setMarkersTriggers(tool, bloc, bloc);
	bloc.checkForActiveRow(bloc);
};



/*****************************************************************************/
/** GESTION DU GEOCODER                                                     **/
/*****************************************************************************/

function GeolocGeocoder(mapId) {
	this.mapId = mapId;
	this.tools = null;
	this.strings = null;
}

GeolocGeocoder.objs = new Array();
GeolocGeocoder.obj = function(mapId)
{
	if (!isObject(GeolocGeocoder.objs[mapId]))
		GeolocGeocoder.objs[mapId] = new GeolocGeocoder(mapId);
	return GeolocGeocoder.objs[mapId];
};

GeolocGeocoder.prototype.initialize = function(strings)
{
	this.tools = GeolocTools.obj(this.mapId);
	this.strings = strings;
	var objThis = this;
	
	// Action sur le bouton de la recherche par adresse
	jQuery('#'+objThis.mapId+'_geocode').click(function()
	{
		var map = gMap(objThis.mapId);
		var address = jQuery('#'+objThis.mapId+'_address').val();
		if (isObject(map) && isObject(address) && (address !== ''))
		{
			map.queryGeocoder(address, function(locations)
			{
				var results = new Array();
				if (isObject(locations) && locations && locations.length)
				{
					for (var index = 0; index < locations.length; index++)
						results.push({
								id: index,
								title: locations[index].name,
								latitude: locations[index].latitude,
								longitude: locations[index].longitude 
							});
				}
				objThis.tools.setResults('geocoder', {
					title: { name: objThis.strings['geocoder_name'], numeric: false },
					latitude: { name: objThis.strings['latitude'], numeric: true },
					longitude: { name: objThis.strings['longitude'], numeric: true }
				}, results, false);
				gmap_setViewportOnMarkers(gMap(this.mapId));
			});
			return true;
		}
		return false;
	});
	
	// Gestion des edits
	var bEraseOnFocusIn = true;
	jQuery('#'+objThis.mapId+'_address').focusin(function() {
		if (bEraseOnFocusIn === true)
		{
			bEraseOnFocusIn = false;
			jQuery(this).val('');
			jQuery(this).removeClass('empty-edit');
		}
	});
	jQuery('#'+objThis.mapId+'_address').focusout(function() {
		if (jQuery(this).val() === '')
		{
			bEraseOnFocusIn = true;
			jQuery(this).addClass('empty-edit');
			jQuery(this).val(objThis.strings['address_explic']);
			jQuery('#'+objThis.mapId+'_geocode').attr('disabled', 'disabled');
		}
		else
			jQuery('#'+objThis.mapId+'_geocode').removeAttr('disabled');
	});
	jQuery('#'+objThis.mapId+'_address').keyup(function() {
		if (jQuery(this).val() === '')
			jQuery('#'+objThis.mapId+'_geocode').attr('disabled', 'disabled');
		else
			jQuery('#'+objThis.mapId+'_geocode').removeAttr('disabled');
	});
};



/*****************************************************************************/
/** RECHERCHE DES POINTS SELON UNE REQUETE (DONT VOISINS)                   **/
/*****************************************************************************/

function GeolocGenericTool(mapId, tool) {
	this.mapId = mapId;
	this.tool = tool;
	this.tools = null;
	this.strings = null;
}

GeolocGenericTool.objs = new Array();
GeolocGenericTool.obj = function(mapId, tool)
{
	var key = ''+mapId+'-'+tool;
	if (!isObject(GeolocGenericTool.objs[key]))
		GeolocGenericTool.objs[key] = new GeolocGenericTool(mapId, tool);
	return GeolocGenericTool.objs[key];
};

// Requête pour récupérer les points
GeolocGenericTool.prototype._handleJSONMarker = function(map, feature)
{
	if (!feature || (feature.type != 'Feature'))
		return false;
	var geometry = feature.geometry;
	if (!geometry || (geometry.type != 'Point'))
		return false;
	var marker = {
			longitude: geometry.coordinates[0],
			latitude: geometry.coordinates[1]
		};
	var params = feature['properties'];
	for (var name in params)
		marker[name] = params[name];
	marker.icon = 'priveSibling';
	marker.icon_sel = 'priveSiblingActive';
	marker.id = params['id_point'];
	return marker;
}
GeolocGenericTool.prototype._handleJSONMarkers = function(map, content)
{
	var features;
	if (typeof(content) == "string")
		features = eval('(' + content + ')');
	else
		features = content;
	var markers = new Array();
	switch (features.type)
	{
	case 'FeatureCollection':
		{
			features = features.features;
			for (var index = 0; index < features.length; index++)
				markers.push(this._handleJSONMarker(map, features[index]));
		}
		break;
	case 'Feature':
		markers.push(this._handleJSONMarker(map, features));
		break;
	}
	this.tools.setResults(this.tool, this.cols, markers, true);
	return true;
};
GeolocGenericTool.prototype.query = function(url, params)
{
	var objThis = this;
	var map = gMap(objThis.mapId);
	var focus = false;
	if (isObject(params['focus']))
	{
		focus = params['focus'];
		delete params['focus'];
	}
	jQuery.ajax({
		url: url,
		type: "GET",
		dataType: "json",
		data: params,
		success: function(content, status, request) {
			if (content)
			{
				objThis._handleJSONMarkers(map, content);
				if (focus)
					gmap_setViewportOnMarkers(gMap(objThis.mapId));
			}
		}
	});
	return true;
}

GeolocGenericTool.prototype.initialize = function(strings, cols, url, params, fnDataExchange)
{
	this.tools = GeolocTools.obj(this.mapId);
	this.strings = strings;
	this.cols = cols;
	var objThis = this;
	
	jQuery('#'+objThis.mapId+'_search_'+objThis.tool).click(function()
	{
		if (fnDataExchange && (typeof(fnDataExchange) == 'function'))
			fnDataExchange(objThis, objThis.mapId, params);
		objThis.query(url, params);
	});
};

// Mise à jour du tableau des positions
// Cette fonction n'est plus utilisée que pour le paramétrage du plugin, pour
// la supprimer, il faudrait intégrer GeolocTools dans l'interface privée. Mais
// il n'y a pas toutes les fonctionnalités, ni le mêmes divs... Donc ça demande
// un peu de travail.
jQuery.fn.updateGeocoderResults = function(results)
{
	// Récupérer un modèle de ligne
	var template = jQuery('table.address_template', this);
	var templateHeader = jQuery("tr.header", template).clone();
	var templateRow = jQuery("tr.geocoder", template);
	var templateNoResults = jQuery("tr.no-results", template).clone();
	
	// Vider le tableau
	var tableContents = jQuery('table.address_list tbody', this);
	tableContents.html("");

	// Cacher le tableau s'il n'y a pas de résultats...
	if (!isObject(results) || (results.length == 0))
	{
		tableContents.append(templateNoResults);
	}
	
	// Parcourir les résultats pour ajouter les lignes
	else
	{
		tableContents.append(templateHeader);
		for (var index in results)
		{
			var newRow = templateRow.clone();
			jQuery('.addr_location', newRow).html(results[index].name);
			jQuery('.addr_latitude', newRow).html(results[index].latitude);
			jQuery('.addr_longitude', newRow).html(results[index].longitude);
			tableContents.append(newRow);
		}
	}
	
	// Réafficher le tableau
	this.removeClass('hidden');
}



/*****************************************************************************/
/** GESTION DE LA LISTE DE POINTS ÉDITÉS                                    **/
/*****************************************************************************/

function EditMarkers(mapId) {
	this.mapId = mapId;
	this.divId = null;
	this.canDragMarkers = false;
	this.strings = null;
	this.bloc = null;
	this.map = null;
}

EditMarkers.objs = new Array();
EditMarkers.obj = function(mapId)
{
	if (!isObject(EditMarkers.objs[mapId]))
		EditMarkers.objs[mapId] = new EditMarkers(mapId);
	return EditMarkers.objs[mapId];
}

// ID du marker actif
EditMarkers.prototype.getActiveMarkerID = function()
{
	var edit = jQuery('#active_marker_'+this.mapId);
	if (!isObject(edit))
		return 0;
	return parseInt(edit.val());
};
EditMarkers.prototype.setActiveMarkerID = function(id)
{
	var edit = jQuery('#active_marker_'+this.mapId);
	if (isObject(edit))
		edit.val(id);
};

// Nombre de marqueurs
EditMarkers.prototype.getMarkersCount = function()
{
	var edit = jQuery('#markers_count_'+this.mapId);
	if (!isObject(edit))
		return -1;
	return parseInt(edit.val());
};
EditMarkers.prototype.setMarkersCount = function(count)
{
	var edit = jQuery('#markers_count_'+this.mapId);
	if (isObject(edit))
		edit.val(count);
};

// Information sur le marqueur actif
EditMarkers.prototype.getActiveMarkerInfo = function()
{
	var id = this.getActiveMarkerID();
	var row = this.bloc.getMarkerRow(id);
	if (isObject(row))
		return row.getMarkerRowInfo();
	else
		return null;
};

// Mise à jour d'un marqueur
EditMarkers.prototype.updateMarker = function(row, bDoNotUpdatePosition)
{
	var isActive = (row.hasClass('active')) ? true : false;
	var isDeleted = (row.hasClass('deleted')) ? true : false;
	var info = row.getMarkerRowInfo();
	if (isDeleted)
	{
		if (this.map)
			this.map.removeMarker(info['id']);
	}
	else
	{
		if (bDoNotUpdatePosition !== true)
		{
			var params = {
				latitude: info['latitude'],
				longitude: info['longitude'],
			};
			if (isActive)
			{
				params['title'] = this.strings.titre_marqueur_actif;
				params['icon'] = 'activeMarker';
				params['zorder'] = 20;
				params['draggable'] = true;
			}
			else
			{
				params['title'] = this.strings.titre_marqueur_edit;
				params['icon'] = 'editMarker';
				params['zorder'] = 10;
				params['draggable'] = false;
			}
			if (this.map)
				this.map.setMarker(info['id'], params);
		}
		if (isActive)
		{
			if (this.map)
			{
				this.map.panTo(info['latitude'], info['longitude']);
				this.map.setZoom(info['zoom']);
			}
		}
	}
};

// Ajout de tous les marqueurs définis
EditMarkers.prototype.updateMarkers = function()
{
	// Mise à jour des marqueurs
	var activeRowID = 0;
	var count = 0;
	var objThis = this;
	jQuery('tr.marker', this.bloc).each(function()
	{
		var row = jQuery(this);
		if (row.hasClass('active'))
			activeRowID = row.getMarkerRowID();
		count++;
		objThis.updateMarker(row);
	});
	
	// Mise  jour de l\'information globale
	if (activeRowID)
		this.setActiveMarkerID(activeRowID);
	this.setMarkersCount(count);
};

// Forcer la création d'un marqueur s'il n'y en a pas
EditMarkers.prototype.ensureActiveMarker = function()
{
	var commands = jQuery('#markers_set_cmds_'+this.mapId);
	this.bloc.checkForActiveRow(this.bloc, commands);
};


// Modification de la position du marqueur actif
EditMarkers.prototype.setActiveMarkerPosition = function(latitude, longitude)
{
	var markerID = this.getActiveMarkerID();
	if (markerID != 0)
	{
		var row = this.bloc.getMarkerRow(markerID);
		if (isObject(row))
		{
			row.setMarkerPosition(latitude, longitude);
			this.updateMarker(row);
		}
	}
};

// Modification du zoom du marqueur actif
EditMarkers.prototype.setActiveMarkerZoom = function(zoom)
{
	var markerID = this.getActiveMarkerID();
	if (markerID != 0)
	{
		var row = this.bloc.getMarkerRow(markerID);
		if (isObject(row))
		{
			row.setMarkerZoom(zoom);
			this.updateMarker(row);
		}
	}
};

// Changer l'ID d'un marqueur (parce qu'on le relie à un autre)
EditMarkers.prototype.changeActiveMarkerID = function(id)
{
	var markerID = this.getActiveMarkerID();
	if (markerID != 0)
	{
		var row = this.bloc.getMarkerRow(markerID);
		if (isObject(row))
		{
			this.map.removeMarker(markerID);
			row.changeMarkerID(id);
			this.updateMarker(row);
		}
	}
};
	
// Initialisation
EditMarkers.prototype.initialize = function(divId, canDragMarkers, strings)
{
	this.divId = divId;
	this.canDragMarkers = canDragMarkers;
	this.strings = strings;
	this.bloc = jQuery('#markers_set_'+this.mapId);
	this.map = null;
	
	var objThis = this;
	jQuery('#'+this.divId).gmapReady(function()
	{
		var bloc = objThis.bloc;
		objThis.map = gMap(objThis.mapId);
		if (!isObject(bloc) || !isObject(objThis.map))
			return;
		
		// Fonctionnement de la liste de marqueurs
		bloc.setMarkerTriggers(null, bloc);
		bloc.checkForActiveRow(bloc);
		jQuery('.btn_marker_add').setAddMarkerAction(bloc, function()
		{
			return objThis.map.getViewport();
		}, 'markers_template_'+objThis.mapId, bloc);

		// Créer les marqueurs
		objThis.updateMarkers();
		
		// Évènements de la liste des marqueurs vers la carte
		bloc.bind('marker_created', function(event, markerID)
		{
			objThis.updateMarkers();
		});
		bloc.bind('marker_deleted', function(event, markerID, state)
		{
			if (state === 'created') // La ligne a été supprimée réellement, donc elle ne sera pas mise à jour
				objThis.map.removeMarker(markerID);
			objThis.updateMarkers();
		});
		bloc.bind('active_marker_changed', function(event, ids)
		{
			objThis.setActiveMarkerID(ids.active);
			objThis.updateMarkers();
		});
		
		// Évènements de la carte vers la liste de marqueurs
		objThis.map.addListener('click-on-map', function(event, latitude, longitude)
		{
			objThis.setActiveMarkerPosition(latitude, longitude);
		});
		objThis.map.addListener('zoom', function(event, zoom)
		{
			var markerID = objThis.getActiveMarkerID();
			if (markerID != 0)
			{
				var row = bloc.getMarkerRow(markerID);
				if (isObject(row))
					row.setMarkerZoom(zoom);
			}
		});
		if (objThis.canDragMarkers)
			objThis.map.addListener('drop-marker', function(event, id, latitude, longitude)
			{
				var row = bloc.getMarkerRow(id);
				if (isObject(row))
				{
					row.setMarkerPosition(latitude, longitude);
					objThis.updateMarker(row, true);
				}
			});
	});
};



/*****************************************************************************/
/** GESTION DES TABLEAUX DE PROPRIETE UI                                    **/
/*****************************************************************************/

// Surcharge du mécanisme de dépliage des blocs
// Pour éviter d'être trop sensible aux changements de SPIP, les fonctions sont
// entièrement réécrites ici...
jQuery.fn.gmap_showother = function(cible)
{
	var me = this;
	if (me.is('.replie'))
	{
		me.addClass('deplie').removeClass('replie');
		jQuery(cible).slideDown('fast', function()
		{
			jQuery(me).addClass('blocdeplie').removeClass('blocreplie').removeClass('togglewait');
		});
		jQuery('#'+me.depliantEventTarget).trigger('gmap_depliantShow');
	}
	return this;
}
jQuery.fn.gmap_hideother = function(cible)
{
	var me = this;
	if (!me.is('.replie'))
	{
		me.addClass('replie').removeClass('deplie');
		jQuery(cible).slideUp('fast', function()
		{
			jQuery(me).addClass('blocreplie').removeClass('blocdeplie').removeClass('togglewait');
		});
		jQuery('#'+me.depliantEventTarget).trigger('gmap_depliantHide');
	}
	return this;
}
jQuery.fn.gmap_toggleother = function(cible)
{
	if (this.is('.deplie'))
		return this.gmap_hideother(cible);
	else
		return this.gmap_showother(cible);
}
jQuery.fn.gmap_depliant = function(eventTarget, cible)
{
	this.depliantEventTarget = eventTarget;
	
	// premier passage
	if (!this.is('.depliant'))
	{
		var time = 400;
		var me = this;
		this.addClass('depliant');

		// effectuer le premier hover
		if (!me.is('.deplie'))
		{
			me.addClass('hover').addClass('togglewait');
			var t = setTimeout(function()
			{
				me.gmap_toggleother(cible);
				t = null;
			}, time);
		}

		// programmer les futurs hover
		me.hover(function(e)
		{
			me.addClass('hover');
			if (!me.is('.deplie'))
			{
				me.addClass('togglewait');
				if (t) { clearTimeout(t); t = null; }
				t = setTimeout(function()
				{
					me.gmap_toggleother(cible);
					t = null;
				}, time);
			}
		}
		, function(e)
		{
			if (t) { clearTimeout(t); t = null; }
			me.removeClass('hover');
		})
		.end();
	}
	return this;
}
jQuery.fn.gmap_depliant_clicancre = function(eventTarget, cible)
{
	var me = this.parent();
	me.depliantEventTarget = eventTarget;
	// gerer le triangle clicable
	if (me.is('.togglewait'))
		return false;
	me.gmap_toggleother(cible);
	return false;
}

// Serialisation à la PHP
function php_serialize(txt)
{
	switch(typeof(txt))
	{
	case 'string':
		return 's:'+txt.length+':"'+txt+'";';
	case 'number':
		if(txt>=0 && String(txt).indexOf('.') == -1 && txt < 65536) return 'i:'+txt+';';
		return 'd:'+txt+';';
	case 'boolean':
		return 'b:'+( (txt)?'1':'0' )+';';
	case 'object':
		var i=0,k,ret='';
		for(k in txt){
			//alert(isNaN(k));
			if(!isNaN(k)) k = Number(k);
			ret += php_serialize(k)+php_serialize(txt[k]);
			i++;
		}
		return 'a:'+i+':{'+ret+'}';
	default:
		return 'N;';
		alert('var undefined: '+typeof(txt));return undefined;
	}
}

// Désérialisation "à la" PHP
function php_unserialize(txt)
{
	var level=0,arrlen=new Array(),del=0,final=new Array(),key=new Array(),save=txt;
	while(1)
	{
		switch(txt.substr(0,1)){
		case 'N':
			del = 2;
			ret = null;
		break;
		case 'b':
			del = txt.indexOf(';')+1;
			ret = (txt.substring(2,del-1) == '1')?true:false;
		break;
		case 'i':
			del = txt.indexOf(';')+1;
			ret = Number(txt.substring(2,del-1));
		break;
		case 'd':
			del = txt.indexOf(';')+1;
			ret = Number(txt.substring(2,del-1));
		break;
		case 's':
			del = txt.substr(2,txt.substr(2).indexOf(':'));
			ret = txt.substr( 1+txt.indexOf('"'),del);
			del = txt.indexOf('"')+ 1 + ret.length + 2;
		break;
		case 'a':
			del = txt.indexOf(':{')+2;
			ret = new Array();
			arrlen[level+1] = Number(txt.substring(txt.indexOf(':')+1, del-2))*2;
		break;
		case 'O':
			txt = txt.substr(2);
			var tmp = txt.indexOf(':"')+2;
			var nlen = Number(txt.substring(0, txt.indexOf(':')));
			name = txt.substring(tmp, tmp+nlen );
			//alert(name);
			txt = txt.substring(tmp+nlen+2);
			del = txt.indexOf(':{')+2;
			ret = new Object();
			arrlen[level+1] = Number(txt.substring(0, del-2))*2;
		break;
		case '}':
			txt = txt.substr(1);
			if(arrlen[level] != 0){alert('var missed : '+save); return undefined;};
			//alert(arrlen[level]);
			level--;
		continue;
		default:
			if(level==0) return final;
			alert('syntax invalid(1) : '+save+"\nat\n"+txt+"level is at "+level);
			return undefined;
		}
		if(arrlen[level]%2 == 0){
			if(typeof(ret) == 'object'){alert('array index object no accepted : '+save);return undefined;}
			if(ret == undefined){alert('syntax invalid(2) : '+save);return undefined;}
			key[level] = ret;
		} else {
			var ev = '';
			for(var i=1;i<=level;i++){
				if(typeof(key[i]) == 'number'){
					ev += '['+key[i]+']';
				}else{
					ev += '["'+key[i]+'"]';
				}
			}
			eval('final'+ev+'= ret;');
		}
		arrlen[level]--;//alert(arrlen[level]-1);
		if(typeof(ret) == 'object') level++;
		txt = txt.substr(del);
		continue;
	}
}

// Changement d'une propriété dans le tableau
function setUIState(id, propname, propvalue)
{
	var strState = jQuery('#'+id).val();
	var state = php_unserialize(strState);
	if (!isObject(state))
		state = new Array();
	state[propname] = propvalue;
	strState = php_serialize(state);
	jQuery('#'+id).val(strState);
}

// Récupération d'un propriété
function getUIState(id, propname, defvalue)
{
	var strState = jQuery('#'+id).val();
	if (!isObject(strState))
		return defvalue;
	var state = php_unserialize(strState);
	if (!isObject(state) || !isObject(state[propname]))
		return defvalue;
	return state[propname];
}