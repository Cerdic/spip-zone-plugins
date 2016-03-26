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

if (!defined('_ECRIRE_INC_VERSION')) return;

 
// on insert la feuille de styles
function responsivenav_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" href="'.produire_fond_statique('css/responsive-nav.css').'" type="text/css" />';
	$flux .= '<link rel="stylesheet" href="'.produire_fond_statique('css/responsive-nav.spip.css').'" type="text/css" />';
	return $flux;
}

// on insert le script
function responsivenav_insert_head($flux) {
	$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/responsive-nav.js').'"></script>';
	$flux .= '<script>/*<![CDATA[*/ ;(function($){ $(function(){ var nav = responsiveNav("'. _RESPONSIVENAV_SELECTOR .'",{jsClass: "'. _RESPONSIVENAV_JSCLASS .'"}); }); })(jQuery); /*]]>*/ </script>';
	return $flux;
}

?>