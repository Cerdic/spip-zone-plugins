<?php
// fichier charge a chaque recalcul
	global $cout_metas_pipelines;

	// fonctions indispensables a l'execution
	// include_spip('cout_lancement');
	cs_log("appel de cout_fonctions : strlen=" . strlen($cout_metas_pipelines['fonctions']));

	// inclusion des fonctions pre-compilees
	if (!$GLOBALS['tweak_fonctions']) include_once(sous_repertoire(_DIR_TMP, "couteau-suisse").'mes_fonctions.php');
	cs_log(' -- appel cout_fonctions achev�... tweak_fonctions = ' . intval($GLOBALS['tweak_fonctions']));

/*
	if(!$GLOBALS['tweak_fonctions']) {
		if (isset($cout_metas_pipelines['fonctions'])) eval($cout_metas_pipelines['fonctions']);
cs_log(' -- appel mes_fonctions achev� par eval()... tweak_fonctions = '.intval($GLOBALS['tweak_fonctions']));
	}
*/
?>