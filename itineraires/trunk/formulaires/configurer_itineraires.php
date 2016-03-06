<?php
/**
 * Gestion du formulaire de configuration des itinéraires
 *
 * @plugin     Itinéraires
 * @copyright  2013
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Itineraires\Configuration
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Saisies du formulaire de configuration des itinéraires
 *
 * @return array
 *     Environnement du formulaire
 */
function formulaires_configurer_itineraires_saisies_dist(){
	include_spip('inc/config');
	
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'difficulte_max',
				'label' => _T('itineraires:configurer_difficulte_max_label'),
				'defaut' => lire_config('itineraires/difficulte_max', 5),
			),
			'verifier' => array(
				'type' => 'entier',
				'options' => array(
					'min' => 2,
				),
			),
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'activer_etapes',
				'label' => _T('itineraires:configurer_activer_etapes_label'),
				'label_case' => _T('itineraires:configurer_activer_etapes_label_case'),
				'defaut' => lire_config('itineraires/activer_etapes', ''),
			),
		),
	);
	
	return $saisies;
}
