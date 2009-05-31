<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/agenda_gestion");

function exec_voir_agenda_dist()
{
	$id_article = intval(_request('ajouter_id_article'));
	$id_evenement = intval(_request('id_evenement'));	
	$flag_editable = true; // autoriser()
	
	// hack pour faire marcher le calendrier
	$GLOBALS['REQUEST_URI'] = preg_replace(',exec=[^&]*,','exec=calendrier',$GLOBALS['REQUEST_URI']);
	
	$voir_agenda = charger_fonction("voir_agenda","inc");
	$res = $voir_agenda($flag_editable);

	ajax_retour($res);
}

?>