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
function formulaires_assigner_ticket_charger($id_ticket='', $retour='', $config_fonc='ticket_assigner_config', $row=array(), $hidden=''){
	
	if(is_numeric($id_ticket)){
		if (!autoriser('assigner', 'ticket', $id_ticket)) {
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
function formulaires_assigner_ticket_verifier($id_ticket='', $retour='', $config_fonc='ticket_assigner_config', $row=array(), $hidden=''){
	$erreurs = array();

	$id_assigne_ancien = sql_getfetsel("id_assigne","spip_tickets","id_ticket=".intval($id_ticket));
	$id_assigne = _request('id_assigne');
	if($id_assigne == $id_assigne_ancien){
		$erreurs['message_erreur'] = _T('tickets:assignation_non_modifiee');	
	}
	return $erreurs;
}

function ticket_assigner_config(){
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
function formulaires_assigner_ticket_traiter($id_ticket='',$retour='', $config_fonc='ticket_assigner_config', $row=array(), $hidden=''){
	$message = "";
	$id_assigne = _request('id_assigne');
	sql_updateq("spip_tickets",array('id_assigne' => $id_assigne),"id_ticket=$id_ticket");
	$message['message_ok'] = _T('tickets:assignation_modifiee');
	if($retour){
		$message['redirect'] = $retour;
	}
	
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('assignerticket', $id_ticket,
			array('id_auteur' => id_assigne)
		);
	}
	
	return $message;
}
?>