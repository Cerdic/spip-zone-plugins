<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function pb_selection_interface ( $vars="" ) {
	$exec = $vars["args"]["exec"];
	$id_rubrique = $vars["args"]["id_rubrique"];
	$id_article = $vars["args"]["id_article"];
	$data =	$vars["data"];
	
	if ($id_rubrique < 1) $id_rubrique=0;
	
	
	
	if ($exec == "rubriques" OR $exec == "rubrique") {
		
		$contexte = array('id_rubrique'=>$id_rubrique);

		$ret .= "<div id='pave_selection'>";
	
		$page = evaluer_fond("selection_interface", $contexte);
		$ret .= $page["texte"];

		$ret .= "</div>";
	}


	$data = $ret.$data;

	$vars["data"] = $data;
	return $vars;
}

function pb_selection_jqueryui_plugins($plugins) {
	$plugins[] = "jquery.ui.core";
	$plugins[] = "jquery.ui.widget";
	$plugins[] = "jquery.ui.mouse";
	$plugins[] = "jquery.ui.sortable";
	return $plugins;
}

?>
