<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function jqueryui_jquery_plugins($plugins){
	$config = @unserialize($GLOBALS['meta']['jqueryui']);
	
	if (!is_array($config) OR !is_array($config['plugins'])) {
		$config['plugins'] = array();
		$ecrire_meta = 'oui';
	}
	
	$config['plugins'] = array_unique(array_merge(sinon(pipeline('jqueryui_forcer'),array()),$config['plugins']));
	
	/**
	 * Gestion des dépendances inter plugins
	 */
	$dependance_core = array(
							'ui.accordion',
							'ui.datepicker',
							'ui.dialog',
							'ui.draggable',
							'ui.droppable',
							'ui.resizable',
							'ui.selectable',
							'ui.slider',
							'ui.sortable',
							'ui.tabs'
	);

	$dependance_draggable = array(
							'ui.droppable',
							'ui.dialog'
							);

	$dependance_resizable = array(
							'ui.dialog'
							);
	
	$dependance_effects = array(
							'effects.blind',
							'effects.bounce',
							'effects.clip',
							'effects.drop',
							'effects.explode',
							'effects.fold',
							'effects.highlight',
							'effects.pulsate',
							'effects.scale',
							'effects.shake',
							'effects.slide',
							'effects.transfer'
						);
	
	/**
	 * Vérification des dépendances
	 * Ici on ajoute quand même le plugin en question et on supprime les doublons via array_unique
	 * Pour éviter le cas où un pipeline demanderait un plugin dans le mauvais sens de la dépendance par exemple
	 * 
	 * On commence par le bas de l'échelle :
	 * - draggable
	 * - resizable
	 * - core
	 * - effects
	 */
	if(count($intersect = array_intersect($config['plugins'],$dependance_draggable)) > 0){
		$keys = array_keys($intersect);
		array_splice($config['plugins'],$keys[0], 0, "ui.draggable");
	}
	if(count($intersect = array_intersect($config['plugins'],$dependance_resizable)) > 0){
		$keys = array_keys($intersect);
		array_splice($config['plugins'],$keys[0], 0, "ui.resizable");
	}	
	if(count($intersect = array_intersect($config['plugins'],$dependance_core)) > 0){
		$keys = array_keys($intersect);
		array_splice($config['plugins'],$keys[0], 0, "ui.core");
	}
	if(count($intersect = array_intersect($config['plugins'],$dependance_effects)) > 0){
		$keys = array_keys($intersect);
		array_splice($config['plugins'],$keys[0], 0, "effects.core");
	}
	$config['plugins'] = array_unique($config['plugins']);
	foreach ($config['plugins'] as $val) {
		$plugins[] = _DIR_JQUERYUI_JS.$val.".js";
	}
	
	// si pas de config en base, on enregistre les scripts issu du pipeline jqueryui_forcer + leurs dépendances
	if ($ecrire_meta == 'oui') {
		include_spip('inc/meta');
		ecrire_meta('jqueryui',serialize($config));
	}

	return $plugins;
}

/**
 * jqueryui_insert_head : ajout des CSS de jQuery-UI pour les pages publiques
 * avec gestion du theme et des UI utilises
 * 
 * @param: $flux 
 * @return: $flux
 */
function jqueryui_insert_head($flux) {
	$config = @unserialize($GLOBALS['meta']['jqueryui']);

	// recuperer le repertoire du theme
	$theme = 'default/';
	if (isset($config['theme']) AND $config['theme'] != '')
		$theme = $config['theme'].'/';
	if ($theme == 'no_css/')
		return $flux;
	
	// recuperer la liste des plugins jquery actives ou issus du pipeline jqueryui_forcer
	$config['plugins'] = array_unique(array_merge(sinon(pipeline('jqueryui_forcer'),array()),$config['plugins']));

	// en 1.6 pas de CSS par plugin: ui.all.css comprend tout sauf datepicker
	if (!in_array('ui.all', $config['plugins']))
		$config['plugins'][] = 'ui.all';
		
	// les CSS correspondantes aux plugins
	$Tjquery_css = array(
						'ui.all',
						'ui.datepicker'
						);

	// appeler les CSS necessaires
	foreach ($config['plugins'] as $plug) {
		if (in_array($plug, $Tjquery_css)) {
			$flux .= "<link rel='stylesheet' type='text/css' media='all' href='".find_in_path(_DIR_JQUERYUI_CSS.$theme.$plug.'.css')."' />\n";
		}
	}

	return $flux;
}

?>
