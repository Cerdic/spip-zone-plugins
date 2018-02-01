<?php
/**
 * Script de définition.
 *
 * @plugin     Réservation Événements
 * @copyright  2013 - 2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Pipelines
 */


/**
 * Définit les devises disponibles
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
if (!defined('_ECRIRE_INC_VERSION'))
	return;


function inc_reservations_devises_dist() {

	if (test_plugin_actif('prix_objets')) {
		include_spip('inc/config');
		$config=lire_config('prix_objets/devises',array('EUR'));
		$devises = array();
		foreach ($config AS $devise) {
			$devises[$devise] = traduire_devise($devise);
		}
	}
	else {
		$devise = array('EUR' => '€');
	}

	return $devises;
}
