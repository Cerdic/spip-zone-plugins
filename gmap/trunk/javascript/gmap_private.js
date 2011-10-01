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
jQuery.fn.checkForActiveRow = function(eventTarget, bForce)
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
	else if ((lastRow == null) && (bForce === true))
	{
		jQuery('.btn_marker_add').triggerHandler('click');
	}
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
	var state = row.getMarkerRowState();

	// Si le marqueur est créé, on détruit toute la ligne
	if (state == "created")
	{
		// Supprimer la ligne
		row.remove();
		
		// Signaler la création
		eventTarget.trigger('marker_deleted', markerID);
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
			eventTarget.trigger('marker_deleted', markerID);
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
/** GESTION DES TABLEAUX DE RÉSULTAT DU GEOCODER                            **/
/*****************************************************************************/

// Mise à jour du tableau des positions
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