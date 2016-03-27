<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Utilisations de pipelines par Responsive Nav
 *
 * @plugin     Responsive Nav
 * @copyright  2016
 * @author     jeanmarie
 * @licence    GNU/GPL
 * @package    SPIP\Responsivenav\Pipelines
 */

 
// on insert la feuille de styles
function responsivenav_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" href="'.produire_fond_statique('css/responsive-nav.css').'" type="text/css" />';
	$flux .= '<link rel="stylesheet" href="'.produire_fond_statique('css/responsive-nav.spip.css').'" type="text/css" />';
	return $flux;
}

// on insert le script et on l'appelle
function responsivenav_insert_head($flux) {
	$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/responsive-nav.js').'"></script>';
	$flux .= '<script>/*<![CDATA[*/ ;(function($){ $(function(){ var nav = responsiveNav("'. _RESPONSIVENAV_SELECTOR .'",{jsClass: "'. _RESPONSIVENAV_JSCLASS.'"' ;
	if (defined('_RESPONSIVENAV_ANIMATE')) {$flux .= ',animate: '._RESPONSIVENAV_ANIMATE;}
	if (defined('_RESPONSIVENAV_TRANSITION')) {$flux .= ',transition: '._RESPONSIVENAV_TRANSITION;}
	if (defined('_RESPONSIVENAV_LABEL')) {$flux .= ',label: "'._RESPONSIVENAV_LABEL.'"';}
	if (defined('_RESPONSIVENAV_INSERT')) {$flux .= ',insert: "'._RESPONSIVENAV_INSERT.'"';}
	if (defined('_RESPONSIVENAV_CUSTOMTOGGLE')) {$flux .= ',customToggle: "'._RESPONSIVENAV_CUSTOMTOGGLE.'"';}
	if (defined('_RESPONSIVENAV_CLOSEONNAVCLICK')) {$flux .= ',closeOnNavClick: '._RESPONSIVENAV_CLOSEONNAVCLICK;}
	if (defined('_RESPONSIVENAV_OPENPOS')) {$flux .= ',openPos: "'._RESPONSIVENAV_OPENPOS.'"';}
	if (defined('_RESPONSIVENAV_NAVCLASS')) {$flux .= ',navClass: "'._RESPONSIVENAV_NAVCLASS.'"';}
	if (defined('_RESPONSIVENAV_NAVACTIVECLASS')) {$flux .= ',navActiveClass: "'._RESPONSIVENAV_NAVACTIVECLASS.'"';}
	$flux .= '}); }); })(jQuery); /*]]>*/ </script>';
	return $flux;
}
?>