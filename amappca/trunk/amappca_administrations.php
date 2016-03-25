<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin AMAP, Producteurs et Consommateurs associés
 *
 * @plugin     AMAP, Producteurs et Consommateurs associés
 * @copyright  2016
 * @author     Rien
 * @licence    GNU/GPL
 * @package    SPIP\Amappca\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin AMAP, Producteurs et Consommateurs associés.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function amappca_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_amap_periodes', 'spip_amap_distributions', 'spip_amap_distributions_liens', 'spip_organisations', 'spip_commandes', 'spip_commandes_details'))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin AMAP, Producteurs et Consommateurs associés.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function amappca_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_amap_periodes");
	sql_drop_table("spip_amap_distributions");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('amap_periode', 'amap_distribution')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('amap_periode', 'amap_distribution')));
	sql_delete("spip_forum",                 sql_in("objet", array('amap_periode', 'amap_distribution')));

	effacer_meta($nom_meta_base_version);
}
