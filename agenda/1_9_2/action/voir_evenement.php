<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/agenda_gestion");
include_spip("inc/editer_evenement");

function action_voir_evenement_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$arg = explode('-',_request('arg'));
	$action = $arg[1];
	$id_article = $arg[2];
	$id_evenement = $arg[3];
	$redirect = urldecode(_request('redirect'));
	if ($action=='modifier'){
		//if (autoriser())
		$id_evenement = Agenda_action_formulaire_article($id_article,$id_evenement);
		if ($id_evenement)
			$redirect = parametre_url($redirect,'id_evenement',$id_evenement,'&');
	}
	
	if ($redirect){
		redirige_par_entete($redirect);
	}
}

?>