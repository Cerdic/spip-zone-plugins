<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');

function formulaires_configurer_prestashop_api_saisies_dist(){
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'url',
				'label' => _T('prestashop_api:configurer_url_label'),
				'defaut' => lire_config('prestashop_api/url'),
				'obligatoire' => 'oui',
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'cle',
				'label' => _T('prestashop_api:configurer_cle_label'),
				'defaut' => lire_config('prestashop_api/cle'),
				'obligatoire' => 'oui',
			),
		),
	);
	
	return $saisies;
}
