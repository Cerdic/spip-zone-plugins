<?php
/**
 *
 * Fonction de chargement du formulaire de configuration CFG
 * On ajoute aux champs déjà dans la meta ce qui est activé par le pipeline 
 * jqueryui_forcer
 *
 */
function cfg_config_jqueryui_charger(&$cfg){
	$valeurs = &$cfg->val['plugins'];
	if(!is_array($valeurs))
		$valeurs = array();
	$plugins_pipeline = pipeline('jqueryui_forcer');
	if(!is_array($plugins_pipeline))
		$plugins_pipeline = array();
	$cfg->val['plugins'] = array_unique(array_merge($plugins_pipeline,$valeurs));
	$cfg->val['plugins_disable'] = $plugins_pipeline;
}

function cfg_config_jqueryui_pre_traiter(&$cfg){
	$valeurs = &$cfg->val['plugins'];
	if(!is_array($valeurs))
		$valeurs = array();
	$plugins_pipeline = pipeline('jqueryui_forcer');
	if(!is_array($plugins_pipeline))
		$plugins_pipeline = array();
	
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
	
	$valeurs = array_unique(array_merge($plugins_pipeline,$valeurs));
	
	/**
	 * Vérification des dépendances :
	 * On commence par le bas de l'échelle :
	 * - draggable
	 * - position
	 * - mouse
	 * - widget
	 * - core
	 * - effects
	 */
	if((count($intersect = array_intersect($valeurs,$dependance_draggable)) > 0) && !in_array('jquery.ui.draggable',$valeurs)){
		$keys = array_keys($intersect);
		array_splice($valeurs,$keys[0], 0, "jquery.ui.draggable");
	}
	if((count($intersect = array_intersect($valeurs,$dependance_position)) > 0) && !in_array('jquery.ui.position',$valeurs)){
		$keys = array_keys($intersect);
		array_splice($valeurs,$keys[0], 0, "jquery.ui.position");
	}
	if((count($intersect = array_intersect($valeurs,$dependance_mouse)) > 0) && !in_array('jquery.ui.mouse',$valeurs)){
		$keys = array_keys($intersect);
		array_splice($valeurs,$keys[0], 0, "jquery.ui.mouse");
	}
	if((count($intersect = array_intersect($valeurs,$dependance_widget)) > 0) && !in_array('jquery.ui.widget',$valeurs)){
		$keys = array_keys($intersect);
		array_splice($valeurs,$keys[0], 0, "jquery.ui.widget");
	}
	if((count($intersect = array_intersect($valeurs,$dependance_core)) > 0) && !in_array('jquery.ui.core',$valeurs)){
		$keys = array_keys($intersect);
		array_splice($valeurs,$keys[0], 0, "jquery.ui.core");
	}
	if((count($intersect = array_intersect($valeurs,$dependance_effects)) > 0) && !in_array('jquery.effects.core',$valeurs)){
		$keys = array_keys($intersect);
		array_splice($valeurs,$keys[0], 0, "jquery.effects.core");
	}
	if((count($intersect = array_intersect($valeurs,$dependance_effects)) > 0) && !in_array('jquery.effects.core',$valeurs)){
		$keys = array_keys($intersect);
		array_splice($valeurs,$keys[0], 0, "jquery.effects.core");
	}
	
	set_request('plugins',$valeurs);
	$cfg->val['plugins'] = $valeurs;
}

?>
