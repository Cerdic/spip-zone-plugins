<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Inc\Facteur_factory
 */



/**
 * Recuperer la config par defaut de Facteur, en s'assurant que les meta ont bien ete migrees
 * @return array
 */
function facteur_config_default() {
	if (!function_exists('lire_config')) {
		include_spip('inc/config');
	}

	// si jamais les meta sont pas migrees... le faire a l'arrache !
	if (isset($GLOBALS['meta']["facteur_smtp"])) {
		include_spip('facteur_administrations');
		facteur_migre_metas_to_config();
	}

	$config = lire_config('facteur');

	if ($config['adresse_envoi'] !== 'oui' or !$config['adresse_envoi_email']) {
		$config['adresse_envoi_email'] = (isset($GLOBALS['meta']["email_envoi"]) AND $GLOBALS['meta']["email_envoi"]) ?
					$GLOBALS['meta']["email_envoi"]
					: $GLOBALS['meta']['email_webmaster'];

		if (!function_exists('extraire_multi')) {
			include_spip('inc/filtres');
		}
		$config['adresse_envoi_nom'] = strip_tags(extraire_multi($GLOBALS['meta']['nom_site']));
	}

	$config['adresses_site'] = array(
		$GLOBALS['meta']['adresse_site'] . '/',
		url_de_base(),
	);

	return $config;
}


/**
 * Generer le FacteurXXX selon la config par defaut/passee en options
 * @param array $options
 * @return \SPIP\Facteur\FacteurMail
 * @throws \PHPMailer\PHPMailer\Exception
 */
function facteur_factory($options = array()) {

	if (!is_array($options)) {
		$options = array();
	}
	$options = array_merge(facteur_config_default(), $options);

	$config_mailer = $options['mailer'];

	switch ($config_mailer) {
		case 'smtp':
			include_spip('inc/Facteur/FacteurSMTP');
			return new SPIP\Facteur\FacteurSMTP($options);

		case 'mailjet':
			include_spip('inc/Facteur/FacteurMailjet');
			return new SPIP\Facteur\FacteurMailjet($options);

		case 'mail':
		default:
			include_spip('inc/Facteur/FacteurMail');
			return new SPIP\Facteur\FacteurMail($options);
	}

}