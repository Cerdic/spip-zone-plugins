<?php 
/**
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

/*
	
	Nota: plugin.xml en cache.
		si modif plugin.xml, il faut reactiver le plugin (config/plugin: desactiver/activer)
		Pas sur SPIP.2
	Nota:
		SPIP 2 fait automatiquement un cache des css pour l'espace prive'.
		Penser a vider le cache ou simplement local/cache-css/ lors de modification de la feuille.
	
*/

if(!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/spiplistes_api_globales');

function spiplistes_header_prive ($flux) {

	$exec = _request('exec');

	$flux .= ""
		. "\n\n<!-- SPIPLISTES GADGETS v.: ".spiplistes_real_version_get(_SPIPLISTES_PREFIX)." -->\n"
		. "<script src='".url_absolue(find_in_path('javascript/spiplistes_gadgets.js'))."' type='text/javascript'></script>\n"
		;

	if(in_array($exec, array(
		_SPIPLISTES_EXEC_ABONNES_LISTE
		, _SPIPLISTES_EXEC_COURRIER_GERER
		, _SPIPLISTES_EXEC_COURRIER_EDIT
		, _SPIPLISTES_EXEC_COURRIERS_LISTE
		, _SPIPLISTES_EXEC_LISTES_LISTE
		, _SPIPLISTES_EXEC_LISTE_GERER
		, _SPIPLISTES_EXEC_LISTE_EDIT
		, _SPIPLISTES_EXEC_MAINTENANCE
		, _SPIPLISTES_EXEC_CONFIGURE
		, 'auteur_infos' // liste-listes
		, _SPIPLISTES_EXEC_IMPORT_EXPORT
		)
		)
	) {
		$js_dir = _DIR_PLUGIN_SPIPLISTES . "javascript/";
		
		$flux .= "\n\n<!-- SPIP-Listes -->\n" 
			. "<link rel='stylesheet' href='"._DIR_PLUGIN_SPIPLISTES."spiplistes_style_prive.css' type='text/css' media='all' />\n"
			;

		switch($exec) {
			case _SPIPLISTES_EXEC_COURRIER_EDIT:
			case _SPIPLISTES_EXEC_COURRIER_GERER:
				$flux .= ""
					. "<script type=\"text/javascript\" src=\"" . $js_dir . "spiplistes_courrier_edit.js\"></script>\n"
/*
 le datepicker ne sert plus dans cette version.					
					. "<link rel='stylesheet' href='".url_absolue(find_in_path('img_pack/date_picker.css'))."' type='text/css' media='all' />\n"
					. "<script src='".url_absolue(find_in_path('javascript/datepicker.js'))."' type='text/javascript'></script>\n"
 A priori, ce bout de code ne sert plus
					. "<script src='".url_absolue(find_in_path('javascript/jquery-dom.js'))."' type='text/javascript'></script>\n"
*/					
					. "<meta http-equiv='expires' content='0' />\n"
					. "<meta http-equiv='pragma' content='no-cache' />\n"
					. "<meta http-equiv='cache-control' content='no-cache' />\n"
					;
				break;
			case _SPIPLISTES_EXEC_COURRIERS_LISTE:
				break;
			case _SPIPLISTES_EXEC_CONFIGURE:
				$flux .= "<script type=\"text/javascript\" src=\"" . $js_dir . "spiplistes_config.js\"></script>\n";
				break;
			case _SPIPLISTES_EXEC_LISTE_GERER:
				$js_alert = spiplistes_texte_html_2_iso(_T('spiplistes:Attention_action_retire_invites'), $GLOBALS['meta']['charset'], true);
				$flux .= ""
					. "<meta id='x-spiplistes-alert' content='" . $js_alert . "' />\n"
					. "<meta id='x-spiplistes-pri' name='" . _SPIPLISTES_LIST_PRIVATE . "' content='" . spiplistes_items_get_item("puce", _SPIPLISTES_LIST_PRIVATE) . "' />\n"
					. "<meta id='x-spiplistes-pub' name='" . _SPIPLISTES_LIST_PUBLIC . "' content='" . spiplistes_items_get_item("puce", _SPIPLISTES_LIST_PUBLIC) . "' />\n"
					. "<meta id='x-spiplistes-tra' name='" . _SPIPLISTES_TRASH_LIST . "' content='" . spiplistes_items_get_item("puce", _SPIPLISTES_TRASH_LIST) . "' />\n"
					. "<script type=\"text/javascript\" src=\"" . $js_dir . "spiplistes_liste_gerer.js\"></script>\n"
					. "<style type='text/css'>
.spiplistes .supprimer_cet_abo {background-image:url(".find_in_path("images/croix-rouge.gif").")}
</style>"
					;
				break;
			case _SPIPLISTES_EXEC_ABONNES_LISTE:
				$flux .= "<script type=\"text/javascript\" src=\"" . $js_dir . "spiplistes_abonnes_tous.js\"></script>\n
<style type='text/css'>
.spiplistes .supprimer_cet_abo {background-image:url(".find_in_path("images/croix-rouge.gif").")}
</style>
";			
				break;
			case _SPIPLISTES_EXEC_MAINTENANCE:
				$flux .= "<script type=\"text/javascript\" src=\"" . $js_dir . "spiplistes_maintenance.js\"></script>\n";
				break;
		}
		$flux .= "<!-- SPIP-Listes /-->\n\n";
	}

	return ($flux);
}

?>