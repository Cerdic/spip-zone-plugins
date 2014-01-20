<?php
/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @return
 */
function projets_autoriser(){}

/**
 * Autorisation de creation d'un projet
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_projets_creer_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;
	$type = lire_config('projets/autorisations/creer_type', 'webmestre');
	switch($type) {
		case 'webmestre':
			// Webmestres uniquement
			$autorise = in_array($qui['id_auteur'], explode(':', _ID_WEBMESTRES));
			break;
		case 'par_statut':
			// Autorisation par statut
			$autorise = in_array($qui['statut'], lire_config('projets/autorisations/creer_statuts',array()));
			break;
		case 'par_auteur':
			// Autorisation par id d'auteurs
			$autorise = in_array($qui['id_auteur'], lire_config('projets/autorisations/creer_auteurs',array()));
			break;
	}
	return $autorise;
}

/**
 * Autorisation de modification d'un projet
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_projets_modifier_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;

	if(autoriser_projets_creer_dist($faire, $type, $id, $qui, $opt)){
		return autoriser_projets_creer_dist($faire, $type, $id, $qui, $opt);
	}
	else{
		$type = lire_config('projets/autorisations/modifier_type', 'webmestre');
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = in_array($qui['id_auteur'], explode(':', _ID_WEBMESTRES));
				break;
			case 'par_statut':
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('projets/autorisations/modifier_statuts',array()));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('projets/autorisations/modifier_auteurs',array()));
				break;
		}
	}

	return $autorise;
}

/**
 * Autorisation de notification d'evenements d'un projet
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_projets_voir_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;

	// Eviter toute erreur de configuration

	// Si on peut creer, on peut voir
	if(autoriser_projets_creer_dist($faire, $type, $id, $qui, $opt)){
		return autoriser_projets_creer_dist($faire, $type, $id, $qui, $opt);
	}
	// Si on peut modifier, on peut voir également
	else if(autoriser_projets_modifier_dist($faire, $type, $id, $qui, $opt)){
		return autoriser_projets_creer_dist($faire, $type, $id, $qui, $opt);
	}
	else{
		$type = lire_config('projets/autorisations/creer_type', 'webmestre');
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = in_array($qui['id_auteur'], explode(':', _ID_WEBMESTRES));
				break;
			case 'par_statut':
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('projets/autorisations/voir_statuts',array()));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('projets/autorisations/voir_auteurs',array()));
				break;
		}
	}
	return $autorise;
}

/**
 * Autorisation de notification d'evenements d'un projet
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_projets_notifier_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;

	// On ne pourra de toute manière être notifié que losque l'on pourra voir le projet
	if(autoriser_projets_voir_dist($faire, $type, $id, $qui, $opt)){

	}


	return $autorise;
}
?>
