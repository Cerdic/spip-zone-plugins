<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function action_supprimer_gabarit_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	list($id_gabarit) = preg_split(',[^0-9],',$arg);
	include_spip('inc/autoriser');
	if (intval($id_gabarit) AND autoriser('supprimer','gabarit',$id_gabarit,null,null)){
		include_spip("action/editer_gabarit");
		gabarits_action_supprime_gabarit($id_gabarit);
	}
}

?>