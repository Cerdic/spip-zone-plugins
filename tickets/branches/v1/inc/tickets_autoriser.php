<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @return
 */
function tickets_autoriser(){}

/**
 * Renvoie la liste des auteurs ou des statuts autorises pour une action donnee
 * 
 * @param string $action L'action que l'on souhaite faire
 * @param boolean $utiliser_defaut [optional]
 * @return 
 */ 
function definir_autorisations_tickets($action,$utiliser_defaut=true){
	$aut = null;

	switch(strtolower($action)){
		case 'ecrire':
			$define = (defined('_TICKETS_AUTORISATION_ECRIRE')) ? _TICKETS_AUTORISATION_ECRIRE : ($utiliser_defaut ? '0minirezo':'');
			break;
		case 'notifier':
			$define = (defined('_TICKETS_AUTORISATION_NOTIFIER')) ? _TICKETS_AUTORISATION_NOTIFIER : ($utiliser_defaut ? '0minirezo':'');
			break;
		case 'assigner':
			$define = (defined('_TICKETS_AUTORISATION_ASSIGNER')) ? _TICKETS_AUTORISATION_ASSIGNER : ($utiliser_defaut ? '0minirezo':'');
			break;
		case 'commenter':
			$define = (defined('_TICKETS_AUTORISATION_COMMENTER')) ? _TICKETS_AUTORISATION_COMMENTER : ($utiliser_defaut ? '1comite':'');
			break;
		case 'modifier':
			$define = (defined('_TICKETS_AUTORISATION_MODIFIER')) ? _TICKETS_AUTORISATION_MODIFIER : ($utiliser_defaut ? '0minirezo':'');
			break;
		default:
			$define = $utiliser_defaut ? '0minirezo' : '';
			break;
	}

	if($define){
		$liste = explode(':', $define);
		if (in_array('webmestre', $liste))
			$aut['auteur'] = explode(':', _ID_WEBMESTRES);
		else if (in_array('0minirezo', $liste))
			$aut['statut'] = array('0minirezo');
		else if (in_array('1comite', $liste))
			$aut['statut'] = array('0minirezo', '1comite');
		else
			$aut['auteur'] = $liste;
	}

	return $aut;
}

/**
 * Autorisation d'écrire des tickets
 * (défini qui peut créer un ticket)
 * 
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return 
 */
function autoriser_ticket_ecrire_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;
	$utiliser_defaut = true;

	if(autoriser_ticket_modifier_dist($faire, $type, $id, $qui, $opt)){
		return autoriser_ticket_modifier_dist($faire, $type, $id, $qui, $opt);
	}
	// Utilisation du CFG si possible
	if(function_exists('lire_config')){
		$type = lire_config('tickets/autorisations/ecrire_type', 'par_statut');
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = tickets_verifier_webmestre($qui);
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('tickets/autorisations/ecrire_statuts',array('0minirezo')))){
					return true;
				}
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('tickets/autorisations/ecrire_statuts',array()));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('tickets/autorisations/ecrire_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
	}

	if($type){
		$utiliser_defaut = false;
	}

	// Si pas de CFG ou pas autorise dans le cfg => on teste les define
	$liste = definir_autorisations_tickets('ecrire',$utiliser_defaut);
	if ($liste['statut'])
		$autorise = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$autorise = in_array($qui['id_auteur'], $liste['auteur']);

	return $autorise;
}

/**
 * Autorisation d'assignation des tickets
 * (défini qui peu assigner les tickets)
 * 
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return 
 */
function autoriser_ticket_assigner_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;
	$utiliser_defaut = true;

	if(autoriser_ticket_modifier_dist($faire, $type, $id, $qui, $opt)){
		return autoriser_ticket_modifier_dist($faire, $type, $id, $qui, $opt);
	}
	// Utilisation du CFG si possible
	if(function_exists('lire_config')){
		$type = lire_config('tickets/autorisations/assigner_type', 'par_statut');
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = tickets_verifier_webmestre($qui);
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('tickets/autorisations/assigner_statuts',array()))){
					return true;
				}
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('tickets/autorisations/assigner_statuts',array('0minirezo')));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('tickets/autorisations/assigner_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
	}

	if($type){
		$utiliser_defaut = false;
	}

	$liste = definir_autorisations_tickets('assigner',$utiliser_defaut);
	if ($liste['statut'])
		$autorise = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$autorise = in_array($qui['id_auteur'], $liste['auteur']);

	return $autorise;
}

/**
 * Autorisation de notification des tickets
 * (défini qui doit être notifié)
 * 
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return 
 */
function autoriser_ticket_commenter_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;
	$utiliser_defaut = true;

	if(autoriser_ticket_modifier_dist($faire, $type, $id, $qui, $opt)){
		return autoriser_ticket_modifier_dist($faire, $type, $id, $qui, $opt);
	}
	// Utilisation du CFG si possible
	if(function_exists('lire_config')){
		$type = lire_config('tickets/autorisations/commenter_type', 'par_statut');
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = tickets_verifier_webmestre($qui);
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('tickets/autorisations/commenter_statuts',array()))){
					return true;
				}
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('tickets/autorisations/commenter_statuts',array('0minirezo','1comite')));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('tickets/autorisations/commenter_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
	}

	if($type){
		$utiliser_defaut = false;
	}

	$liste = definir_autorisations_tickets('commenter',$utiliser_defaut);
	if ($liste['statut'])
		$autorise = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$autorise = in_array($qui['id_auteur'], $liste['auteur']);

	return $autorise;
}

/**
 * Autorisation de modification des tickets
 * Défini qui peut modifier les tickets :
 * - Les personnes assignées
 * - Les personnes correspondant à la configuration
 * 
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return 
 */ 
function autoriser_ticket_modifier_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;
	$utiliser_defaut = true;

	// Si l'auteur en question est l'auteur assigné au ticket,
	// il peut modifier le ticket
	if(intval($id)){
		$id_assigne = sql_getfetsel('id_assigne','spip_tickets','id_ticket='.intval($id));
		if($id_assigne && ($id_assigne == $qui['id_auteur'])){
			return true;
		}
	}
	// Utilisation du CFG si possible
	if(function_exists('lire_config')){
		$type = lire_config('tickets/autorisations/modifier_type', 'par_statut');
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = tickets_verifier_webmestre($qui);
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('tickets/autorisations/modifier_statuts',array()))){
					return true;
				}
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('tickets/autorisations/modifier_statuts',array('0minirezo')));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('tickets/autorisations/modifier_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
	}

	// Si pas de configuration CFG, on utilise des valeurs par défaut
	if($type){
		$utiliser_defaut = false;
	}

	// Si $utiliser_defaut = true, on utilisera les valeurs par défaut
	// Sinon on ajoute la possibilité de régler par define
	$liste = definir_autorisations_tickets('modifier',$utiliser_defaut);
	if ($liste['statut'])
		$autorise = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$autorise = in_array($qui['id_auteur'], $liste['auteur']);
	return $autorise;
}

function tickets_verifier_webmestre($qui){
	$webmestre =  false;
	$webmestre = in_array($qui['id_auteur'],explode(':', _ID_WEBMESTRES));
	if(!$webmestre && ($qui['webmestre']=='oui')){
		$webmestre =  true;
	}
	return $webmestre;
}
?>
