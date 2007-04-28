<?php

// fichier charge a chaque hit
	global $cout_metas_pipelines;

	// pour forcer les logs du plugin, outil actif ou non :
	// $GLOBALS['forcer_log_couteau_suisse'] = true

	// on active tout de suite les logs, si l'outil est actif.
	$GLOBALS['log_couteau_suisse'] = (strpos($GLOBALS['meta']['tweaks_actifs'], 'log_couteau_suisse') !== false || $GLOBALS['forcer_log_couteau_suisse']);

	if($GLOBALS['log_couteau_suisse']) {
		spip_log('TWEAKS. ' . str_repeat('-', 80));
		spip_log('TWEAKS. appel de mes_options (début) : strlen=' . strlen($cout_metas_pipelines['options']));
	}
	// fonctions indispensables a l'execution
	include_spip('cout_lancement');
	cout_log("appel de mes_options (suite) : strlen=".strlen($cout_metas_pipelines['options']));

	// inclusion des options pre-compilees, si l'on n'est jamais passé par ici...
	if (!$GLOBALS['tweak_options']) {
		$file_exists = file_exists($f = sous_repertoire(_DIR_TMP, "tweak-spip").'mes_options.php');
		if($file_exists) include_once($f);
			// si les fichiers sont absents, on recompile tout
			else tweak_initialisation(1);
	}
	cout_log(' -- appel mes_options achevé... tweak_options = '.intval($GLOBALS['tweak_options']) . ($file_exists?' et fichier trouvé':' et fichier non trouvé !!'));

/*
	if(!$GLOBALS['tweak_options']) {
		if (isset($cout_metas_pipelines['options'])) eval($cout_metas_pipelines['options']);
cout_log(' -- appel mes_options achevé par eval() ... tweak_options = '.intval($GLOBALS['tweak_options']));
	}
*/
	 $GLOBALS['log_couteau_suisse'] |= $GLOBALS['forcer_log_couteau_suisse'];

?>