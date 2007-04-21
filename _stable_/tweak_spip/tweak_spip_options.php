<?php
// fichier charge a chaque hit
	global $tweaks_metas_pipes;

	// pour forcer les logs du plugin, tweak actif ou non :
	// $GLOBALS['forcer_log_tweaks'] = true

	// on active tout de suite les logs, si le tweak est actif.
	$GLOBALS['log_tweaks'] = (strpos($GLOBALS['meta']['tweaks_actifs'], 'log_tweaks') !== false || $GLOBALS['forcer_log_tweaks']);

	if($GLOBALS['log_tweaks']) {
		spip_log('TWEAKS. ' . str_repeat('-', 80));
		spip_log('TWEAKS. appel de mes_options (début) : strlen=' . strlen($tweaks_metas_pipes['options']));
	}
	// fonctions indispensables a l'execution
	include_once _DIR_PLUGIN_TWEAK_SPIP . 'tweak_spip_init.php';
	tweak_log("appel de mes_options (suite) : strlen=".strlen($tweaks_metas_pipes['options']));

	// inclusion des options pre-compilees, si l'on n'est jamais passé par ici...
	if (!$GLOBALS['tweak_options']) {
		$file_exists = file_exists($f = sous_repertoire(_DIR_TMP, "tweak-spip").'mes_options.php');
		if($file_exists) include_once($f);
			// si les fichiers sont absents, on recompile tout
			else tweak_initialisation(1);
	}
	tweak_log(' -- appel mes_options achevé... tweak_options = '.intval($GLOBALS['tweak_options']) . ($file_exists?' et fichier trouvé':' et fichier non trouvé !!'));

/*
	if(!$GLOBALS['tweak_options']) {
		if (isset($tweaks_metas_pipes['options'])) eval($tweaks_metas_pipes['options']);
tweak_log(' -- appel mes_options achevé par eval() ... tweak_options = '.intval($GLOBALS['tweak_options']));
	}
*/
	 $GLOBALS['log_tweaks'] |= $GLOBALS['forcer_log_tweaks'];

?>