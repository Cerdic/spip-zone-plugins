<?php
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
    $flux .= '<script type="text/javascript" src="'.produire_fond_statique('js/responsive-nav.js').'"></script>';
	if (test_plugin_actif('Zpip')){
		$flux .= '<script>$(function() {  var nav = responsiveNav(".menu.menu-container",{jsClass: "js-responsivenav"});}); </script>';
		} else {
			$flux .= '<script>$(function() {  var nav = responsiveNav("#nav",{jsClass: "js-responsivenav"});}); </script>';
		}
	
    return $flux;
}

?>