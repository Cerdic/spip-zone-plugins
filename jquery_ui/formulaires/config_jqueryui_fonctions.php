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
	spip_log($cfg->val['plugins'],'test');
}


?>
