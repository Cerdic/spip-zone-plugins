<?php
// fichier charge a chaque recalcul
	global $tweaks_metas_pipes;

	// fonctions indispensables
	include_spip('tweak_spip');
tweak_log("appel de mes_fonctions : strlen=".strlen($tweaks_metas_pipes['fonctions']));

	// inclusion des fonctions pre-compilees
	if (!$GLOBALS['tweak_fonctions']) include_once(sous_repertoire(_DIR_TMP, "tweak-spip").'mes_fonctions.php');
	if (!$GLOBALS['tweak_fonctions']) eval($tweaks_metas_pipes['fonctions']);
tweak_log(' -- appel mes_fonctions achevé... tweak_fonctions = '.intval($GLOBALS['tweak_fonctions']));
?>