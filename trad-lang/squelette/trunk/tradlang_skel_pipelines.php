<?php
/**
 * Trad-lang v2 Squelette
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 * Fichier des pipelines utilisés par le plugin
 * 
 * @package SPIP\Tradlang Skel\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * 
 * On ajoute les javascript dans le head :
 * - javascript/tradlang.js
 * - javascript/tradlang_tooltip.js si le plugin tooltip est activé
 * 
 * @param string $flux 
 * 		Le contenu de la balise #INSERT_HEAD
 * @return string $flux
 * 		Le contenu de la balise modifié
 */
function tradlang_skel_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/tradlang.js').'" ></script>'."\n";
	if(defined('_DIR_PLUGIN_TOOLTIP'))
		$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/tradlang_tooltip.js').'" ></script>'."\n";
	return $flux;
}

/**
 * Insertion dans le pipeline jqueryui_forcer (plugin jQueryUI)
 * 
 * On ajoute le chargement des js pour les tabs
 * 
 * @param array $plugins 
 * 		Un tableau des scripts déjà demandé au chargement
 * @return array $plugins 
 * 		Le tableau complété avec les scripts que l'on souhaite 
 */
function tradlang_skel_jqueryui_plugins($plugins){
	if(!test_espace_prive())
		$plugins[] = "jquery.ui.tabs";
	return $plugins;
}
/**
 * Insertion dans le pipeline insert_head_css (SPIP)
 * 
 * On ajoute les deux feuilles de style dans le head :
 * - La statique css/tradlang.css
 * - la calculée spip.php?page=tradlang.css
 * 
 * @param string $flux
 * 		Le contenu de la balise #INSERT_HEAD_CSS
 * @return string $flux
 * 		Le contenu de la balise modifié
 */
function tradlang_skel_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="'.direction_css(find_in_path('css/tradlang.css')).'" type="text/css" />';
		$flux .= '<link rel="stylesheet" href="'.parametre_url(generer_url_public('tradlang.css'),'ltr',$GLOBALS['spip_lang_left']).'" type="text/css" />';
	}
	return $flux;
}
?>