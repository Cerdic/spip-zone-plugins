<?php
/**
 *
 * Fonction de chargement du formulaire de configuration CFG
 * On ajoute aux champs déjà dans la meta ce qui est activé par le pipeline 
 * jqueryui_forcer
 *
 */
function cfg_config_jqueryui_20_charger(&$cfg){
	$valeurs = &$cfg->val['plugins'];
	if(!is_array($valeurs))
		$valeurs = array();
	$plugins_pipeline = pipeline('jqueryui_forcer');
	if(!is_array($plugins_pipeline))
		$plugins_pipeline = array();
	$cfg->val['plugins'] = array_unique(array_merge($plugins_pipeline,$valeurs));
	$cfg->val['plugins_disable'] = $plugins_pipeline;
}

function cfg_config_jqueryui_20_pre_traiter(&$cfg){
	$valeurs = &$cfg->val['plugins'];
	if(!is_array($valeurs))
		$valeurs = array();
	$plugins_pipeline = pipeline('jqueryui_forcer');
	if(!is_array($plugins_pipeline))
		$plugins_pipeline = array();
	
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
	
	$valeurs = array_unique(array_merge($plugins_pipeline,$valeurs));
	
	/**
	 * Vérification des dépendances :
	 * On commence par le bas de l'échelle :
	 * - draggable
	 * - resizable
	 * - core
	 * - effects
	 */
	if((count($intersect = array_intersect($valeurs,$dependance_draggable)) > 0) && !in_array('ui.draggable',$valeurs)){
		$keys = array_keys($intersect);
		array_splice($valeurs,$keys[0], 0, "ui.draggable");
	}
	if((count($intersect = array_intersect($valeurs,$dependance_resizable)) > 0) && !in_array('ui.resizable',$valeurs)){
		$keys = array_keys($intersect);
		array_splice($valeurs,$keys[0], 0, "ui.resizable");
	}
	if((count($intersect = array_intersect($valeurs,$dependance_core)) > 0) && !in_array('ui.core',$valeurs)){
		$keys = array_keys($intersect);
		array_splice($valeurs,$keys[0], 0, "ui.core");
	}
	if((count($intersect = array_intersect($valeurs,$dependance_effects)) > 0) && !in_array('effects.core',$valeurs)){
		$keys = array_keys($intersect);
		array_splice($valeurs,$keys[0], 0, "effects.core");
	}
	
	set_request('plugins',$valeurs);
	$cfg->val['plugins'] = $valeurs;
}

?>
