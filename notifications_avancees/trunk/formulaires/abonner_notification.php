<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_abonner_notification_charger($id_notifications_abonnement, $redirect='', $quoi='', $id=0, $option=array()){
	$contexte = array();
	$id_notifications_abonnement = intval($id_notifications_abonnement);
	
	// S'il n'y a ni identifiant d'abonnement correct, ni les infos requises pour un nouvel abonnement, kaput !
	if (
		(!$id_notifications_abonnement
			or !($abonnement = sql_fetsel('*', 'spip_notifications_abonnements', 'id_notifications_abonnement = '.$id_notifications_abonnement))
		)
		and !($quoi and $id)
	)
		return false;
	
	include_spip('notifavancees_pipelines');
	
	// Si c'est un abonnement déjà là, on récupère les infos dont on a besoin
	if ($abonnement){
		$quoi = $abonnement['quoi'];
		$id = $abonnement['id'];
	}
	
	// On va chercher les paramètres de ce type de notification
	$contexte['_infos_notification'] = notifications_charger_infos($quoi);
	
	return $contexte;
}

function formulaires_abonner_notification_verifier($id_notifications_abonnement, $redirect='', $quoi='', $id=0, $option=array()){
	$erreurs = array();
	
	return $erreurs;
}

function formulaires_abonner_notification_traiter($id_notifications_abonnement, $redirect='', $quoi='', $id=0, $option=array()){
	$retours = array();
	
	return $retours;
}

?>
