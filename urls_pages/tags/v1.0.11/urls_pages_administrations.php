<?php
/**
 * Fonctions d'installation et de désinstallation du plugin URLs Pages Étendues
 *
 * @plugin     URLs Pages Étendues
 * @copyright  2016
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Urls_pages_etendues\Administrations
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function urls_pages_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();

	// Ajout de la colonne `page` dans spip_urls et migration des URLs des pages
	$maj['1.0.0'] = array(
		array('sql_alter', "TABLE spip_urls ADD COLUMN page VARCHAR(255) NOT NULL DEFAULT ''"),
		array('urls_pages_maj_100'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 */
function urls_pages_vider_tables($nom_meta_base_version) {

	// Suppression des URLs des pages, puis de la colonne `page`
	sql_delete('spip_urls', array('page != \'\'', 'id_objet = 0', 'type = \'\''));
	sql_alter('TABLE spip_urls DROP COLUMN page');

	// Suppression meta
	effacer_meta($nom_meta_base_version);

	// Invalider le cache pour éviter une erreur undefined function url_perso
	include_spip('inc/invalideur');
	suivre_invalideur(1);
}


/**
 * Mise à jour schéma 1.0.0
 *
 * Migrer les URLs du meta `urls_pages` vers la table `spip_urls`.
 * Dans le meta, on ne conserve que les pages non converties.
 * On n'a plus besoin de la valeur `rewritebase`.
 *
 * @return void
 */
function urls_pages_maj_100(){

	include_spip('inc/config');
	if ($pages = lire_config('urls_pages')){
		$pages_non_converties = array();
		// on créé des nouvelles lignes dans spip_urls pour chaque page de la config
		foreach ($pages as $page => $url) {
			if ($page != 'rewritebase'
				and strlen($url)
			) {
				if (!sql_countsel('spip_urls', 'url = ' . sql_quote($url))){
					sql_insertq('spip_urls', array(
						'page'     => $page,
						'url'      => $url,
						'date'     => date('Y-m-d H:i:s'),
						'type'     => '',
						'id_objet' => 0,
						'perma'    => 1,
					));
				} else {
					$pages_non_converties[$page] = $url;
				}
			}
		}
		// On ne garde que les pages non converties dans la config
		$nouvelle_config = count($pages_non_converties) ? $pages_non_converties : '';
		ecrire_config('urls_pages', $nouvelle_config);
	}

}
