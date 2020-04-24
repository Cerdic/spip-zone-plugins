<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Administrations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function facteur_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	include_spip('inc/config');

	$maj['create'] = array(
		array('ecrire_config','facteur/mailer', 'mail'),
		array('ecrire_config','facteur/smtp_auth', 'non'),
		array('ecrire_config','facteur/smtp_secure', 'non'),
		array('ecrire_config','facteur/smtp_sender', ''),
		array('ecrire_config','facteur/filtre_images', 0),
		array('ecrire_config','facteur/filtre_css', 0),
		array('ecrire_config','facteur/filtre_iso_8859', 0),
		array('ecrire_config','facteur/adresse_envoi', 'non'),
		array('facteur_vieil_upgrade'),
	);

	$maj['2.0.0'] = array(
		array('facteur_migre_metas_to_config'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Migrer les N metas en une seule meta facteur que l'on accede via les fonctions lire_config/ecrire_config
 */
function facteur_migre_metas_to_config(){
	include_spip('inc/config');
	// ne pas migrer 2 fois
	if (!isset($GLOBALS['meta']["facteur"]) or !@unserialize($GLOBALS['meta']["facteur"])) {
		foreach (array(
			'adresse_envoi', 'adresse_envoi_email', 'adresse_envoi_nom', 'forcer_from',
			'cc', 'bcc',
			'mailer',
			'smtp_host', 'smtp_port', 'smtp_auth',
			'smtp_username', 'smtp_password', 'smtp_secure', 'smtp_sender', 'smtp_tls_allow_self_signed',
			'filtre_images', 'filtre_iso_8859',
		) as $config) {
			if (isset($GLOBALS['meta']["facteur_$config"])) {
				ecrire_config("facteur/$config", $GLOBALS['meta']["facteur_$config"]);
				effacer_meta("facteur_$config");
			}
		}
	}

	if (isset($GLOBALS['meta']["facteur_smtp"])) {
		if (!lire_config("facteur/mailer",'')) {
			ecrire_config("facteur/mailer", $GLOBALS['meta']["facteur_smtp"] === 'oui' ? 'smtp' : 'mail');
		}
		effacer_meta('facteur_smtp');
	}
}

/**
 * migration depuis tres ancienne version du plugin spip_notifications, a la main
 */
function facteur_vieil_upgrade() {
	if (isset($GLOBALS['meta']['spip_notifications_version'])) {
		ecrire_config('facteur/mailer', ($GLOBALS['meta']['spip_notifications_smtp'] === 'oui') ? 'smtp' : 'mail');
		ecrire_config('facteur/smtp_auth', $GLOBALS['meta']['spip_notifications_smtp_auth']);
		ecrire_config('facteur/smtp_secure', $GLOBALS['meta']['spip_notifications_smtp_secure']);
		ecrire_config('facteur/smtp_sender', $GLOBALS['meta']['spip_notifications_smtp_sender']);
		ecrire_config('facteur/filtre_images', $GLOBALS['meta']['spip_notifications_filtre_images']);
		ecrire_config('facteur/filtre_css', $GLOBALS['meta']['spip_notifications_filtre_css']);
		ecrire_config('facteur/filtre_iso_8859', $GLOBALS['meta']['spip_notifications_filtre_iso_8859']);
		ecrire_config('facteur/adresse_envoi', $GLOBALS['meta']['spip_notifications_adresse_envoi']);
		ecrire_config('facteur/adresse_envoi_nom', $GLOBALS['meta']['spip_notifications_adresse_envoi_nom']);
		ecrire_config('facteur/adresse_envoi_email', $GLOBALS['meta']['spip_notifications_adresse_envoi_email']);
		// supprimer l'ancien nommage
		effacer_meta('spip_notifications_smtp');
		effacer_meta('spip_notifications_smtp_auth');
		effacer_meta('spip_notifications_smtp_secure');
		effacer_meta('spip_notifications_smtp_sender');
		effacer_meta('spip_notifications_filtre_images');
		effacer_meta('spip_notifications_filtre_css');
		effacer_meta('spip_notifications_filtre_iso_8859');
		effacer_meta('spip_notifications_adresse_envoi');
		effacer_meta('spip_notifications_adresse_envoi_nom');
		effacer_meta('spip_notifications_adresse_envoi_email');
		effacer_meta('spip_notifications_version');
		// KEZAKO ?
		include_spip('base/abstract_sql');
		sql_drop_table('spip_notifications', true);
	}
}


function facteur_vider_tables($nom_meta_base_version) {
	effacer_meta('facteur');
	effacer_meta($nom_meta_base_version);
}
