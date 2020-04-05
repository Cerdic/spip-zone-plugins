<?php
/**
 * Utilisations de pipelines par Objets restrictions périodes
 *
 * @plugin     Objets restrictions périodes
 * @copyright  2019
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Objets_restrictions_periodes\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Compléte le tableau d’erreurs de type dates envoyé par le fomulaire d'éditions d'objets locations.
 *
 * @pipeline objets_location_verifier_dates
 * @param array $flux
 *  Données du pipeline
 * @return array
 *   Données du pipeline
 */
function objets_restrictions_periodes_objets_location_verifier_dates($flux){
	include_spip('inc/locations_objets_restrictions');
	$flux['data'] = lor_verifier($flux['data'], 'dates');
	return $flux;
}

/**
 * Permet de compléter ou modifier le résultat de la compilation d’un squelette donné.
 *
 * @pipeline recuperer_fond
 * @param array $flux
 *  Données du pipeline
 * @return array
 *   Données du pipeline
 */
function objets_restrictions_periodes_recuperer_fond($flux){
	if ($flux['args']['fond'] == 'formulaires/inc-editer_objets_location_dates'){

		// On ajoute un ajax pour la date_fin et recharge avec les erreurs.
		$flux['data']['texte'] .= recuperer_fond('formulaires/inc-editer_objets_location_dates_script');

		if ($flux['args']['contexte']['recharge_ajax']) {
			include_spip('inc/objets_location');
			include_spip('public/assembler');

			// On récupère les données du contexte.
			$contexte = calculer_contexte();

			// On vérifie les erreurs de restrictions.
			$contexte['erreurs'] = objets_location_verifier_dates($erreurs);

			// On évite le loop infinie.
			unset($contexte['recharge_ajax']);

			$flux['data']['texte'] = recuperer_fond(
				'formulaires/inc-editer_objets_location_dates',
				$contexte,
				['ajax' => 'objets_location_dates']);
		}
	}
	return $flux;
}
