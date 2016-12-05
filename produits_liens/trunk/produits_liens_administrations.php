<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin produits_liens
 *
 * @plugin     produits
 * @copyright  2015
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Produits_liens\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin produits.liens
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function produits_liens_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	include_spip('base/abstract_sql');
	include_spip('inc/config');
	
	$maj['create'] = array(
		array('maj_tables', array('spip_produits_liens')),
		array('ecrire_config', 'produits/produits_liens', array(
			'produits_objets' => '',
			)
		)
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin produits.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function produits_liens_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	include_spip('inc/config');
	
	sql_drop_table("spip_produits_liens");
	effacer_meta($nom_meta_base_version);
	effacer_config('produits/produits_liens');
}
