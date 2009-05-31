<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_typer_dist() {
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if($id = _request('id_rubrique')) {
		$objet = 'rubrique';
	}
	elseif($id = _request('id_article')) {
		$objet = 'article';
	}
	elseif (preg_match(",^\W*(\d+)\W(\w*)\W(\w*)$,", $arg, $r)) {
		$id = $r[1];
		$objet = $r[2];
		set_request('type', $r[3]);
	}
	elseif (!preg_match(",^\W*(\d+)\W(\w*)$,", $arg, $r)) {
		spip_log("action_type_dist $arg pas compris");
	}
	else {
		$id = $r[1];
		$objet = $r[2];
	}

	$id_objet = id_table_objet($objet);
	$table_objet = table_objet_sql($objet);
	$type = _request('type');
	sql_updateq($table_objet, array(_TYPE => $type), "$id_objet=$id");

	//Propagation
	if($objet == 'rubrique') {
		include_spip('inc/typer');
		propager_type($id, $type);
	}
}

?>