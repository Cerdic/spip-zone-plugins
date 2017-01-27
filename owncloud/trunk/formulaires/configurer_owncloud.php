<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_owncloud_saisies_dist() {
	$saisies = array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'configuration',
				'label' => _T('owncloud:cfg_configuration')
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'login',
						'label' => _T('owncloud:cfg_login'),
						'explication' => _T('owncloud:cfg_login_explication'),
						'obligatoire' => 'oui',

					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'password',
						'label' => _T('owncloud:cfg_password'),
						'type' => 'password',
						'explication' => _T('owncloud:cfg_password_explication'),
						'obligatoire' => 'oui',
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'url_remote',
						'label' => _T('owncloud:cfg_url_remote'),
						'explication' => _T('owncloud:cfg_url_remote_explication'),
						'obligatoire' => 'oui',
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'directory_remote',
						'label' => _T('owncloud:cfg_directory_remote'),
						'explication' => _T('owncloud:cfg_directory_remote_explication')
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'synchro',
				'label' => _T('owncloud:cfg_synchro')
			),
			'saisies' => array(
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'activer_synchro',
						'label' => _T('owncloud:cfg_activer_synchro'),
						'explication' => _T('owncloud:cfg_activer_synchro_explication')
					)
				),
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'activer_effacement_distant',
						'label' => _T('owncloud:cfg_activer_effacement_distant'),
						'explication' => _T('owncloud:cfg_activer_effacement_distant_explication'),
						'afficher_si' => '@activer_synchro@ == "on"'
					)
				),
			)
		),
	);
	return $saisies;
}

function formulaires_configurer_owncloud_charger_dist() {
	// Contexte du formulaire.
	include_spip('inc/config');
	$config = lire_config('owncloud');
	$config = array(
		'login' => $config['login'],
		'password' => $config['password'],
		'url_remote' => $config['url_remote'],
		'directory_remote' => ltrim($config['directory_remote'], '/'),
		'activer_synchro' => $config['activer_synchro'],
		'activer_effacement_distant' => $config['activer_effacement_distant']
	);
	return (empty($config)) ? array() : $config;
}
