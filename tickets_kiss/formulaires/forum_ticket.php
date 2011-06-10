<?php

/**
 * Plugin Tickets pour Spip 2.0
 * Licence GPL (c) 2008-2009
 * 
 * Formulaire de forum d'un ticket
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');

/**
 * Fonction de chargement des valeurs
 */
function formulaires_forum_ticket_charger($id_ticket=''){
	
	if(is_numeric($id_ticket)){
		if (!autoriser('commenter', 'ticket', $id_ticket)) {
			$editable = false;
		}else{
			$editable = true;
			if(_request('texte')){
				$valeurs['texte'] = _request('texte');
			}
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
 * @return array
 * @param int $id_ticket[optional]
 */
function formulaires_forum_ticket_verifier($id_ticket=''){
	$texte = _request("texte");
	if(strlen($texte)<20){
		$erreurs['texte'] = _T('ticketskiss:erreur_texte_longueur_mini',array('nb'=> 20));
	}
	if(_request(nobot_forum)){
		$erreurs['nobot'] = true;
	}
	if(count($erreurs)>0){
		$erreurs['message_erreur'] = _T('ticketskiss:erreur_verifier_formulaire');
	}
	return $erreurs;
}

/**
 * 
 * Fonction de traitement du formulaire
 * 
 * @return 
 * @param int $id_ticket[optional]
 */
function formulaires_forum_ticket_traiter($id_ticket=''){
	global $visiteur_session;
	
	$texte = _request("texte");
		
	$id_forum_ticket = sql_insertq("spip_tickets_forum", 
		array("id_ticket" => $id_ticket, "texte" => $texte, "id_auteur" => $visiteur_session['id_auteur'],  "date" => "NOW()"));

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_ticket/$id_ticket'");
	
	if($id_forum_ticket > 0){
		// Notifications
			if ($notifications = charger_fonction('notifications', 'inc')) {
				$notifications('commenterticket', $id_ticket,
					array('id_ticket_forum' => $id_forum_ticket, 'id_auteur' => $visiteur_session['id_auteur'])
				);
			}
		include_spip('inc/headers');
		$message['message_ok'] = _T('ticketskiss:message_publie');
		$message['redirect'] = ancre_url (parametre_url(self(),'id_forum',$id_forum_ticket,'&'),'tf'.$id_forum_ticket);
	}
		
	return $message;
}
?>
