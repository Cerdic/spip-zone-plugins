<?php
/**
 * @name 		ADX MENU | SPIP 2.0 plugin
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 * @license		(c) 2009 GNU GPL v3 {@link http://opensource.org/licenses/gpl-license.php GNU Public License}
 * @version 	0.2 (06/2009)
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/** 
 * Insertion des CSS par INSERT_HEAD
 */
function adxmenu_insert_head($flux){
	$conf = function_exists('lire_config') ? lire_config('adxmenu') : false;
	$adx = (!$conf OR !strlen($conf['adx'])) ? ADXMENU_OUVERTURE_DEFAUT : $conf['adx'];
	include_spip('plugins/installer'); // spip_version_compare dans SPIP 3.x
	include_spip('inc/plugin'); // spip_version_compare dans SPIP 2.x
	if (spip_version_compare($GLOBALS['spip_version_branche'], '3.0.0alpha', '>=')) {
    $css_styles = generer_url_public("adxmenu_css_styles_spip3.css",'type_menu='.$adx);
	} else {
    $css_styles = generer_url_public("adxmenu_css_styles.css",'type_menu='.$adx);
	}
	$flux .= "<!-- ADX Menu insert head -->"
		."\n<link rel='stylesheet' href='".generer_url_public("adxmenu_css.css",'type_menu='.$adx)."' type='text/css' media='projection, screen, tv' />"
		."\n<!--[if lte IE 6]>"
		."\n<link rel='stylesheet' href='".generer_url_public('css/adxmenu_css_ie.css','type_menu='.$adx)."' type='text/css' media='projection, screen, tv' />"
		."\n<script src='".url_absolue(find_in_path('javascript/ADxMenu.js'))."' type='text/javascript'></script>"
		."\n<![endif]-->"
		."\n<link rel='stylesheet' href='".$css_styles."' type='text/css' media='projection, screen, tv' />"
		."<!-- END ADX Menu insert head -->\n";
	return $flux;
}

/** 
 * Insertion des CSS par INSERT_HEAD_CSS (pour SPIP3)
 */
function adxmenu_insert_head_css($flux){
	return adxmenu_insert_head($flux);
}

?>