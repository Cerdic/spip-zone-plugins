<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_statistiques_campagnes_charger_dist($type, $id){
	if (!in_array($type, array('campagne', 'annonceur'))){
		return false;
	}
	
	$contexte = array(
		'_type' => $type,
		'_id' => $id,
		'date_debut' => _request('date_debut',''),
		'date_fin' => _request('date_fin','')
	);
	
	return $contexte;
}

function formulaires_statistiques_campagnes_verifier_dist($type, $id){
	include_spip('inc/campagnes');
	$erreurs = array();
	
	// S'il y a des dates, on vérifie le format et l'ordre
	$date_debut = campagnes_verifier_date_saisie('debut', $erreurs);
	$date_fin = campagnes_verifier_date_saisie('fin', $erreurs);
	if ($date_debut and $date_fin and $date_fin < $date_debut){
		$erreurs['message_erreur'] = _T('campagne:erreur_date_avant_apres');
	}
	// Soit aucune date soit les deux
	if (($date_debut and !$date_fin) or (!$date_debut and $date_fin)){
		$erreurs['message_erreur'] = _T('campagne:erreur_date_deux');
	}
	
	return $erreurs;
}

function formulaires_statistiques_campagnes_traiter_dist($type, $id){
	include_spip('inc/campagnes');
	$retours = array('editable' => true);
	
	// On met les dates au format SQL ou on supprime si pas les deux
	$erreurs = array();
	$date_debut = campagnes_verifier_date_saisie('debut', $erreurs);
	$date_fin = campagnes_verifier_date_saisie('fin', $erreurs);
	if ($date_debut and $date_fin){
		$date_debut = date('Y-m-d', $date_debut);
		set_request('date_debut', $date_debut);
		$date_fin = date('Y-m-d', $date_fin);
		set_request('date_fin', $date_fin);
	}
	else{
		set_request('date_debut', '');
		set_request('date_fin', '');
	}
	
	return $retours;
}

?>
