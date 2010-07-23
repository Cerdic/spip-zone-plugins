<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function action_supprimer_canevas_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	list($id_canevas) = preg_split(',[^0-9],',$arg);
	include_spip('inc/autoriser');
	if (intval($id_canevas) AND autoriser('supprimer','canevas',$id_canevas,null,null)){
		include_spip("action/editer_canevas");
		canevas_action_supprime_canevas($id_canevas);
	}
}

?>