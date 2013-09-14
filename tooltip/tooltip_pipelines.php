<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
/**
 * Insertion dans le pipeline insert_head_css (SPIP)
 * 
 * On n'ajoute la CSS de l'ancienne version de jquery tooltips que lorsque la version de 
 * jquery ui est < à 1.10.1, dans le cas inverse, c'est jqueryui qui fait le travail
 * 
 * @param string $flux
 * 		Le contenu de la balise #INSERT_HEAD_CSS
 * @return string $flux
 * 		Le contenu de la balise #INSERT_HEAD_CSS complétée
 */
function tooltip_insert_head_css($flux) {
	$f = chercher_filtre('info_plugin');
	include_spip('plugins/installer');
	if(!function_exists('spip_version_compare') || spip_version_compare($f('jqueryui','version'),'1.10.1','<')){
		$flux .= '<link rel="stylesheet" type="text/css" href="'.direction_css(find_in_path('css/tooltip.css')).'" media="all" />'."\n";
	}
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * 
 * On ajoute un bloc javascript dans le head des pages si le sélecteur sur lequel
 * appliquer les tooltips est configuré
 * 
 * @param string $flux
 * 		Le contenu de la balise #INSERT_HEAD
 * @return string $flux
 * 		Le contenu de la balise #INSERT_HEAD complétée
 */
function tooltip_insert_head($flux) {
	$config = @unserialize($GLOBALS['meta']['tooltip']);
	if (!is_array($config))
		$config = array();
	if(isset($config['selecteur']) && strlen($config['selecteur']) > 0){
		$flux .=
			'<script type="text/javascript">/* <![CDATA[ */
				var tooltip_init=function(){
					if($("'.$config['selecteur'].'").size() > 0)
						$("'.$config['selecteur'].'").tooltip();
				}
				$(document).ready(function(){
					tooltip_init();
				});
				onAjaxLoad(tooltip_init);
			/* ]]> */</script>
			';
	}
	return $flux;
}

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * 
 * On n'ajoute l'ancienne version de jquery tooltips que lorsque la version de jquery ui est
 * < à 1.10.1, dans le cas inverse, on passe par jqueryui_plugins
 * 
 * @param array $plugins
 * 		Le tableau des plugins jQuery ajoutés dans le head
 * @return array $plugins
 * 		Le tableau des plugins complétés
 */
function tooltip_jquery_plugins($plugins){
	$f = chercher_filtre('info_plugin');
	include_spip('plugins/installer');
	if(!function_exists('spip_version_compare') || spip_version_compare($f('jqueryui','version'),'1.10.1','<')){
		$plugins[] = 'lib/bgiframe.js';
		$plugins[] = 'lib/delegate.js';
		$plugins[] = 'lib/dimensions.js';
		$plugins[] = 'demo/chili-1.7.pack.js';
		$plugins[] = 'js/tooltip.js';
	}
	return $plugins;
}

/**
 * Insertion dans le pipeline jqueryui_plugins (Plugin jQuery UI)
 * 
 * On n'ajoute l'ancienne version de jquery tooltips que lorsque la version de jquery ui est
 * < à 1.10.1, dans le cas inverse, on passe par jqueryui_plugins
 * 
 * @param array $plugins
 * 		Le tableau des plugins jQuery ajoutés dans le head
 * @return array $plugins
 * 		Le tableau des plugins complétés
 */
function tooltip_jqueryui_plugins($plugins){
	$f = chercher_filtre('info_plugin');
	include_spip('plugins/installer');
	if(!function_exists('spip_version_compare') || spip_version_compare($f('jqueryui','version'),'1.10.1','<')){
		$plugins[] = "jquery.ui.tooltip";
	}
	return $plugins;
}
?>