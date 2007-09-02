<?php
	// Puisque ce plugin n'est pas destine (pour l'instant) a abandonner la compatibilite avec 1.9.1
	define('_SIGNALER_ECHOS', false); // horrible      
// fichier charge a chaque hit
	global $cs_metas_pipelines;

	// pour forcer les logs du plugin, outil actif ou non :
	// $GLOBALS['forcer_log_couteau_suisse'] = true;

	// on active tout de suite les logs, si l'outil est actif.
	$GLOBALS['log_couteau_suisse'] = (strpos($GLOBALS['meta']['tweaks_actifs'], 'log_couteau_suisse') !== false || $GLOBALS['forcer_log_couteau_suisse']);

	if($GLOBALS['log_couteau_suisse']) {
		spip_log('COUTEAU-SUISSE. ' . str_repeat('-', 80));
		spip_log('COUTEAU-SUISSE. appel de cout_options (début) : strlen=' . strlen($cs_metas_pipelines['options']));
	}
	// fonctions indispensables a l'execution
	include_spip('cout_lancement');
	cs_log("appel de cout_options (suite) : strlen=".strlen($cs_metas_pipelines['options']));

	// inclusion des options pre-compilees, si l'on n'est jamais passé par ici...
	if (!$GLOBALS['cs_options']) {
		$file_exists = file_exists($f = sous_repertoire(_DIR_TMP, "couteau-suisse").'mes_options.php');
		if($file_exists) include_once($f);
			// si les fichiers sont absents, on recompile tout
			else cs_initialisation(1);
	}
	cs_log(' -- appel de cout_options achevé... cs_options = '.intval($GLOBALS['cs_options']) 
		. ($file_exists?" et fichier '$f' trouvé":" et fichier '$f' non trouvé !!"));

/*
	if(!$GLOBALS['cs_options']) {
		if (isset($cs_metas_pipelines['options'])) eval($cs_metas_pipelines['options']);
cs_log(' -- appel de cout_options achevé par eval() ... cs_options = '.intval($GLOBALS['cs_options']));
	}
*/
	 $GLOBALS['log_couteau_suisse'] |= $GLOBALS['forcer_log_couteau_suisse'];

?>