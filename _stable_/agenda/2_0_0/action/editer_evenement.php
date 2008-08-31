<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/agenda_gestion");
include_spip("inc/editer_evenement");

function action_editer_evenement_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$arg = explode('-',_request('arg'));
	$id_article = $arg[0];
	$action = $arg[1];
	$id_evenement = $arg[2];
	$redirect = urldecode(_request('redirect'));

	if ($action=='modifier')
		//if (autoriser())
		$id_evenement = Agenda_action_formulaire_article($id_article,$id_evenement);
	elseif ($action=='supprimer')
		//if (autoriser())
		$id_evenement = Agenda_action_supprime_evenement($id_article,$id_evenement);
	elseif ($action=='saisierapidecompiler'){
		$redirect = parametre_url($redirect,'evenements_saisie_rapide',_request('evenements_saisie_rapide'),'&');
		$id_evenement = 0;
	}
	elseif ($action=='saisierapidecreer'){
		include_spip('inc/agenda_saisie_rapide');
		$evenements_saisie_rapide = _request('evenements_saisie_rapide');
		$t = Agenda_compile_texte_saisie_rapide($evenements_saisie_rapide);
		foreach($t as $e) if(count($e)) {
			$e['evenement_insert']=1;
			Agenda_action_formulaire_article($id_article,$id_evenement,$e);
		}
	}

	if ($redirect){
		if ($id_evenement)
			$redirect = parametre_url($redirect,'id_evenement',$id_evenement,'&');
		redirige_par_entete($redirect);
	}
}

?>