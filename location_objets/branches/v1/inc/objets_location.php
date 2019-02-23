<?php
/**
 * Fonctions spécifiquesdes objets locations
 *
 * @plugin     Location d&#039;objets
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets\Fonctions
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Calcule le prix et infos relationnées d'un objet
 *
 * @param array $set
 *        	Les valeurs à enregistrer.
 * @param array $contexte
 * 	        Les variables de l'environnement.
 * @return array
 * 	        Les valeurs à enregistrer.
 */
function location_prix_objet($set, $contexte) {

	// Crée une variable pour chaque élément du contexte.
	foreach ($contexte as $cle => $valeur) {
		$$cle = $valeur;
	}

	if (test_plugin_actif('prix_objets')) {
		$prix_objet = TRUE;
		if ($prix_unitaire_ht = prix_par_objet(
					$location_objet,
					$id_location_objet,
					array(
						'date_debut' => $date_debut,
						'date_fin' => $date_fin
					),
					'prix_ht',
					array(
						'mode' => $mode_calcul_prix,
					)
				)) {
			$set['prix_unitaire_ht'] = $prix_unitaire_ht;
			$prix_ttc = prix_par_objet(
				$location_objet,
				$id_location_objet,
				array(
					'date_debut' => $date_debut,
					'date_fin' => $date_fin
				),
				'prix',
				array(
					'mode' => $mode_calcul_prix,
				)
			);
			$set['taxe'] = $prix_ttc - $prix_unitaire_ht;
			$set['devise'] = devise_defaut_objet($id_location_objet, $location_objet);
			$set['prix_total'] = _request('prix_total');
		}
	}

	$set = pipeline('location_prix_objet', array(
		'data' => $set,
		'args' => $contexte
	));

	return $set;
}

/**
 * Retourne les valeurs disponibles pour le champ entite_duree
 *
 * @return array
 *   les valeurs.
 */
function entite_duree_definitions() {
	return [
		'jour' => _T('ecrire:jours'),
		'nuit' => _T('dates_outils:nuits'),
	];
}

function objets_location_verifier() {
	$erreurs = [];

	$type_verification = ['general', 'dates'];

	foreach ($type_verification AS $type) {
		$verifier = 'objets_location_verifier_' . $type;
		$erreurs = $verifier($erreurs);
	}

	return $erreurs;
}

function objets_location_verifier_general($erreurs) {

	$erreurs += formulaires_editer_objet_verifier(
		'objets_location',
		$id_objets_location,
		array(
			'id_auteur',
			'location_objet',
			'id_location_objet',
			'date_debut',
			'date_fin',
		)
	);

	$erreurs = pipeline('objets_location_verifier_general', [
			'args' => ['type' => 'general'],
			'data' => $erreurs
		]);

return $erreurs;
}

function objets_location_verifier_dates($erreurs) {
	$champs_dates = ['date_debut', 'date_fin'];

	// Vérifier si on a une date correcte.
	$verifier = charger_fonction('verifier', 'inc');
	foreach ($champs_dates  AS $champ) {
		$normaliser = null;

		if ($erreur = $verifier(
			_request($champ),
			'date',
			array('normaliser'=>'datetime'),
			$normaliser)) {
			$erreurs[$champ] = $erreur;
			// si une valeur de normalisation a ete transmis, la prendre.
		}
		elseif (!is_null($normaliser)) {
			set_request($champ, $normaliser);
			// si pas de normalisation ET pas de date soumise, il ne faut pas tenter d'enregistrer ''
		}
		else {
			set_request($champ, null);
		}
	}

	if (($date_debut = _request('date_debut') AND $date_fin = _request('date_fin')) AND
		strtotime($date_debut) > strtotime($date_fin)) {
		$erreurs['date_fin'] = _T('objets_location:erreur_date_fin_anterieur_date_debut');
	}
	elseif ($erreur = $verifier(
				array(
					'date_debut' => $date_debut,
					'date_fin' => $date_fin
				),
				'dates_diponibles',
				array(
					'objet' => objet_type(_request('location_objet')),
					'id_objet' => _request('id_location_objet'),
					'debut' => 0,
					'fin' => 0,
					'utilisation_squelette' => 'disponibilites/utilisees_objet_location',
					'utilisation_id_exclu' => _request('id_objets_location'),
					'format' => $format,
				)
			)
		) {
		$erreurs['date_fin'] = $erreur;
	}

	$erreurs = pipeline('objets_location_verifier_dates', [
			'args' => ['type' => 'dates'],
			'data' => $erreurs
		]);

return $erreurs;
}


