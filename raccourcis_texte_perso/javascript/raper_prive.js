/**
 * 
 * javascript/raper_prive.js
 *
 * $LastChangedRevision: 24959 $
 * $LastChangedBy: paladin@quesaco.org $
 * $LastChangedDate: 2008-12-05 16:12:09 +0100 (ven., 05 déc. 2008) $
 * 
 *
 */

	/*****************************************************
	Copyright (C) 2009 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of RaPer.
	
	*****************************************************/
	


/* jQuery.noConflict();  /* pas glop sur SPIP2 + JQuery 1.2.6 */

jQuery(document).ready(function(){

	jQuery.extend({
		urlencode: function(str) {
    		return(encodeURIComponent(str));
		}
		,
		// pour mettre à jour la boite info (Edition)
		raper_boite_info: function(raper_lang) { 
			jQuery.ajax({ 
				'type': "POST"
				, 'url': "/spip.php?action=raper_ajax"
				, 'data': "raper_lang=" + raper_lang + "&raper_do=info"
				, 'success': function($result){
					if(!$result) $result = "Erreur !";
					jQuery("#msg-boite-info").html($result);
				}
			});
		}
		,
		// petit crayon et croix
		// version js du code php
		raper_edit_creer_lien: function($type, $id) { 
			var $title = jQuery("#x-raper_title_" + $type).attr('content');
			$lien = ""
			+ "<a href='#_r_" + $id + "' class='raper-" + $type + "' title=\"" + $title + "\""
			+ " onclick=\"" + "javascript:return jQuery.raper_action('raper-" + $type + "', '" + $id + "')" + "\""
			+ "\">"
			+ "<span class='no-screen $type'>" + $title + "</span></a>\n";
			return($lien);
		;
		}
		,
		raper_action: function(raper_do, id_raccourci) { 
			// la langue du raccourci est dans la meta
			var raper_lang = jQuery("#x-raper_lang").attr('content');
			var expression = /raper-(edit|drop|cancel|apply)/;
			expression.exec(raper_do);
	 		raper_do = RegExp.$1;
			var id_parent = "#_r_" + id_raccourci;
			/* alert(raper_do + " " + id_parent); /* */
			if ((raper_do == 'apply') || (raper_do == 'cancel') || (raper_do == 'edit') || (raper_do == 'drop')) {
				var data = "", raper_value = "";
				if ((raper_do == 'edit') || (raper_do == 'drop')) {
				// petite animation icone
					jQuery(this).addClass('raper-rload');
				}
				// confirmation modifier raccourci ? 
				// Préparer le contenu
				if(raper_do == 'apply') {
					var value = jQuery(id_parent + " td.value textarea").val();
					raper_value = "&raper_value=" + jQuery.urlencode(value);
				}
				jQuery.ajax({ 
					'type': "POST"
					, 'url': "/spip.php?action=raper_ajax"
					, 'data': "raper_do=" + raper_do + "&raper_id=" + id_raccourci + "&raper_lang=" + raper_lang + raper_value
					, 'success': function($result){
						if (!$result) {
							alert("Erreur: valeur indéfinie ou raccourci inexistant");
						}
						else {
							// placer le résultat dans la bonne cellule
							jQuery(id_parent + " td.value").html($result);
							switch(raper_do) {
								case 'edit': // editer un raccourci perso ?
									jQuery(id_parent + ' a.raper-edit').removeClass('raper-rload');
									// faire disparaitre le crayon et le drop
									jQuery(id_parent + ' a.raper-edit').remove();
									var mark_drop = false;
									var ii;
									if(ii = jQuery(id_parent + ' a.raper-drop span').html()) {
										mark_drop = "<span class='drop'>&nbsp;</span>";
									}
									jQuery(id_parent + ' a.raper-drop').remove();
									if(mark_drop) {
										// place un marqueur pour replacer le drop si cancel
										jQuery(id_parent + " td.actions").prepend(mark_drop);
									}
									break;
								case 'drop': // supprimer un raccourci perso ?
									// faire disparaitre le drop
									jQuery(id_parent + ' a.raper-drop').remove();
									// mettre à jour la boite info
									jQuery.raper_boite_info(raper_lang);
									break;
								case 'apply':
									// remettre le crayon et placer le drop
									jQuery(id_parent + " td.actions").prepend(jQuery.raper_edit_creer_lien('edit', id_raccourci)
										+ jQuery.raper_edit_creer_lien('drop', id_raccourci));
									// mettre à jour la boite info
									jQuery.raper_boite_info(raper_lang);
									break;
								case 'cancel':
									// si le drop était présent, le remettre
									if(jQuery(id_parent + " td.actions span.drop").text()) {
										jQuery(id_parent + " td.actions").prepend(
											jQuery.raper_edit_creer_lien('edit', id_raccourci)
											+ " " + jQuery.raper_edit_creer_lien('drop', id_raccourci)
											);
									}
									else {
										// sinon, remettre uniquement le crayon
										jQuery(id_parent + " td.actions").prepend(jQuery.raper_edit_creer_lien('edit', id_raccourci));
									}
									break;
							}
						}
					}
				});
				return(false);
			}
		}
	});
	
	/*
	 * Formulaire du configure
	 */
	jQuery(".raper-form input[@name=editer_tout]").change( function() {
		if (jQuery(this).attr('checked')) {
			jQuery("input[@class=choix]").removeAttr('checked');
		}
	});
	jQuery("input[@name=editer_public],input[@name=editer_local]").change( function() {
		if (jQuery(this).attr('checked')) {
			jQuery("input[@name=editer_tout]").removeAttr('checked');
		}
	});
	
});
