<?php

function action_supprimer_societe_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();
	
	if ($id_societe = intval($arg)) {
	 	include_spip('action/editer_societe');
	 	i2_societe_supprime_societe($id_societe);
	}
}
?>