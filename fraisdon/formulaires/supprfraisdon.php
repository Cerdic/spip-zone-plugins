<?php

include_spip('base/abstract_sql');
include_spip('base/db_mysql.php');
include_spip('inc/utils.php');


function formulaires_supprfraisdon_charger_dist($id_fraisdon= '0'){
	$valeurs = array();
	$valeurs['_hidden'] = "<input type='hidden' name='id_fraisdon_suppr' value='$id_fraisdon' />";
	$valeurs['id_fraisdon_modif']= '';
	$valeurs['id_fraisdon_valid']= '';
	return $valeurs;
}

function formulaires_supprfraisdon_traiter_dist(){
	$id_fraisdon= _request('id_fraisdon_suppr');

	$result= sql_query("delete from spip_fraisdons where id_fraisdon = $id_fraisdon");
	return array('message_ok'=>_T('fraisdon:suppr_ok'));
}

?>
