<?php
/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2013
 *
 */
 
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
 * @param string $action 
 * 		L'action que l'on souhaite faire
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
		case 'assigneretre':
			$define = (defined('_TICKETS_AUTORISATION_ASSIGNERETRE')) ? _TICKETS_AUTORISATION_ASSIGNERETRE : ($utiliser_defaut ? '0minirezo':'');
			break;
		case 'commenter':
			$define = (defined('_TICKETS_AUTORISATION_COMMENTER')) ? _TICKETS_AUTORISATION_COMMENTER : ($utiliser_defaut ? '1comite':'');
			break;
		case 'modifier':
			$define = (defined('_TICKETS_AUTORISATION_MODIFIER')) ? _TICKETS_AUTORISATION_MODIFIER : ($utiliser_defaut ? '0minirezo':'');
			break;
		case 'epingler':
			$define = (defined('_TICKETS_AUTORISATION_EPINGLER')) ? _TICKETS_AUTORISATION_EPINGLER : ($utiliser_defaut ? '0minirezo':'');
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
 * @param string $faire : l'action à faire
 * @param string $type : le type d'objet sur lequel porte l'action
 * @param int $id : l'identifiant numérique de l'objet
 * @param array $qui : les éléments de session de l'utilisateur en cours
 * @param array $opt : les options
 * @return boolean true/false : true si autorisé, false sinon
 */
function autoriser_ticket_ecrire_dist($faire, $type, $id, $qui, $opt){
	if(($qui['webmestre'] == 'oui') && $qui['statut'] == '0minirezo')
		return true;
	
	$autorise = false;
	$utiliser_defaut = true;

	if(autoriser('modifier', $type, $id, $qui, $opt))
		return autoriser('modifier', $type, $id, $qui, $opt);
	
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	
	$type = lire_config('tickets/autorisations/ecrire_type');
	if($type){
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = ($qui['webmestre'] == 'oui');
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
		if($autorise == true)
			return $autorise;
		$utiliser_defaut = false;
	}

	// Si pas configuré ou pas autorisé dans la conf => on teste les define
	$liste = definir_autorisations_tickets('ecrire',$utiliser_defaut);
	if ($liste['statut'])
		$autorise = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$autorise = in_array($qui['id_auteur'], $liste['auteur']);

	return $autorise;
}


/**
 * Autorisation de créer des tickets
 * (défini qui peut créer un ticket)
 * 
 * @param string $faire : l'action à faire
 * @param string $type : le type d'objet sur lequel porte l'action
 * @param int $id : l'identifiant numérique de l'objet
 * @param array $qui : les éléments de session de l'utilisateur en cours
 * @param array $opt : les options
 * @return boolean true/false : true si autorisé, false sinon
 */
function autoriser_ticket_creer_dist($faire, $type, $id, $qui, $opt){
	return	autoriser('ecrire','ticket', $id, $qui, $opt);
}

/**
 * Autorisation d'assignation des tickets
 * (défini qui peu assigner les tickets)
 * 
 * - Les webmestres
 * @param string $faire : l'action à faire
 * @param string $type : le type d'objet sur lequel porte l'action
 * @param int $id : l'identifiant numérique de l'objet
 * @param array $qui : les éléments de session de l'utilisateur en cours
 * @param array $opt : les options
 * @return boolean true/false : true si autorisé, false sinon
 */
function autoriser_ticket_assigner_dist($faire, $type, $id, $qui, $opt){
	if(($qui['webmestre'] == 'oui') && $qui['statut'] == '0minirezo')
		return true;
	
	$autorise = false;
	$utiliser_defaut = true;

	if(!function_exists('lire_config'))
		include_spip('inc/config');
	
	if((lire_config('tickets/autorisations/assigner_modifieur') == 'on') && autoriser('modifier', $type, $id, $qui, $opt))
		return true;

	$type = lire_config('tickets/autorisations/assigner_type');
	if($type){
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = ($qui['webmestre'] == 'oui');
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('tickets/autorisations/assigner_statuts',array())))
					return true;
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
 * Autorisation d'être assigné à un ticket
 *
 * @param string $faire : l'action à faire
 * @param string $type : le type d'objet sur lequel porte l'action
 * @param int $id : l'identifiant numérique de l'objet
 * @param array $qui : les éléments de session de l'utilisateur en cours
 * @param array $opt : les options
 * @return boolean true/false : true si autorisé, false sinon
 */
function autoriser_ticket_assigneretre_dist($faire, $type, $id, $qui, $opt){
	
	$autorise = false;
	$utiliser_defaut = true;

	if(!function_exists('lire_config'))
		include_spip('inc/config');

	$type = lire_config('tickets/autorisations/assigneretre_type');
	if($type){
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = ($qui['webmestre'] == 'oui');
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('tickets/autorisations/assigneretre_statuts',array())))
					return true;
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('tickets/autorisations/assigneretre_statuts',array('0minirezo')));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('tickets/autorisations/assigneretre_auteurs',array()));
				break;
		}
		if($autorise == true)
			return $autorise;
		$utiliser_defaut = false;
	}

	$liste = definir_autorisations_tickets('assigneretre',$utiliser_defaut);
	if ($liste['statut'])
		$autorise = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$autorise = in_array($qui['id_auteur'], $liste['auteur']);

	return $autorise;
}

/**
 * Autorisation de modification des tickets
 * Défini qui peut modifier les tickets :
 * - Les webmestres
 * - L'auteur du ticket
 * - Les personnes assignées
 * - Les personnes correspondant à la configuration
 * 
 * @param string $faire : l'action à faire
 * @param string $type : le type d'objet sur lequel porte l'action
 * @param int $id : l'identifiant numérique de l'objet
 * @param array $qui : les éléments de session de l'utilisateur en cours
 * @param array $opt : les options
 * @return boolean true/false : true si autorisé, false sinon
 */ 
function autoriser_ticket_modifier_dist($faire, $type, $id, $qui, $opt){
	if(($qui['webmestre'] == 'oui') && $qui['statut'] == '0minirezo')
		return true;
	
	$autorise = false;
	$utiliser_defaut = true;

	if(is_numeric($id)){
		// Si l'auteur en question est l'auteur du ticket ou l'auteur assigné au ticket,
		// il peut modifier le ticket
		$id_assigne_auteur = sql_fetsel('id_assigne,id_auteur','spip_tickets','id_ticket='.intval($id));
		if(($id_assigne_auteur['id_auteur'] && ($id_assigne_auteur['id_auteur'] == $qui['id_auteur'])) || ($id_assigne_auteur['id_assigne'] && ($id_assigne_auteur['id_assigne'] == $qui['id_auteur'])))
			return true;
		
		if(!function_exists('lire_config'))
			include_spip('inc/config');

		$type = lire_config('tickets/autorisations/modifier_type', 'par_statut');
		if($type){
			switch($type) {
				case 'webmestre':
					// Webmestres uniquement
					$autorise = ($qui['webmestre'] == 'oui');
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
			$utiliser_defaut = false;
		}
	
		// Si $utiliser_defaut = true, on utilisera les valeurs par défaut
		// Sinon on ajoute la possibilité de régler par define
		$liste = definir_autorisations_tickets('modifier',$utiliser_defaut);
		if ($liste['statut'])
			$autorise = in_array($qui['statut'], $liste['statut']);
		else if ($liste['auteur'])
			$autorise = in_array($qui['id_auteur'], $liste['auteur']);
		if(!$autorise){
			$id_auteur = sql_getfetsel('id_auteur','spip_tickets','id_ticket='.intval($id));
			if($id_auteur && ($id_auteur== $qui['id_auteur']))
				$autorise = true;
		}
	}
	return $autorise;
}

/**
 * Autorisation à instituer des tickets
 * Défini qui peut changer le statut des tickets :
 * 
 * - les webmestres
 * 
 * @param string $faire : l'action à faire
 * @param string $type : le type d'objet sur lequel porte l'action
 * @param int $id : l'identifiant numérique de l'objet
 * @param array $qui : les éléments de session de l'utilisateur en cours
 * @param array $opt : les options
 * @return boolean true/false : true si autorisé, false sinon
 */ 
function autoriser_ticket_instituer_dist($faire, $type, $id, $qui, $opt){
	if(($qui['webmestre'] == 'oui') && $qui['statut'] == '0minirezo')
		return true;
	
	$autorise = false;
	$utiliser_defaut = true;

	if(is_numeric($id)){
		// Si l'auteur en question est l'auteur du ticket ou l'auteur assigné au ticket,
		// il peut modifier le ticket
		$id_assigne_auteur = sql_fetsel('statut,id_assigne,id_auteur','spip_tickets','id_ticket='.intval($id));
		
		if(($id_assigne_auteur['statut'] == 'redac' && $opt['statut'] == 'ouvert') 
			OR ($id_assigne_auteur['id_auteur'] && ($id_assigne_auteur['id_auteur'] == $qui['id_auteur']))
			OR ($id_assigne_auteur['id_assigne'] && ($id_assigne_auteur['id_assigne'] == $qui['id_auteur'])))
			return true;
		
		if(!function_exists('lire_config'))
			include_spip('inc/config');

		$type = lire_config('tickets/autorisations/modifier_type', 'par_statut');
		if($type){
			switch($type) {
				case 'webmestre':
					// Webmestres uniquement
					$autorise = ($qui['webmestre'] == 'oui');
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
			$utiliser_defaut = false;
		}
	
		// Si $utiliser_defaut = true, on utilisera les valeurs par défaut
		// Sinon on ajoute la possibilité de régler par define
		$liste = definir_autorisations_tickets('modifier',$utiliser_defaut);
		if ($liste['statut'])
			$autorise = in_array($qui['statut'], $liste['statut']);
		else if ($liste['auteur'])
			$autorise = in_array($qui['id_auteur'], $liste['auteur']);
		if(!$autorise){
			$id_auteur = sql_getfetsel('id_auteur','spip_tickets','id_ticket='.intval($id));
			if($id_auteur && ($id_auteur== $qui['id_auteur']))
				$autorise = true;
		}
	}
	return $autorise;
}

/**
 * Autorisation de notification des tickets
 * (défini qui doit être notifié)
 * 
 * @param string $faire : l'action à faire
 * @param string $type : le type d'objet sur lequel porte l'action
 * @param int $id : l'identifiant numérique de l'objet
 * @param array $qui : les éléments de session de l'utilisateur en cours
 * @param array $opt : les options
 * @return boolean true/false : true si autorisé, false sinon
 */
function autoriser_ticket_commenter_dist($faire, $type, $id, $qui, $opt){
	if(($qui['webmestre'] == 'oui') && $qui['statut'] == '0minirezo')
		return true;
	
	$autorise = false;
	$utiliser_defaut = true;

	if(!function_exists('lire_config'))
		include_spip('inc/config');
	
	if((lire_config('tickets/autorisations/assigner_modifieur') == 'on') && autoriser('modifier', $type, $id, $qui, $opt))
		return true;
	
	$type = lire_config('tickets/autorisations/commenter_type', 'par_statut');
	if($type){
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = ($qui['webmestre'] == 'oui');
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
 * Autorisation d'épingler des tickets
 * Défini qui peut épingler les tickets
 * Par défaut seulement les admins
 * 
 * @param string $faire : l'action à faire
 * @param string $type : le type d'objet sur lequel porte l'action
 * @param int $id : l'identifiant numérique de l'objet
 * @param array $qui : les éléments de session de l'utilisateur en cours
 * @param array $opt : les options
 * @return boolean true/false : true si autorisé, false sinon
 */ 
function autoriser_ticket_epingler_dist($faire, $type, $id, $qui, $opt){
	if(($qui['webmestre'] == 'oui') && $qui['statut'] == '0minirezo')
		return true;
	
	$autorise = false;
	$utiliser_defaut = true;

	if(!function_exists('lire_config'))
		include_spip('inc/config');

	$type = lire_config('tickets/autorisations/epingler_type', 'par_statut');
	if($type){
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = ($qui['webmestre'] == 'oui');
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('tickets/autorisations/epingler_statuts',array('0minirezo'))))
					return true;
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('tickets/autorisations/modifier_statuts',array('0minirezo')));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('tickets/autorisations/modifier_auteurs',array()));
				break;
		}
		if($autorise == true)
			return $autorise;
		$utiliser_defaut = false;
	}

	// Si $utiliser_defaut = true, on utilisera les valeurs par défaut
	// Sinon on ajoute la possibilité de régler par define
	$liste = definir_autorisations_tickets('epingler',$utiliser_defaut);
	if ($liste['statut'])
		$autorise = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$autorise = in_array($qui['id_auteur'], $liste['auteur']);
	return $autorise;
}

/**
 * La liste des tickets est accessible à tout le monde
 * 
 * @param string $faire
 * 		L'action à faire
 * @param string $type
 * 		Le type d'objet sur lequel porte l'action
 * @param int $id
 *		L'identifiant numérique de l'objet
 * @param array $qui
 * 		Les éléments de session de l'utilisateur en cours
 * @param array $opt
 * 		Les options
 * @return boolean true/false
 * 		true si autorisé, false sinon
 */
function autoriser_tickets_menu_dist($faire, $type, $id, $qui, $opt) {
	return true;
}

/**
 * Le bouton de création rapide de ticket est visible pour ceux pouvant créer un ticket
 * 
 * @param string $faire
 * 		L'action à faire
 * @param string $type
 * 		Le type d'objet sur lequel porte l'action
 * @param int $id
 *		L'identifiant numérique de l'objet
 * @param array $qui
 * 		Les éléments de session de l'utilisateur en cours
 * @param array $opt
 * 		Les options
 * @return boolean true/false
 * 		true si autorisé, false sinon
 */
function autoriser_ticketedit_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('ecrire','ticket');
}

?>