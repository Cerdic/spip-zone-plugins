<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline jqueryui_forcer (plugin jQueryUI)
 * 
 * On ajoute le chargement des js nécessaires
 * @param array $plugins Un tableau des scripts déjà demandé au chargement
 * @retune array $plugins Le tableau complété avec les scripts que l'on souhaite 
 */
function jfu_jqueryui_forcer($plugins){
	$plugins[] = "jquery.ui.core";
	$plugins[] = "jquery.ui.widget";
	$plugins[] = "jquery.ui.button";
	$plugins[] = "jquery.ui.progressbar";
	return $plugins;
}
?>