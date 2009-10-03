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

define('LONGUEUR_MINI_COMMENTAIRES_TICKETS', 20);

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
	if(strlen($texte) < LONGUEUR_MINI_COMMENTAIRES_TICKETS){
		$erreurs['texte'] = _T('tickets:erreur_texte_longueur_mini',array('nb'=> LONGUEUR_MINI_COMMENTAIRES_TICKETS));
	}
	if(_request("nobot")){
		$erreurs['nobot'] = true;
	}
	if(count($erreurs)>0){
		$erreurs['message_erreur'] = _T('tickets:erreur_verifier_formulaire');
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
		include_spip('inc/headers');
		$message['message_ok'] = _T('tickets:message_publie');
		$message['redirect'] = self();
	}
		
	return $message;
}
?>
