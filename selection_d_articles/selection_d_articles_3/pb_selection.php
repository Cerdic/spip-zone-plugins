<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function pb_selection_interface ( $vars="" ) {
	$exec = $vars["args"]["exec"];

	if (!defined('_PB_SELECTION_RUBRIQUES_EXEC'))
		define('_PB_SELECTION_RUBRIQUES_EXEC', 'rubriques rubrique');	
	
	if (in_array($exec, explode(' ', _PB_SELECTION_RUBRIQUES_EXEC))) {
		
		$id_rubrique = $vars["args"]["id_rubrique"];
		$id_article = $vars["args"]["id_article"];
		$data =	$vars["data"];
	
		if ($id_rubrique < 1) $id_rubrique=0;
	
		$contexte = array('id_rubrique'=>$id_rubrique);

		$ret .= "<div id='pave_selection'>";
	
		$page = evaluer_fond("selection_interface", $contexte);
		$ret .= $page["texte"];

		$ret .= "</div>";

		$data = $ret.$data;

		$vars["data"] = $data;
	}


	return $vars;
}

function pb_selection_jqueryui_plugins($plugins) {
	if (_DIR_RACINE == "../") {
		$plugins[] = "jquery.ui.core";
		$plugins[] = "jquery.ui.widget";
		$plugins[] = "jquery.ui.mouse";
		$plugins[] = "jquery.ui.sortable";
	}
	return $plugins;
}

?>
