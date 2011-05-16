<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function action_supprimer_evenement_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	
	list($id_evenement,$id_article) = preg_split(',[^0-9],',$arg);
	include_spip('inc/autoriser');
	if (intval($id_article) AND intval($id_evenement) AND autoriser('supprimer','evenement',$id_evenement,null,array('id_article'=>$id_article))){
		include_spip("action/editer_evenement");
		agenda_action_supprime_evenement($id_article,$id_evenement);
	}
}

?>