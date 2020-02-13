<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');
include_spip('inc/session');

function formulaires_commander_abonnement_charger_dist($retour = ''){
	$contexte = array(
		'id_abonnements_offre' => 0,
		'montants_persos' => array(),
	);
	
	return $contexte;
}

function formulaires_commander_abonnement_verifier_dist($retour = ''){
	$erreurs = array();
	$id_abonnements_offre = intval(_request('id_abonnements_offre'));
	$montants_persos = _request('montants_persos');
	
	// Trouver le prix par défaut de l'offre choisie
	$trouver_prix = charger_fonction('prix', 'inc');
	$prix_defaut = $trouver_prix('abonnements_offre', $id_abonnements_offre);
	
	// Si déjà on a pas trouvé de prix par défaut
	if (!$prix_defaut) {
		$erreurs['message_erreur'] = 'Impossible de trouver le prix de l’offre demandée.';
	}
	
	// S'il y a une demande de montant perso
	if (is_array($montants_persos) and isset($montants_persos[$id_abonnements_offre])) {
		if ($montants_persos[$id_abonnements_offre]['libre'] !== '') {
			$montant_perso = $montants_persos[$id_abonnements_offre]['libre'];
		}
		elseif (isset($montants_persos[$id_abonnements_offre]['predef'])) {
			$montant_perso = $montants_persos[$id_abonnements_offre]['predef'];
		}
		// On va chercher le montant minimum
		$montant_minimum = sql_getfetsel('montant_minimum', 'spip_abonnements_offres', 'id_abonnements_offre = '.$id_abonnements_offre);
		// On ne prend que le tout premier
		$montant_minimum = array_map('trim', explode('|', $montant_minimum));
		$montant_minimum = floatval($montant_minimum[0]);
		// Si le montant minimum est plus petit que le prix de base
		if ($montant_minimum < $prix_defaut) {
			$montant_minimum = $prix_defaut;
		}
		
		if (!is_numeric($montant_perso)) {
			$erreurs['montant_'.$id_abonnements_offre] = 'Le montant doit être un nombre.';
		}
		elseif ($montant_perso != $prix_defaut and $montant_perso < $montant_minimum) {
			$erreurs['montant_'.$id_abonnements_offre] = "Le montant personnalisé doit être d’au moins ${montant_minimum}€";
		}
	}
	
	// S'il n'y a aucune erreur, on évite de refaire des tests et on garde le bon montant
	if (!$erreurs) {
		set_request('montant', $montant_perso ? $montant_perso : $prix_defaut);
	}
	
	return $erreurs;
}

function formulaires_commander_abonnement_traiter_dist($retour = '') {
	include_spip('inc/session');
	$retours = array(
		'redirect' => $retour,
	);
	$id_abonnements_offre = _request('id_abonnements_offre');
	$montant = _request('montant');
	$renouvellement_auto = sql_getfetsel('renouvellement_auto', 'spip_abonnements_offres', 'id_abonnements_offre = '.$id_abonnements_offre);
	
	// On va enregistrer en session les infos nécessaires à la commande
	// elle sera créée quand on aura un utilisateur sous la main et qu'on sera sûr d'avoir ses infos à jour
	$commande_abonnement = array(
		'id_abonnements_offre' => $id_abonnements_offre,
		'montant' => $montant,
		'renouvellement_auto' => $renouvellement_auto,
	);
	
	session_set('commande_abonnement', $commande_abonnement);
	
	return $retours;
}
