<?php

include_spip('base/abstract_sql');
include_spip('base/db_mysql.php');
include_spip('inc/utils.php');

function formulaires_etatfraisdon_charger_dist($id_fraisdon=0){
	$valeurs = array(
		'etat'=> ''
	);

	$result= spip_query("SELECT * FROM spip_fraisdons WHERE id_fraisdon=$id_fraisdon");
	if (sql_count($result) > 0) {
		$row = spip_fetch_array($result);
		$id_fraisdon= $row['id_fraisdon'];
		$valeurs['etat']= $row['etat'];
	}
	$valeurs['id_fraisdon']= $id_fraisdon;
	$valeurs['_hidden'] = "<input type='hidden' name='id_fraisdon' value='$id_fraisdon' />";

	return $valeurs;
}

function formulaires_etatfraisdon_traiter_dist(){
	$id_fraisdon= _request('id_fraisdon');
	$etat= _request('etat');

	$query= "UPDATE spip_fraisdons SET etat="._q($etat)." WHERE id_fraisdon=$id_fraisdon";
	$result= sql_query($query);
	$msg= "OK";
	return array('message_ok'=>$msg);
}

?>
