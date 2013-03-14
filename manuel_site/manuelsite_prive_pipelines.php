<?php
/**
 * Plugin Manuel du site
 * 
 * Utilisation des pipelines dans l'espace privé
 * 
 * @package SPIP\Manuelsite\Pipelines
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline body_prive (SPIP)
 * 
 * On ajoute dans le body l'icone et l'article du manuel du site
 * si la configuration "afficher_bord_gauche" n'est pas activée
 * 
 * @param string $flux
 * 		Le contenu de la page
 * @return string $flux
 * 		Le contenu de la page modifiée
 */
function manuelsite_body_prive($flux){
	include_spip('inc/config');
	$conf_manuelsite = lire_config('manuelsite',array());
	if(
		isset($conf_manuelsite["id_article"]) && $conf_manuelsite["id_article"] 
		&& (!isset($conf_manuelsite["afficher_bord_gauche"]) || $conf_manuelsite["afficher_bord_gauche"]))
		$flux .= recuperer_fond('prive/manuelsite',array('id_article'=>$conf_manuelsite["id_article"]));
	return $flux;
}

/**
 * Insertion dans le pipeline affiche_droite (SPIP)
 * 
 * On ajoute dans la colonne de gauche l'article du manuel du site
 * si la configuration "afficher_bord_gauche" est activée
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return string $flux
 * 		Le contexte du pipeline modifié
 */
function manuelsite_affiche_droite($flux){
	include_spip('inc/config');
	$conf_manuelsite = lire_config('manuelsite',array());
	if(isset($conf_manuelsite["id_article"]) && $conf_manuelsite["id_article"] && 
		!$conf_manuelsite["afficher_bord_gauche"]) {
		include_spip('inc/plugin');
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

/**
 * Insertion dans le pipeline affiche_gauche (SPIP)
 * 
 * On affiche le bloc des contenus possibles de faq dans la colonne de l'article de manuel
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function manuelsite_affiche_gauche($flux){
	// Si c'est un article en edition ou un article dans le prive,
	// on propose le formulaire, si l'article n'existe pas encore, on ne fait rien
	include_spip('inc/plugin');
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

/**
 * Fonction retournant les blocs de FAQ utilisables
 * 
 * Parcourt tous les plugins à la recherche de fichiers de langue sous la forme 
 * /lang/faq-nom_plugin_fr.php afin de les afficher dans le bloc de choix d'éléments de faq
 * 
 * @return string $texte
 * 		Le contenu du bloc
 */
function manuelsite_lister_blocs_faq() {
	include_spip('inc/plugin');
	
	$texte = "\n<div>";

	$modules = array();

	foreach (liste_plugin_actifs() as $plugin) {
		$fichiers = preg_files(_DIR_PLUGINS.$plugin['dir'].'/lang/faq-[a-z_]+\.php$');
		foreach ($fichiers as $fichier) {
			if (preg_match(',/(faq-[a-z]+)_([a-z_]+)\.php$,', $fichier, $r))
				$modules[$plugin['nom']] = $r[1] ;
		}
	}
	ksort($modules);
	if (count($modules) > 0) {
		$texte .= "\n<ul class=\"faq\">" ;
		foreach ($modules as $nom_module => $dir_module) {
			$texte .= "\n<li><b>".typo($nom_module)."</b>";
			$texte .= manuelsite_afficher_raccourcis($dir_module);
			$texte .= "\n</li>" ;
		}
		$texte .= "\n</ul>" ;
	}

	$texte .= "\n</div>" ;
	return $texte ;
}

/**
 * Fonction retournant les blocs de FAQ utilisables pour un plugin donné
 *
 */
function manuelsite_afficher_raccourcis($module = "faq-manuelsite") {
	global $spip_lang;
	
	include_spip('inc/traduire');
	$texte = "\n<ul class=\"faq_blocs\">";
	charger_langue($spip_lang, $module);

	$tableau = $GLOBALS['i18n_' . $module . '_' . $spip_lang];
	ksort($tableau);

	$i = 0;
	foreach ($tableau as $raccourci => $val) {
		if(!preg_match('/_q$/',$raccourci)) {
			$texte .= "\n<li title=\"".texte_backend($val)."\">&lt;faq";
			if($module != "faq-manuelsite") {
				$texte .= "|p=".substr_replace($module,"",0,4);
			}
			$texte .= "|b=$raccourci&gt;</li>";
		}
	}
	$texte .= "\n</ul>" ;

	return $texte;
}
?>