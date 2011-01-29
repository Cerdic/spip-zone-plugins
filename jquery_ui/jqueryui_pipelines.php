<?php
define('_DIR_JQUERYUI_JS','lib/jquery-ui-1.8.9/ui/');

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
							'jquery.ui.mouse',
							'jquery.ui.widget',
							'jquery.ui.datepicker'
	);

	/**
	 * Dépendances à widget
	 * Si un autre plugin est dépendant d'un de ceux là, on ne les ajoute pas
	 */
	$dependance_widget = array(
							'jquery.ui.mouse',
							'jquery.ui.accordion',
							'jquery.ui.autocomplete',
							'jquery.ui.button',
							'jquery.ui.dialog',
							'jquery.ui.tabs',
							'jquery.ui.progressbar'						
							);
	
	$dependance_mouse = array(
							'jquery.ui.draggable',
							'jquery.ui.droppable',
							'jquery.ui.resizable',
							'jquery.ui.selectable',
							'jquery.ui.sortable',
							'jquery.ui.slider'
						);
	
	$dependance_position = array(
							'jquery.ui.autocomplete',
							'jquery.ui.dialog',
							);
	
	$dependance_draggable = array(
							'jquery.ui.droppable'
							);
	
	$dependance_effects = array(
							'jquery.effects.blind',
							'jquery.effects.bounce',
							'jquery.effects.clip',
							'jquery.effects.drop',
							'jquery.effects.explode',
							'jquery.effects.fold',
							'jquery.effects.highlight',
							'jquery.effects.pulsate',
							'jquery.effects.scale',
							'jquery.effects.shake',
							'jquery.effects.slide',
							'jquery.effects.transfer'
						);
	
	/**
	 * Vérification des dépendances
	 * Ici on ajoute quand même le plugin en question et on supprime les doublons via array_unique
	 * Pour éviter le cas où un pipeline demanderait un plugin dans le mauvais sens de la dépendance par exemple
	 * 
	 * On commence par le bas de l'échelle :
	 * - draggable
	 * - position
	 * - mouse
	 * - widget
	 * - core
	 * - effects
	 */
	if(count($intersect = array_intersect($config['plugins'],$dependance_draggable)) > 0){
		$keys = array_keys($intersect);
		array_splice($config['plugins'],$keys[0], 0, "jquery.ui.draggable");
	}
	if(count($intersect = array_intersect($config['plugins'],$dependance_position)) > 0){
		$keys = array_keys($intersect);
		array_splice($config['plugins'],$keys[0], 0, "jquery.ui.position");
	}
	if(count($intersect = array_intersect($config['plugins'],$dependance_mouse)) > 0){
		$keys = array_keys($intersect);
		array_splice($config['plugins'],$keys[0], 0, "jquery.ui.mouse");
	}
	if(count($intersect = array_intersect($config['plugins'],$dependance_widget)) > 0){
		$keys = array_keys($intersect);
		array_splice($config['plugins'],$keys[0], 0, "jquery.ui.widget");
	}
	if(count($intersect = array_intersect($config['plugins'],$dependance_core)) > 0){
		$keys = array_keys($intersect);
		array_splice($config['plugins'],$keys[0], 0, "jquery.ui.core");
	}
	if(count($intersect = array_intersect($config['plugins'],$dependance_effects)) > 0){
		$keys = array_keys($intersect);
		array_splice($config['plugins'],$keys[0], 0, "jquery.effects.core");
	}
	if(count($intersect = array_intersect($config['plugins'],$dependance_effects)) > 0){
		$keys = array_keys($intersect);
		array_splice($config['plugins'],$keys[0], 0, "jquery.effects.core");
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

?>
