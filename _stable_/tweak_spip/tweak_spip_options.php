<?php
// fichier charge a chaque hit
	global $tweaks_metas_pipes;

	// on active tout de suite les logs, si le tweak est actif.
	$GLOBALS['log_tweaks'] = (strpos($GLOBALS['meta']['tweaks_actifs'], 'log_tweaks') !== false);

if($GLOBALS['log_tweaks']) { 
	spip_log('TWEAKS. '.str_repeat('-', 80));
	spip_log('TWEAKS. appel de mes_options (début) : strlen='.strlen($tweaks_metas_pipes['options']));
}
	// fonctions indispensables a l'execution
	include_spip('tweak_spip_init');
tweak_log("appel de mes_options (suite) : strlen=".strlen($tweaks_metas_pipes['options']));

	// inclusion des options pre-compilees
	if (!$GLOBALS['tweak_options']) include_once(sous_repertoire(_DIR_TMP, "tweak-spip").'mes_options.php');
	if (!$GLOBALS['tweak_options'] && isset($tweaks_metas_pipes['options'])) eval($tweaks_metas_pipes['options']);
tweak_log(' -- appel mes_options achevé... tweak_options = '.intval($GLOBALS['tweak_options']));
?>