<?php

/**
 * Formulaire d'édition du livrable d'un ticket
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/autoriser');

/**
 * Fonction de chargement des valeurs
 */
function formulaires_ticket_livrable_charger($id_ticket=''){
	
	if(is_numeric($id_ticket)){
		if (!autoriser('ecrire', 'ticket', $id_ticket)) {
			$editable = false;
		}else{
			$id_livrable = _request('id_livrable');
			if (!_request('id_livrable'))
				$id_livrable = sql_getfetsel('id_livrable', 'spip_tickets', 'id_ticket=' . intval($id_ticket));
			$valeurs['id_ticket'] = $id_ticket;
			$valeurs['id_livrable'] = $id_livrable;
			$valeurs['test'] = sql_countsel('spip_tickets LEFT JOIN spip_livrables USING (id_livrable)', "spip_tickets.id_livrable=60 AND spip_tickets.statut IN('termine','resolu')");
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
 */
function formulaires_ticket_livrable_verifier($id_ticket=''){
	$erreurs = array();

	$id_livrable_ancien = sql_getfetsel('id_livrable','spip_tickets','id_ticket='.intval($id_ticket));
	$id_livrable = _request('id_livrable');
	if($id_assigne == $id_assigne_ancien){
		$erreurs['message_erreur'] = _T('livrables:erreur_meme_livrable');	
	}
	return $erreurs;
}


/**
 * 
 * Fonction de traitement du formulaire
 * 
 */
function formulaires_ticket_livrable_traiter($id_ticket=''){
	$res = array();
	$id_livrable = _request('id_livrable');
	sql_updateq('spip_tickets',array('id_livrable' => $id_livrable),'id_ticket='.intval($id_ticket));
	$res['message_ok'] = _T('preprod:succes_ticket_modifie');

	return $message;
}
?>