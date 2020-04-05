<?php

/**
 * Administrations pour shortcut_url
 *
 * @plugin     shortcut_url
 * @copyright  2015
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\shortcut_url\administrations
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj des tables shortcut_url
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function shortcut_url_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_shortcut_urls', 'spip_shortcut_urls_logs', 'spip_shortcut_urls_bots'))
	);

	$maj['0.0.1'] = array(
		array('maj_tables', array('spip_shortcut_urls_bots'))
	);

	$maj['0.0.2'] = array(
		// Changer le type de champs pour le tri par click
		array('sql_alter','TABLE spip_shortcut_urls MODIFY COLUMN click bigint(11)')
	);

	$maj['0.0.3'] = array(
		// Ajouter un index à la table spip_shortcut_urls_logs
		array('sql_alter','TABLE spip_shortcut_urls_logs ADD INDEX (id_shortcut_url)')
	);

	$maj['0.0.4'] = array(
		// Ajouter un index à la table spip_shortcut_urls_logs et spip_shortcut_urls_bots
		// pour l'adresse ip
		array('sql_alter', 'TABLE spip_shortcut_urls_logs ADD INDEX (ip_address)'),
		array('sql_alter', 'TABLE spip_shortcut_urls_bots ADD INDEX (ip_address)'));

	$maj['0.0.5'] = array(
		// Suppression de unsigned et auto incremente pour compat sqlite
		array('sql_alter', 'TABLE spip_shortcut_urls MODIFY id_shortcut_url BIGINT(21) NOT NULL AUTO_INCREMENT'),
		array('sql_alter', 'TABLE spip_shortcut_urls_logs MODIFY id_shortcut_urls_log BIGINT(21) NOT NULL AUTO_INCREMENT')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation/suppression des tables shortcut_url
 *
 * @param string $nom_meta_base_version
 */
function shortcut_url_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_shortcut_urls,spip_shortcut_urls_logs,spip_shortcut_urls_bots');

	// Nettoyer spip_auteur_liens
	sql_delete('spip_auteurs_liens', sql_in('objet', array('shortcut_url')));
	// Nettoyer les versionnages
	sql_delete('spip_versions', sql_in('objet', array('shortcut_url')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('shortcut_url')));
	effacer_meta('shortcut_url');
	effacer_meta($nom_meta_base_version);
}
