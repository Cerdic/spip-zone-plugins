<?php
function id_societe_to_societe($id_societe){
	if($id_societe != 0){
		$societe = sql_getfetsel('nom', 'spip_societes', 'id_societe ='.intval($id_societe));
		return $societe;
	}
	else return;
}
?>