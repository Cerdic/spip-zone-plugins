<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip("base/forms");
include_spip("inc/forms");

//
// Formulaires
//


	// A reintegrer dans echapper_html()
	function Forms_forms_avant_propre($texte) {
		static $reset;
	//echo "forms_avant_propre::";
		// Mecanisme de mise a jour des liens
		$forms = array();
		$maj_liens = ($_GET['exec']=='articles' AND $id_article = intval($_GET['id_article']));
		if ($maj_liens) {
			if (!$reset) {
				$query = "DELETE FROM spip_forms_articles WHERE id_article=$id_article";
				spip_query($query);
				$reset = true;
			}
			if (preg_match_all(',<form([0-9]+)([|]([a-z_0-9]+))?'.'>,', $texte, $regs, PREG_SET_ORDER)){
				foreach ($regs as $r) {
					$id_form = $r[1];
					$forms[$id_form] = $id_form;
				}
			}
			if ($forms) {
				$query = "INSERT INTO spip_forms_articles (id_article, id_form) ".
					"VALUES ($id_article, ".join("), ($id_article, ", $forms).")";
				spip_query($query);
			}
		}
	
		return $texte;
	}

	// Hack crade a cause des limitations du compilateur
	function _Forms_afficher_reponses_sondage($id_form) {
		return Forms_afficher_reponses_sondage($id_form);
	}

	function Forms_affiche_droite($flux){
		if (_request('exec')=='articles_edit'){
			$flux['data'] .= Forms_afficher_insertion_formulaire($flux['arg']['id_article']);
		}
		return $flux;
	}
	function Forms_insert_head($flux){
		$flux .= 	"<link rel='stylesheet' href='".find_in_path('spip_forms.css')."' type='text/css' media='all' />\n";
		$flux .= 	"<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/date_picker.css' type='text/css' media='all' />\n";
		return $flux;
	}
	function Forms_header_prive($flux){
		$flux .= 	"<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."spip_forms.css' type='text/css' media='all' />\n";
		$flux .= 	"<link rel='stylesheet' href='"._DIR_PLUGIN_FORMS."img_pack/date_picker.css' type='text/css' media='all' />\n";
		if($GLOBALS['meta']['multi_rubriques']=="oui" || $GLOBALS['meta']['multi_articles']=="oui")
			$active_langs = "'".str_replace(",","','",$GLOBALS['meta']['langues_multilingue'])."'";
		else
			$active_langs = "";
		$flux .= "<script src='".find_in_path('forms_lang.js')."' type='text/javascript'></script>\n". 
		"<script type='text/javascript'>\n".
		"var forms_def_lang='".$GLOBALS["spip_lang"]."';var forms_avail_langs=[$active_langs];\n".
		"$(forms_init_lang);\n".
		"</script>\n";
		
		return $flux;
	}

?>
