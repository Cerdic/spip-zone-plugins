<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Pipeline jqueryui_forcer pour demander au plugin l'insertion des scripts pour .sortable()
 *
 * @param array $plugins
 * @return array
 */
function contact_jqueryui_forcer($plugins){
	$plugins[] = "jquery.ui.core";
	$plugins[] = "jquery.ui.widget";
	$plugins[] = "jquery.ui.mouse";
	$plugins[] = "jquery.ui.sortable";
	return $plugins;
}

?>