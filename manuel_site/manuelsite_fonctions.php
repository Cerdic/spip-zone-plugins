<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/traduire');
include_spip('inc/plugin');

/**
 * Fonction retournant l'article du manuel enregistre dans la config
 * si on doit le cacher
 *
 */
function manuelsite_article_si_cacher() {
	$conf_manuelsite = lire_config('manuelsite',array());
	if (!test_espace_prive() && $conf_manuelsite["cacher_public"] && $id=intval($conf_manuelsite["id_article"]))
		return($id);
	return 0;
}

/**
 * Fonction retournant les blocs de FAQ utilisables
 *
 */
function manuelsite_lister_blocs_faq() {
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

// Transformer une chaine p1:v1;p2:v2 en tableau associatif
// Pompage champs extras
function manuelsite_params_to_array($params="") {
	$tablo = array();
	if($params != "") {
		$params = preg_replace( '/^(.*)$/','"${1}"',$params) ;
		$params = str_replace(';','","',$params);
		$params= str_replace(':','"=>"',$params);
		eval("\$tablo=array($params);");
	}
	return $tablo;	
}
?>