<?php

include_spip('base/abstract_sql');
include_spip('base/db_mysql.php');
include_spip('inc/utils.php');

function formulaires_validfraisdon_charger_dist($id_fraisdon=0){
	$valeurs = array();
	$valeurs['_hidden'] = "<input type='hidden' name='id_fraisdon_valid' value='$id_fraisdon' />";
	$valeurs['id_fraisdon_modif']= '';
	$valeurs['id_fraisdon_suppr']= '';
	return $valeurs;
}

function formulaires_validfraisdon_traiter_dist(){
	$id_fraisdon= _request('id_fraisdon_valid');
	$etat= _request('etat');

	$query= "UPDATE spip_fraisdons SET etat="._q($etat)." WHERE id_fraisdon=$id_fraisdon";
	$result= sql_query($query);
	$msg= _T('fraisdon:modif_ok');

	return array('message_ok'=>$msg);
}

?>
