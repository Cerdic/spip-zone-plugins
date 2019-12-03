<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Inc\Facteur_factory
 */



/**
 * Recuperer la config de Facteur, avec eventuelle surcharge
 * en s'assurant que les meta ont bien ete migrees
 *
 * @param array $options
 * @return array
 */
function facteur_config($options = array()) {
	if (!function_exists('lire_config')) {
		include_spip('inc/config');
	}

	// si jamais les meta sont pas migrees... le faire a l'arrache !
	if (empty($GLOBALS['meta']['facteur']) or !@unserialize($GLOBALS['meta']['facteur'])) {
		include_spip('facteur_administrations');
		facteur_migre_metas_to_config();
	}

	$config = lire_config('facteur');
	if (!empty($options) and is_array($options)) {
		$config = array_merge($config, $options);
	}

	if ($config['adresse_envoi'] !== 'oui' or !$config['adresse_envoi_email']) {
		$config = array_merge($config, facteur_config_envoyeur_par_defaut());
	}

	$config['adresses_site'] = array(
		$GLOBALS['meta']['adresse_site'] . '/',
		url_de_base(),
	);

	// et on emule la globale facteur_smtp pour les plugins qui s'appuient dessus comme mailshot
	$GLOBALS['meta']['facteur_smtp'] = ($config['mailer'] === 'smtp' ? 'oui' : 'non');

	return $config;
}

/**
 * Generer la config par defaut de l'envoyeur, hors reglage specifique ou surcharge
 * @return array
 */
function facteur_config_envoyeur_par_defaut() {
	$config = array(
		'adresse_envoi_email' => '',
		'adresse_envoi_nom' => '',
	);

	$config['adresse_envoi_email'] = (isset($GLOBALS['meta']["email_envoi"]) AND $GLOBALS['meta']["email_envoi"]) ?
				$GLOBALS['meta']["email_envoi"]
				: $GLOBALS['meta']['email_webmaster'];

	if (!function_exists('extraire_multi')) {
		include_spip('inc/filtres');
	}

	$config['adresse_envoi_nom'] = strip_tags(extraire_multi($GLOBALS['meta']['nom_site']));

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
	$options = facteur_config($options);

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