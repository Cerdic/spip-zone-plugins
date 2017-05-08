<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Bouquinerie
 *
 * @plugin     Bouquinerie
 * @copyright  2017
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Bouquinerie\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Bouquinerie.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function bouq_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_livres', 'spip_livres_liens', 'spip_livres_auteurs', 'spip_livres_auteurs_liens')));

	$maj['1.0.1'] = array(
		array('sql_alter',"TABLE spip_livres CHANGE  `hauteur` `hauteur` VARCHAR(10) NOT NULL DEFAULT ''"),
		array('sql_alter',"TABLE spip_livres CHANGE  `largeur` `largeur` VARCHAR(10) NOT NULL DEFAULT ''"),
		array('sql_alter',"TABLE spip_livres CHANGE  `prix` `prix` FLOAT(6,2) NOT NULL DEFAULT 0"),
		);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Bouquinerie.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function bouq_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_livres');
	sql_drop_table('spip_livres_liens');
	sql_drop_table('spip_livres_auteurs');
	sql_drop_table('spip_livres_auteurs_liens');

	# Nettoyer les liens courants (le génie optimiser_base_disparus se chargera de nettoyer toutes les tables de liens)
	sql_delete('spip_documents_liens', sql_in('objet', array('livre', 'livres_auteur')));
	sql_delete('spip_mots_liens', sql_in('objet', array('livre', 'livres_auteur')));
	sql_delete('spip_auteurs_liens', sql_in('objet', array('livre', 'livres_auteur')));
	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('livre', 'livres_auteur')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('livre', 'livres_auteur')));
	sql_delete('spip_forum', sql_in('objet', array('livre', 'livres_auteur')));

	effacer_meta($nom_meta_base_version);
}
