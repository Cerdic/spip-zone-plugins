<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/agenda_gestion");
include_spip("inc/autoriser");

function exec_voir_evenement_dist()
{
	//$id_article = intval(_request('ajouter_id_article'));
	$id_evenement = intval(_request('id_evenement'));	
	//if (autoriser())
	$flag_editable = true; // autoriser()

	$voir_evenement = charger_fonction('voir_evenement','inc');
	$res = $voir_evenement($id_evenement,$flag_editable);

	ajax_retour($res);
}

?>