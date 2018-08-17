<?php
if (!defined("_ECRIRE_INC_VERSION"))
	return;

	// Définition des champs pour le détail du formulaire promotion du plugin promotions (https://github.com/abelass/promotions)
function promotions_reservation_multiple_simple_dist($flux = '') {
	return array(
		'nom' => _T('reservations_multiples:nom_reservation_multiple_simple'),
		'plugins_applicables' => 'reservation_evenement',
	);
}

// Définition de l'action de la promotion
function promotions_reservation_multiple_simple_action_dist($flux, $promotion = array()) {

	// Si on est en présence de la première réservation d'une réservation multiple
	if (_request('nr_auteur')) {
		$flux['data']['applicable'] = 'oui';
	}

	return $flux;
}
