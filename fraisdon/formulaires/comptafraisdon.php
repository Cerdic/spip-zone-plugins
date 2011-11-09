<?php

include_spip('base/abstract_sql');
include_spip('base/db_mysql.php');
include_spip('inc/utils.php');

function formulaires_comptafraisdon_charger_dist(){
	$valeurs = array();
	return $valeurs;
}

function formulaires_comptafraisdon_traiter_dist(){
	$anneecomptable= _request('anneecomptable');
	$regroupement= _request('regroupement');
	$etat= _request('etat');
	$ids= _request('ids');

	$query= "UPDATE spip_fraisdons SET anneecomptable="._q($anneecomptable).", regroupement="._q($regroupement).", etat="._q($etat)." WHERE id_fraisdon in $ids";
	$result= sql_query($query);
	$msg= _T('fraisdon:modif_ok');

	return array('message_ok'=>$msg);
}

?>
