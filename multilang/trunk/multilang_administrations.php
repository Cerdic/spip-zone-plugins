<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin Multilang
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'upgrade/maj
 * On crée une configuration par défaut
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function multilang_upgrade($nom_meta_base_version, $version_cible) {

	$maj = array();

	// Installation : config par défaut
	$maj['create'] = array(
		array('multilang_creer_config'),
	);

	// Màj 0.1.1 : réorganisation de la config
	$maj['0.1.1'] = array(
		array('multilang_maj_011'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

}

/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function multilang_vider_tables($nom_meta_base_version) {
	effacer_meta('multilang');
	effacer_meta($nom_meta_base_version);
}

/**
 * Configuration par défaut à l'installation
 *
 * @return void
 */
function multilang_creer_config() {
	include_spip('inc/config');
	$config = lire_config('multilang', array());
	$config_defaut = array_merge_recursive(array(
		'formulaires' => array(
			'siteconfig' => 'on',
			'rubrique'   => 'on',
			'auteur'     => 'on',
			'document'   => 'on',
	)), $config);
	ecrire_config('multilang', $config_defaut);
}

/**
 * Mise à jour schéma 0.1.1 : réorganisation de la config
 *
 * On met tout ce qui concerne les formulaires dans une clé `formulaires`.
 *
 * @return void
 */
function multilang_maj_011() {
	include_spip('inc/config');
	include_spip('base_objets');
	include_spip('inc/filtres');
	$config          = lire_config('multilang', array());
	$formulaires     = array_map('objet_type', array_keys(lister_tables_objets_sql()));
	$formulaires[]   = 'siteconfig';
	$cfg_formulaires = array();
	foreach ($config as $cle => $valeur) {
		$surnoms   = objet_info($cle, 'type_surnoms');
		$surnoms   = is_array($surnoms) ? $surnoms : array();
		$surnoms[] = $cle;
		if (array_intersect($surnoms, $formulaires)) {
			$cfg_formulaires[$cle] = $valeur;
			foreach ($surnoms as $surnom) {
				$surnom = str_replace('-', '', $surnom); // mot-cle => motcle
				unset($config[$surnom]);
			}
		}
	}
	$config['formulaires'] = array_filter($cfg_formulaires);
	ecrire_config('multilang', $config);
}
