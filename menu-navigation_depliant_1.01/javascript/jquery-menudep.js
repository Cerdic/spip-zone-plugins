/*
	// jquery/jquery-menudep.js

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Menudep.
	
	Menudep is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Menudep is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Menudep; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Menudep. 
	
	Menudep est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	Menudep est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	******************************************************/

jQuery().ready(function(){

	var menudep_div = menudep['id'] + '>' + menudep['div']
	, menudep_item = menudep['id'] + '>' + menudep['a']
	, menudep_absolute = (menudep['absolute'] == 'oui')
	, menudep_replier_autres = (menudep['replier'] == 'oui')
	, menudep_replier_avant = (menudep['reavant'] == 'oui')
	, menudep_heriter = (menudep['heriter'] == 'oui')
	, menudep_current_id = false
	;

	jQuery.fn.extend({
   	wrapSub: function(html) {
			if ( this[0] ) { jQuery(html, this[0].ownerDocument).clone().insertBefore(this[0]).append(this); }
			return (this);
		},
		defineId: function() {
			$(this).each(function(i) {
                $(this).attr('id','__menudep__'+i);
			})
		},
		showMe: function() {
			if(menudep['speedin']==1) {
				$(this).show();
			}
			else {
				$(this).show(menudep['speedin']);
			}
		},
		hideMe: function() {
			if(menudep['speedout']==1) {
				$(this).hide();
			}
			else {
				$(this).hide(menudep['speedout']);
			}
		}
	});

	// envelopper toutes les sous-rubriques pour permettre
	// d'afficher sous-rubriques et articles sans chevauchement	
	$(menudep_item).each(function () {
		$(this).siblings('ul').wrapSub('<div class=\"srub\"></div>');
	});
	// identifier les nouveaux blocs pour éviter les chevauchements d'event
	$(menudep_item).siblings('div.srub').defineId();
	// masquer les sous-rubriques inactives
	$(menudep_item + ':not(.' + menudep['class'] + ')').siblings('div.srub').hide();
	// afficher la sous-rubrique active
	$(menudep_item + '.' + menudep['class']).siblings('div.srub').show();

	// style des boites flottantes (héritage ou configuration demandée)
	if(menudep_absolute) {
		$(menudep_item + ':not(.' + menudep['class'] + ')').siblings('div.srub')
			.css({'position':'absolute','margin-top':menudep['top'],'margin-left':menudep['left'],'zIndex':menudep['zindex']});
		if(menudep_heriter) {
			var r = false;
			var b = ((r = $(menudep_div).css('border')) ? r : menudep['border']);
			var c = ((r = $(menudep_div).css('background-color')) ? r : menudep['bgcolor']);
			$( menudep_item + ':not(.' + menudep['class'] + ')' ).siblings('div.srub').css({'border':b,'background-color':c});
		}
	}

	menudep_hideBlur = function(event) {
		$(this).parents('div.srub').addClass('hide').hideMe();
	}

	menudep_reactive_listener = function() {
		$(menudep_item).bind('mouseover',menudep_listener);
	}
	menudep_listener = function(event) {
		event.stopPropagation();		
		// ne pas prendre l'evenement en compte si déjà activé
		if(
			menudep_current_id && (menudep_current_id == $(this).siblings('div.srub').attr('id'))) {
			return(false);
		}
		$(menudep_item).unbind('mouseover',menudep_listener);
		menudep_current_id = $(this).siblings('div.srub').attr('id');
		// marquer par une classe toutes les boites sauf boite active
		$(menudep_item + ':not(.' + menudep['class'] + ')').siblings('div.srub').addClass('hide');
		// position de la couche si flottant
		if(menudep_absolute) {
			$(menudep_item + ':not(.' + menudep['class'] + ')').siblings('div.srub').css({'zIndex':menudep['zindex']-1});
			$(this).siblings('div.srub').css({'zIndex':menudep['zindex']});
		}
		// si demandée en config, replier les boites avant
		if(menudep_replier_avant && menudep_replier_autres) {
			$( menudep_item ).siblings('div.hide').hide();
		} 
		// deplier le sous-menu survolé
		//$(this).siblings('div.srub').removeClass('hide').show(menudep['speedin']);
		$(this).siblings('div.srub').removeClass('hide').showMe();
		// replier les autres boites
		if(!menudep_replier_avant && menudep_replier_autres) {
			$( menudep_item ).siblings('div.hide').hideMe();
		} 
		window.setTimeout('menudep_reactive_listener()',menudep['tempo']);
		return(false);
	}
	
	// accrocher les événements pour la souris et le clavier
	$(menudep_item).bind('mouseover',menudep_listener);
	$(menudep_item).focus(menudep_listener);
	$(menudep_item + ':not(.' + menudep['class'] + ')').siblings('div.srub').children('ul').children('li:last-child').blur(menudep_hideBlur);
	
	$(document).click( function() { 
		// tout replier si click dans la page
		$(menudep_item + ':not(.' + menudep['class'] + ')').siblings('div.srub').addClass('hide');
		$(menudep_item).siblings('div.hide').hideMe();
	});

});
