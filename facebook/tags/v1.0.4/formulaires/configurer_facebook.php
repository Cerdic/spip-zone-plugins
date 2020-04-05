<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_facebook_saisies_dist() {
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'cle',
				'label' => _T('facebook:cle')
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'secret',
				'label' => _T('facebook:secret'),
				'type' => 'password'
			)
		)
	);
	return $saisies;
}

function formulaires_configurer_facebook_charger_dist() {
	// Contexte du formulaire.
	include_spip('inc/config');
	$config = lire_config('facebook');
	$config = array(
		'cle' => $config['cle'],
		'secret' => $config['secret']
	);
	return (empty($config)) ? array() : $config;
}
