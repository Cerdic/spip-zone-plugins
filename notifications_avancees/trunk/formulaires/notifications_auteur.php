<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('notifavancees_pipelines');

function formulaires_notifications_auteur_charger($id_auteur){
	$contexte = array();
	
	// Attention à l'id_auteur
	if (!($id_auteur = intval($id_auteur) and $id_auteur > 0))
		return false;
	
	$contexte['_id_auteur'] = $id_auteur;
	
	// On va chercher tous les abonnements de cet auteur et on pré-transforme
	/*$notifications = sql_allfetsel(
		'*',
		'spip_notifications_abonnements',
		'id_auteur = '.$id_auteur
	);*/
	
	// On va chercher les informations sur toutes les notifications disponibles
	$contexte['_notifications_disponibles'] = notifications_lister_disponibles();
	
	// On va chercher les informations sur tous les modes d'envoi disponibles
	$contexte['_modes_disponibles'] = notifications_modes_lister_disponibles();
	
	return $contexte;
}

function formulaires_notifications_auteur_verifier($id_auteur){
	$erreurs = array();
	
	return $erreurs;
}

function formulaires_notifications_auteur_traiter($id_auteur){
	$retours = array();
	
	return $retours;
}

?>
