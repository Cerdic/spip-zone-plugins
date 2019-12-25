<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip('inc/config');

function formulaires_configurer_paypal_saisies_dist(){
	$config = lire_config('paypal');

	return array(
		array(
			'saisie' => 'radio',
			'options' => array(
				'nom' => 'environnement',
				'label' => _T('paypal:label_environnement'),
				'explication' => _T('paypal:explication_environnement'),
				'defaut' => $config['environnement'],
				'cacher_option_intro' => 'oui',
				'datas' => array(
					'test' => _T('paypal:label_environnement_test'),
					'prod' => _T('paypal:label_environnement_prod'),
				)
			)
		),
		array(
			'saisie' => 'email',
			'options' => array(
				'nom' => 'account_prod',
				'label' => _T('paypal:label_account'),
				'explication' => _T('paypal:explication_account'),
				'obligatoire' => 'oui',
				'defaut' => $config['account_prod'],
				'afficher_si' => '@environnement@ == "prod"',
			)
		),
		array(
			'saisie' => 'email',
			'options' => array(
				'nom' => 'account_test',
				'label' => _T('paypal:label_account'),
				'explication' => _T('paypal:explication_account'),
				'obligatoire' => 'oui',
				'defaut' => $config['account_test'],
				'afficher_si' => '@environnement@ == "test"',
			)
		),
		array(
			'saisie' => 'radio',
			'options' => array(
				'nom' => 'currency_code',
				'label' => _T('paypal:label_currency_code'),
				'defaut' => $config['currency_code'],
				'cacher_option_intro' => 'oui',
				'datas' => array(
					'EUR' => _T('paypal:label_currency_code_eur'),
					'USD' => _T('paypal:label_currency_code_usd'),
				)
			)
		),
	);

}

// On n'utilise pas la fonction de traitement par defaut car 
// il ne faut pas enregistrer en base le contenu des champs caches
// qui a ete invalide (mis a NULL) par Saisies
function formulaires_configurer_paypal_traiter_dist(){
	$config = lire_config('paypal');

	include_spip('inc/meta');
	foreach ($config as $k=>$v){
		if (!is_null(_request($k)))
			$config[$k] = _request($k);
	}
	ecrire_meta('paypal',serialize($config));
	
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}

