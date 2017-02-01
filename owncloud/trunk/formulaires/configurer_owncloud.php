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
		'directory_remote' => $config['directory_remote'],
		'activer_synchro' => $config['activer_synchro'],
		'activer_effacement_distant' => $config['activer_effacement_distant']
	);

	return (empty($config)) ? array() : $config;
}

/**
 * Vérification de configuration des paramètres de configurations
 *
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_configurer_owncloud_verifier_dist() {
	$erreurs = array();

	if (!_request('login')) {
		$erreurs['login'] = _T('info_obligatoire');
	}

	if (!_request('password')) {
		$erreurs['password'] = _T('info_obligatoire');
	}

	if (!_request('url_remote')) {
		$erreurs['url_remote'] = _T('info_obligatoire');
	}

	return $erreurs;
}

/**
 * Traiter les données du formulaire  de configuration des paramètres de configurations
 *
 * @return string
 *     Environnement du formulaire
 **/
function formulaires_configurer_owncloud_traiter_dist() {
	include_spip('inc/meta');

	$login = _request('login');

	$password = _request('password')?_request('password'):'';

	// Supprimer les slashs dans le repertoire owncloud à la fin de l'URL.
	$url_remote = rtrim(_request('url_remote'), '/');

	// Supprimer les slashs dans le repertoire owncloud au début et à la fin de la chaine pour gérer les sous répertoires.
	$directory_remote = ltrim(_request('directory_remote'), '/');
	$directory_remote = rtrim($directory_remote, '/');

	$activer_synchro = (_request('activer_synchro')=='on')?'on':'off';

	$activer_effacement_distant = (_request('activer_effacement_distant')=='on')?'on':'off';

	$meta_configuration = array(
		'login' => $login,
		'password' => $password,
		'url_remote' => $url_remote,
		'directory_remote' => $directory_remote,
		'activer_synchro' => $activer_synchro,
		'activer_effacement_distant' => $activer_effacement_distant);

	include_spip('inc/meta');
	ecrire_meta('owncloud', serialize($meta_configuration), '');

	$res = array(
			'editable' => true,
			'message_ok' => _T('config_info_enregistree')
	);

	$res['message_ok'] .= "<script type='text/javascript'>if (window.jQuery) $('.connection_dav').ajaxReload();</script>";
	
	return $res;
}
