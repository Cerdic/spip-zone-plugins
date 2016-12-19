<?php
/**
 * Utilisations de pipelines par Modèle exergue
 *
 * @plugin     Modèle exergue
 * @copyright  2016
 * @author     Jacques Pyrat
 * @licence    GNU/GPL
 * @package    SPIP\Modelexergue\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// insert le css pour les styles supplementaires de la BTE dans le <head> du document (#INSERT_HEAD)
function modelexergue_insert_head_css($flux) {
	$flux.= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/modelexergue.css').'" />' . "\n";
	return $flux;
}
function modelexergue_header_prive($flux) {
	$flux.= '<link rel="stylesheet" type="text/css" href="' . _DIR_PLUGIN_MODELEXERGUE . 'css/modelexergue.css" />' . "\n";
	return $flux;
}

