<?php

function action_echoppe_sauver_depot(){
	include_spip('inc/utils');
	$contexte = Array();
	$contexte['new'] = _request('new');
	$contexte['id_depot'] = _request('id_depot');
	$contexte['titre'] = _request('titre');
	$contexte['description'] = _request('texte');
	$contexte['adresse'] = serialize(_request('adresse'));
	
	if ($contexte['new'] == "oui"){
		$sql_sauver_depot = "INSERT INTO spip_echoppe_depots VALUES ('', '".addslashes($contexte['titre'])."', '".addslashes($contexte['description'])."', '".addslashes($contexte['adresse'])."', NOW());";
		$res_sauver_depot = spip_query($sql_sauver_depot);
		$contexte['id_depot'] == spip_insert_id();
		
	}else{
		$sql_sauver_depot = "UPDATE spip_echoppe_depots SET titre='".addslashes($contexte['titre'])."', descriptif='".addslashes($contexte['description'])."', adresse='".addslashes($contexte['adresse'])."', maj = NOW() WHERE id_depot='".$contexte['id_depot']."';";
		$res_sauver_depot = spip_query($sql_sauver_depot);
	}
	//echo "SQL1 - ".$sql_sauver_depot;
	
	$redirect = generer_url_ecrire('echoppe_gerer_depots');
	redirige_par_entete($redirect);
	
}

?>
