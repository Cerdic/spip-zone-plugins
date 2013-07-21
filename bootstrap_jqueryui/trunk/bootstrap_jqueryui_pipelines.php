<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if(function_exists('jqueryui_insert_head_css_dist')){
	function jqueryui_insert_head_css($flux) {
		return $flux;	
	}
}
/**
 * Ajout des css de jQuery UI pour les pages publiques
 * 
 * @param: $flux 
 * @return: $flux
 */
function bootstrap_jqueryui_insert_head_css($flux) {

	// Modules demandés par le pipeline jqueryui_plugins
	is_array($jqueryui_plugins = pipeline('jqueryui_plugins', array())) || $jqueryui_plugins = array();
	// gestion des dépendances des modules demandés
	is_array($jqueryui_plugins = jqueryui_dependances($jqueryui_plugins)) || $jqueryui_plugins = array();

	// ajouter le thème si nécessaire
	if ($jqueryui_plugins AND !in_array('jquery.ui.theme', $jqueryui_plugins))
		array_unshift($jqueryui_plugins,'jquery.ui.core');

	// les css correspondantes aux plugins
	$styles = array(
						'jquery.ui.accordion',
						'jquery.ui.autocomplete',
						'jquery.ui.button',
						'jquery.ui.core',
						'jquery.ui.datepicker',
						'jquery.ui.dialog',
						'jquery.ui.progressbar',
						'jquery.ui.resizable',
						'jquery.ui.selectable',
						'jquery.ui.slider',
						'jquery.ui.tabs',
						'jquery.ui.theme'
						);

	// insérer les css nécessaires
	foreach ($jqueryui_plugins as $plugin) {
		if (in_array($plugin, $styles)) {
			$css = lesscss_select_css('css/boot'.$plugin.'.css');
			$flux .= "<link rel='stylesheet' type='text/css' media='all' href='".$css."' />\n";
		}
	}

	return $flux;
}