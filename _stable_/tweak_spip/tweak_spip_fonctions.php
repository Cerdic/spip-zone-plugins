<?php
// fichier charge a chaque recalcul
	global $tweaks_metas_pipes;

	// fonctions indispensables
	include_spip('tweak_spip');
tweak_log("appel de mes_fonctions : strlen=".strlen($tweaks_metas_pipes['fonctions']));

	// inclusion des fonctions pre-compilees
//	eval($tweaks_metas_pipes['fonctions']);
	include_once(_DIR_TMP.'tweak-spip/mes_fonctions.php');
tweak_log(" -- appel mes_fonctions achevé...");
?>