<?php
//include_spip('public/interfaces'); global $table_des_traitements; echo'fonctions:'; var_dump($table_des_traitements['TEXTE']);
// fichier charge a chaque recalcul
	global $tweaks_metas_pipes;
	// fonctions indispensables a l'execution
	include_once _DIR_PLUGINS.'tweak_spip/tweak_spip_init.php';
tweak_log("appel de mes_fonctions : strlen=".strlen($tweaks_metas_pipes['fonctions']));

	// inclusion des fonctions pre-compilees
	if (!$GLOBALS['tweak_fonctions']) include_once(sous_repertoire(_DIR_TMP, "tweak-spip").'mes_fonctions.php');
tweak_log(' -- appel mes_fonctions achevé... tweak_fonctions = '.intval($GLOBALS['tweak_fonctions']));
/*
	if(!$GLOBALS['tweak_fonctions']) {
		if (isset($tweaks_metas_pipes['fonctions'])) eval($tweaks_metas_pipes['fonctions']);
tweak_log(' -- appel mes_fonctions achevé par eval()... tweak_fonctions = '.intval($GLOBALS['tweak_fonctions']));
	}
*/
?>