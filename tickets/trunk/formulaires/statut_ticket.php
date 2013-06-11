<?php

/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2012
 * 
 * Formulaire d'assignation d'un ticket à un individu
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');
include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Fonction de chargement des valeurs
 */
function formulaires_statut_ticket_charger($id_ticket='', $retour='', $config_fonc='tickets_statut_config', $row=array(), $hidden=''){
	
	if(is_numeric($id_ticket)){
		if (!autoriser('instituer', 'ticket', $id_ticket)) {
			$editable = false;
		}else{
			$valeurs = formulaires_editer_objet_charger('ticket',$id_ticket,0,0,$retour,$config_fonc,$row,$hidden);
			$editable = true;
		}
	}else{
		$editable = false;
	}
	
	$valeurs['editable'] = $editable;
	return $valeurs;
}

/**
 * 
 * Fonction de vérification des valeurs
 * 
 * @return 
 * @param int $id_ticket[optional]
 * @param string $retour[optional] URL de retour
 * @param object $config_fonc[optional]
 * @param object $row[optional]
 */
function formulaires_statut_ticket_verifier($id_ticket='', $retour='', $config_fonc='tickets_statut_config', $row=array(), $hidden=''){
	$ancien_statut = sql_getfetsel("statut","spip_tickets","id_ticket=".intval($id_ticket));
	$nouveau_statut = _request('statut');
	if($ancien_statut == $nouveau_statut){
		$erreurs['message_erreur'] = _T('tickets:statut_inchange');
	}
	return $erreurs;
}

function tickets_statut_config(){
	return array();
}

/**
 * 
 * Fonction de traitement du formulaire
 * 
 * @return 
 * @param int $id_ticket[optional]
 * @param string $retour[optional]
 * @param object $config_fonc[optional]
 * @param object $row[optional]
 */
function formulaires_statut_ticket_traiter($id_ticket='',$retour='', $config_fonc='tickets_statut_config', $row=array(), $hidden=''){
	// pas d'ajax
	refuser_traiter_formulaire_ajax();
	$message = "";
	include_spip('action/editer_ticket');
	$c = array('statut'=>_request('statut'));
	ticket_instituer($id_ticket, $c);
	$message['message_ok'] = _T('tickets:statut_mis_a_jour');
	if($retour){
		$message['redirect'] = $retour;
	}

	return $message;
}
?>
