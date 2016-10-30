<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Info Sites
 *
 * @plugin     Info Sites
 * @copyright  2014-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Info Sites.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 *
 * @return void
 **/
function info_sites_upgrade($nom_meta_base_version, $version_cible) {
	$maj['create'] = array(array('info_sites_menu_pages'));
	$maj['1.0.2'][] = array('info_sites_menu_pages');
	$maj['1.0.3'][] = array('info_sites_menu_pages');
	$maj['1.0.4'][] = array('info_sites_menu_pages');
	$maj['1.0.5'][] = array('info_sites_menu_pages');
	$maj['1.0.7'][] = array('info_sites_menu_pages');
	$maj['1.0.7'][] = array('info_sites_menu_pages');
	$maj['1.0.8'][] = array('info_sites_maj_108');
	$maj['1.1.0'][] = array(
		'maj_tables',
		array(
			'spip_projets',
		),
	);
	cextras_api_upgrade(info_sites_declarer_champs_extras(), $maj['1.1.0']);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin Info Sites.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 *
 * @return void
 **/
function info_sites_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	cextras_api_vider_tables(info_sites_declarer_champs_extras());
	// On efface la meta de menu du plugin
	effacer_meta('info_sites_menu');
	// Ici on efface tout le reste :
	effacer_meta($nom_meta_base_version);
}

function info_sites_menu_pages() {
	include_spip('inc/utils');
	include_spip('inc/meta');
	// liste des pages par défaut fournie par le plugin
	$liste_pages = array(
		'organisations' => array(
			'nom' => 'info_sites:menu_organisations',
			'icone' => 'fa fa-university fa-lg',
		),
		'contacts' => array(
			'nom' => 'info_sites:menu_contacts',
			'icone' => 'fa fa-users fa-lg',
		),
		'projets' => array(
			'nom' => 'info_sites:menu_projets',
			'icone' => 'fa fa-folder fa-lg',
		),
		'projets_cadres' => array(
			'nom' => 'info_sites:menu_projets_cadres',
			'icone' => 'fa fa-clipboard fa-lg',
		),
		'projets_sites' => array(
			'nom' => 'info_sites:menu_projets_sites',
			'icone' => 'fa fa-desktop fa-lg',
		),
		'commits' => array(
			'nom' => 'info_sites:menu_commits',
			'icone' => 'fa fa-code-fork fa-lg',
		),
		'auteurs' => array(
			'nom' => 'info_sites:menu_auteurs',
			'icone' => 'fa fa-users fa-lg',
		),
		'statistiques' => array(
			'nom' => 'info_sites:menu_statistiques',
			'icone' => 'fa fa-bar-chart-o fa-lg',
		),
	);
	$meta = lire_meta('info_sites_menu');
	if ($meta and $meta = @serialize($meta) and is_array($meta)) {

		if (!array_key_exists(array_keys($liste_pages), $meta)) {
			// ici il faudrait vérifier que `organisations`,
			// `projets` et `sites` soient bien dans la meta
			// pour permettre son utilisation standard à l'installation
			foreach ($liste_pages as $key => $value) {
				$meta[$key] = $liste_pages[$key];
			}
		} else {
			foreach ($meta as $key => $value) {
				if ($meta[$key] != $liste_pages[$key]) {
					$meta[$key] = $liste_pages[$key];
				}
			}
		}
		// maintenant qu'on a vérifié les valeurs de la meta selon nos besoins de base,
		// on stocke sa nouvelle valeur dans `spip_meta`
		ecrire_meta('info_sites_menu', @serialize($meta));
	} else {
		ecrire_meta('info_sites_menu', @serialize($liste_pages));
	}
}

function info_sites_maj_108() {
	include_spip('inc/config');

	$config_svp = lire_config('svp');

	if (is_null($config_svp)) {
		$config_svp = array();
		$config_svp['mode_runtime'] = 'non';
		$config_svp['mode_pas_a_pas'] = 'non';
		$config_svp['mode_log_verbeux'] = 'non';
		$config_svp['autoriser_activer_paquets_obsoletes'] = 'non';
		$config_svp['depot_editable'] = 'non';
	} elseif (isset($config_svp['mode_runtime']) and $config_svp['mode_runtime'] != 'non') {
		$config_svp['mode_runtime'] = 'non';
	} else {
		$config_svp['mode_runtime'] = 'non';
	}
	$config_svp = serialize($config_svp);
	ecrire_config('svp', $config_svp);

}

