<?php
/**
 * Utilisations de pipelines par d3js
 *
 * @plugin     d3js
 * @copyright  2015
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\D3js\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajout des scripts de d3js dans le head des pages publiques
 *
 *
 * @pipeline jquery_plugins
 */
function d3js_jquery_plugins($plugins){
	// Modules demandés par le pipeline jqueryui_plugins
	$d3js_plugins = array('d3.min');

	$d3js_plugins = pipeline('d3js_plugins', $d3js_plugins);

	// insérer les scripts nécessaires
	foreach ($d3js_plugins as $val) {
		$plugins[] = "javascript/".$val.".js";
	}

	return $plugins;
}

/**
 * Ajoute les css pour d3js chargées dans le privé
 * 
 * @param string $flux Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
 */
function d3js_header_prive_css($flux) {

	$css = find_in_path('css/d3js.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='".direction_css($css)."' />\n";

	return $flux;
}

/**
 * Ajoute les css pour d3js chargées dans le public
 * 
 * @param string $flux Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
**/
function d3js_insert_head_css($flux) {
	$css = find_in_path('css/d3js.css');
	$flux .= '<link rel="stylesheet" href="'.direction_css($css).'" type="text/css" media="all" />';

	return $flux;
}

?>