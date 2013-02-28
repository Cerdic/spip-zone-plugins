<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/plugin');

function manuelsite_body_prive($flux){
	include_spip('inc/config');
	$conf_manuelsite = lire_config('manuelsite',array());
	if($conf_manuelsite["id_article"] && (!isset($conf_manuelsite["afficher_bord_gauche"]) || $conf_manuelsite["afficher_bord_gauche"]))
		$flux .= recuperer_fond('prive/manuelsite',array('id_article'=>intval($conf_manuelsite["id_article"])));
	return $flux;
}

function manuelsite_affiche_droite($flux){
	include_spip('inc/config');
	$conf_manuelsite = lire_config('manuelsite',array());
	if($conf_manuelsite["id_article"] && !$conf_manuelsite["afficher_bord_gauche"]) {
		// Spip 2
		if(spip_version_compare($GLOBALS['spip_version_branche'],"2.1.99","<")) {
			$bouton = bouton_block_depliable(_T('manuelsite:titre_manuel'), false, "manuelsite_col");
			$cadre .= debut_cadre('r', find_in_path('prive/themes/spip/images/manuelsite-24.png'), '', $bouton, '', '', false);
			$cadre .= debut_block_depliable(false,"manuelsite_col") 
				. '<div id="manuelsite_contenu">'
				. recuperer_fond('prive/squelettes/inclure/manuelsite_article',array('id_article'=>$conf_manuelsite["id_article"]))
				. '</div>'
				. fin_block();
			$cadre .= fin_cadre_relief(true);
	
			$flux['data'] .= $cadre;

		// Spip3
		} else
			$flux["data"] .= recuperer_fond('prive/squelettes/navigation/bloc_manuelsite',array('id_article'=>$conf_manuelsite["id_article"]));
	}
   return $flux;
}
function manuelsite_affiche_gauche($flux){
	// Si c'est un article en edition ou un article dans le prive,
	// on propose le formulaire, si l'article n'existe pas encore, on ne fait rien

	if(spip_version_compare($GLOBALS['spip_version_branche'],"2.1.99",">")) {
		$exec_article = "article";
		$exec_article_edit = "article_edit";
	} else {
		$exec_article = "articles";
		$exec_article_edit = "article_edits";
	}

	if(($flux["args"]["exec"] == $exec_article_edit || $flux["args"]["exec"] == $exec_article) && $flux["args"]["id_article"] != ''){
		$conf_manuelsite = lire_config('manuelsite');
		if($conf_manuelsite["id_article"] && ($conf_manuelsite["id_article"] == $flux["args"]["id_article"])) {
			// Spip 2
			if(spip_version_compare($GLOBALS['spip_version_branche'],"2.1.99","<")) {
				$bouton = bouton_block_depliable(_T('manuelsite:titre_faq'), false, "manuelsite_col");
				$cadre .= debut_cadre('r', find_in_path('prive/themes/spip/images/manuelsite-24.png'), '', $bouton, '', '', false);
				$cadre .= debut_block_depliable(false,"manuelsite_col") 
					. '<div class="cadre_padding" id="manuelsite_faq">'
					. _T('manuelsite:explication_faq')
					. manuelsite_lister_blocs_faq()
					. '</div>'
					. fin_block();
				$cadre .= fin_cadre_relief(true);
		
				$flux['data'] .= $cadre;
			// Spip 3
			} else { 
				$flux["data"] .= recuperer_fond('prive/squelettes/navigation/bloc_faq');
			}
		}
	}
   return $flux;
}
?>