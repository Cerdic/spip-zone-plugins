<?php
// fichier charge a chaque recalcul
	global $cout_metas_pipelines;

	// fonctions indispensables a l'execution
	// include_spip('cout_lancement');
	cout_log("appel de mes_fonctions : strlen=" . strlen($cout_metas_pipelines['fonctions']));

	// inclusion des fonctions pre-compilees
	if (!$GLOBALS['tweak_fonctions']) include_once(sous_repertoire(_DIR_TMP, "tweak-spip").'mes_fonctions.php');
	cout_log(' -- appel mes_fonctions achevé... tweak_fonctions = ' . intval($GLOBALS['tweak_fonctions']));

/*
	if(!$GLOBALS['tweak_fonctions']) {
		if (isset($cout_metas_pipelines['fonctions'])) eval($cout_metas_pipelines['fonctions']);
cout_log(' -- appel mes_fonctions achevé par eval()... tweak_fonctions = '.intval($GLOBALS['tweak_fonctions']));
	}
*/
?>