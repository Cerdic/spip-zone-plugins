<?php

/**
 * Gestion du formulaire de configuration des dictionnaires
 * 
 * @package SPIP\Dictionnaires\Formulaires
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Liste des saisies du formulaire de configuration de dictionnaires
 *
 * @return array
 *     Liste des saisies du formulaire
**/
function formulaires_configurer_dictionnaires_saisies_dist(){
	include_spip('inc/config');
	
	$saisies = array(
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'remplacer_premier_defaut',
				'label_case' => _T('dictionnaire:configurer_champ_remplacer_premier_defaut_label'),
			),
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'remplacer_premier_abbr',
				'label_case' => _T('dictionnaire:configurer_champ_remplacer_premier_abbr_label'),
			),
		),
	);
	
	return $saisies;
}

/**
 * Chargement du formulaire de configuration de dictionnaires
 *
 * @return array
 *     Environnement du formulaire
**/
function formulaires_configurer_dictionnaires_charger_dist(){
	return array(
		'remplacer_premier_defaut' => lire_config('dictionnaires/remplacer_premier_defaut'),
		'remplacer_premier_abbr' => lire_config('dictionnaires/remplacer_premier_abbr'),
	);
}

?>
