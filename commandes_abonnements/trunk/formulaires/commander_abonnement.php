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
	$offre = sql_fetsel('*', 'spip_abonnements_offres', 'id_abonnements_offre ='.$id_abonnements_offre);
	$renouvellement_auto = $offre['renouvellement_auto'];

	// 1) Soit il y a déjà une commande d'abonnement en cours,
	// on la met à jour avec l'offre sélectionnée
	if (
		$id_auteur = session_get('id_auteur')
		and $detail = sql_fetsel(
			'd.*',
			'spip_commandes_details AS d INNER JOIN spip_commandes AS c ON c.id_commande=d.id_commande',
			array(
				'd.objet=' . sql_quote('abonnements_offre'),
				'c.statut=' . sql_quote('encours'),
				'c.id_auteur=' . intval($id_auteur),
			)
		)
	) {
		$montant_ht = $montant;
		$echeances = array();
		$periodicite = '';

		// Si on trouve une taxe, on regénère un montant HT
		// (car comme le montant peut être personnalisé, ce qu'on a c'est toujours le TTC)
		if ($taxe = floatval($offre['taxe'])) {
			$montant_ht = round($montant * (1 / (1 + $taxe)), 2);
		}

		// Échéances avec les deux seuls cas qu'on sait gérer pour l'instant
		if ($renouvellement_auto) {
			if ($offre['periode'] == 'mois' and $offre['duree'] == 1) {
				$periodicite = 'mois';
			} elseif ($offre['periode'] == 'mois' and $offre['duree'] == 12) {
				$periodicite = 'annee';
			}
			$echeances = array(
				array('montant_ht' => $montant_ht, 'montant' => $montant),
			);
		}

		$set_detail = array(
			'descriptif'       => $offre['titre'],
			'id_objet'         => $id_abonnements_offre,
			'prix_unitaire_ht' => $montant_ht,
			'taxe'             => $taxe,
		);
		$set_commande = array(
			'echeances_type' => $periodicite,
			'echeances'      => $echeances,
		);
		sql_updateq('spip_commandes', $set_commande, 'id_commande='.intval($detail['id_commande']));
		sql_updateq('spip_commandes_details', $set_detail, 'id_commandes_detail='.intval($detail['id_commandes_detail']));

		// Puis juste au cas-où, on supprime la session éventuelle
		session_set('commande_abonnement', null);

	// 2) Soit pas de commande, et on enregistre en session les infos nécessaires,
	// elle sera créée quand on aura un utilisateur sous la main
	// et qu'on sera sûr d'avoir ses infos à jour
	} else {
		$commande_abonnement = array(
			'id_abonnements_offre' => $id_abonnements_offre,
			'montant' => $montant,
			'renouvellement_auto' => $renouvellement_auto,
		);
		session_set('commande_abonnement', $commande_abonnement);
	}
	return $retours;
}
