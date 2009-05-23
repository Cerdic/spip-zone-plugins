<?php

/**
 * Plugin Tickets pour Spip 2.0
 * Licence GPL (c) 2008-2009
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
		if (!autoriser('ecrire', 'ticket', $id_ticket)) {
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
		include_spip('inc/headers');
		$retour = parametre_url($retour,'id_ticket',$id);
		$message .= redirige_formulaire($retour);
	}
	/**
	 * 
	// Envoyer mail annoncant le bug
	if (($statut != $ancien_statut) AND ($statut != "redac")) {
		include_spip('inc/tickets_filtres');
		$nom_site = $GLOBALS["meta"]["nom_site"];
		$url_site = $GLOBALS["meta"]["adresse_site"];
		$url_ticket = "$url_site/ecrire/?exec=ticket_afficher&id_ticket=$id_ticket";
		$email_webmestre = $GLOBALS["meta"]["email_webmaster"];
		$titre = trim($titre);
		$titre_message = "[Ticket - $nom_site] $titre - statut:".tickets_texte_statut($statut); 
		$header = "From: ". $nom_site . " <" . $email_webmestre . ">\r\n";
		$message = "$titre_message\n
		------------------------------------------\n
		Ceci est un message automatique : n'y repondez pas.\n\n
		$texte\n\n
		$url_ticket";
		
		// Determiner la liste des auteurs a notifier
		include_spip('inc/tickets_autoriser');
		$select = array('email');
		$from = array('spip_auteurs AS t1');
		$autorises = definir_autorisations_tickets('notifier');
		if ($autorises['statut']) 
			$where = array(sql_in('t1.statut', $autorises['statut']), 't1.email LIKE '.sql_quote('%@%'));
		else
			$where = array(sql_in('t1.id_auteur', $autorises['auteur']), 't1.email LIKE '.sql_quote('%@%'));
		$query_auteurs = sql_select($select, $from, $where);
		// Envoi des mails
		while ($row_auteur = sql_fetch($query_auteurs)) {
			$recipient = $row_auteur["email"];
			mail($recipient, $titre_message, $message, $header);
		}
	}
	*/
	return $message;
}
?>