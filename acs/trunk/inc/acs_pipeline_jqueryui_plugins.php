<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');

function  acs_jqueryui_plugins($scripts){
	if (autoriser('acs', 'jqueryui_plugins')) {
		$scripts[] = "jquery.ui.draggable";
		$scripts[] = "jquery.ui.droppable";
		acs_log('acs_jqueryui_plugins()'.dbg($scripts));
	}
	return $scripts;
}
?>