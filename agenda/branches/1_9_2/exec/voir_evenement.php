<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/agenda_gestion");
include_spip("inc/autoriser");

function exec_voir_evenement_dist()
{
	$id_article = intval(_request('ajouter_id_article'));
	$id_evenement = intval(_request('id_evenement'));	
	//if (autoriser())
	$flag_editable = true; // autoriser()

	$res = "";
	if ((_request('edit')||($neweven=_request('neweven')))&&($flag_editable))	{ //---------------Edition RDV ------------------------------
		$ndate = _request('ndate');
		$form .= Agenda_formulaire_edition_evenement($id_evenement,$neweven,$ndate);
		$url = parametre_url(self(),'exec','','&');
		$url = parametre_url($url,'edit','','&');
		$url = parametre_url($url,'neweven','','&');
		$args = explode('?',$url);
		$res .= ajax_action_auteur('voir_evenement',"0-modifier-$id_article-$id_evenement", 'calendrier', end($args), $form,'','reload_agenda');
	}
	else {
		$voir_evenement = charger_fonction('voir_evenement','inc');
		$res .= $voir_evenement($id_evenement,$flag_editable);
	}

	ajax_retour($res);
}

?>