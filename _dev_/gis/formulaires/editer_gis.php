<?php

function formulaires_editer_gis_charger_dist($id_article){

	$valeurs = array(
		'lat'=> '',
		'lonx'=> '',
		'editable'=>true
	);
	
	$row = sql_fetsel("lat,lonx", "spip_gis", "id_article=$id_article");
	if ($row) {
		$valeurs['lat'] = $row['lat'];
		$valeurs['lonx'] = $row['lonx'];
	}
	
	return $valeurs;
}


function formulaires_editer_gis_verifier_dist($id_article){

	$erreurs = array();

	return $erreurs;
}


function formulaires_editer_gis_traiter_dist($id_article){
	$res = array('editable'=>' ');
	
	// recuperation des donnees
	$lat = _request('lat');
	$lonx = _request('lonx');
	
	include_spip('base/abstract_sql');
	
	// mise a jour ou creation ?
	if ($id_gis = sql_getfetsel("id_gis", "spip_gis", "id_article=$id_article")) {
		sql_updateq("spip_gis",
					array("id_article" => $id_article , "lat" => $lat, "lonx" => $lonx),
					"id_gis=$id_gis");
		$res['message_ok'] = _T('gis:coord_maj');
	}else{
		sql_insertq("spip_gis",  array("id_article" => $id_article , "lat" => $lat, "lonx" => $lonx));
		$res['message_ok'] = _T('gis:coord_enregistre');
	}
	return $res;
}

?>