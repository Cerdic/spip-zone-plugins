<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function action_supprimer_legende_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	list($id_legende) = preg_split(',[^0-9],',$arg);
	include_spip('inc/autoriser');
	if (intval($id_legende) AND autoriser('supprimer','legende',$id_legende,null,null)){
		include_spip("action/editer_legende");
		legendes_action_supprime_legende($id_legende);
	}
}

?>