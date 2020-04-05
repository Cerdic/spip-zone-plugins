<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Utilisations de pipelines par Responsive Nav
 *
 * @plugin     Responsive Nav
 * @copyright  2016
 * @author     jeanmarie
 * @licence    CC by-sa
 * @package    SPIP\Responsivenav\Pipelines
 */

 
// on insert la feuille de styles
function responsivenav_insert_head_css($flux) {
	$flux .="\n".'<link rel="stylesheet" href="'.produire_fond_statique('css/responsive-nav.css').'" type="text/css" />';
	$flux .="\n".'<link rel="stylesheet" href="'.produire_fond_statique('css/responsive-nav.spip.css').'" type="text/css" />';
	return $flux;
}

// on insert le script et on l'appelle
function responsivenav_insert_head($flux) {
	$flux .="\n".'<script type="text/javascript" src="'.find_in_path('javascript/responsive-nav.js').'"></script>';
	$flux .="\n".'<script>/*<![CDATA[*/ ;jQuery(function(){ $(function(){ if ($("'.lire_config('responsivenav/selector','#nav').'").length) { var nav = responsiveNav("'.lire_config('responsivenav/selector','#nav').'",{jsClass: "'._RESPONSIVENAV_JSCLASS.'",label: "'.lire_config('responsivenav/label','&#9776; Menu').'",insert: "'.lire_config('responsivenav/insert','before').'"' ;
	if (defined('_RESPONSIVENAV_ANIMATE')) {$flux .= ',animate: '._RESPONSIVENAV_ANIMATE;}
	if (defined('_RESPONSIVENAV_TRANSITION')) {$flux .= ',transition: '._RESPONSIVENAV_TRANSITION;}
	if (defined('_RESPONSIVENAV_CUSTOMTOGGLE')) {$flux .= ',customToggle: "'._RESPONSIVENAV_CUSTOMTOGGLE.'"';}
	if (defined('_RESPONSIVENAV_CLOSEONNAVCLICK')) {$flux .= ',closeOnNavClick: '._RESPONSIVENAV_CLOSEONNAVCLICK;}
	if (defined('_RESPONSIVENAV_OPENPOS')) {$flux .= ',openPos: "'._RESPONSIVENAV_OPENPOS.'"';}
	if (defined('_RESPONSIVENAV_NAVCLASS')) {$flux .= ',navClass: "'._RESPONSIVENAV_NAVCLASS.'"';}
	if (defined('_RESPONSIVENAV_NAVACTIVECLASS')) {$flux .= ',navActiveClass: "'._RESPONSIVENAV_NAVACTIVECLASS.'"';}
	$flux .= '}); } }); }); /*]]>*/ </script>';
	return $flux;
}
