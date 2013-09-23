<?php
/**
 * Fichier gérant l'installation et désinstallation du plugins Amap
 *
 * @plugin     Amap
 * @copyright  2010-2013
 * @author     Stephane Moulinet
 * @author     E-cosystems
 * @author     Pierre KUHN
 * @licence    GPL v3
 * @package    SPIP\Amap\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/cextras');
include_spip('inc/rubriques');
include_spip('base/amap');

/**
 * Fonction d'installation et de mise à jour du @plugin     Amap.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function amap_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
				array('maj_tables', array('spip_amap_paniers', 'spip_amap_responsables', 'spip_amap_livraisons')),
				array('amap_rubriques'),
	);

	cextras_api_upgrade(amap_declarer_champs_extras(), $maj['create']);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction d'installation des rubriques
 *
**/

function amap_rubriques(){
	creer_rubrique_nommee("000. Agenda de la saison");
	creer_rubrique_nommee("000. Agenda de la saison/001. Distribution");
	creer_rubrique_nommee("000. Agenda de la saison/002. Événements");
	creer_rubrique_nommee("001. Archives");
	ecrire_config('amap/email', 'oui');
}

/**
 * Fonction de désinstallation du @plugin     Amap.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function amap_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_amap_paniers");
	sql_drop_table("spip_amap_responsables");
	sql_drop_table("spip_amap_livraisons");

	# Supprimer les champs extrats
	cextras_api_vider_tables(amap_declarer_champs_extras());

	effacer_meta('amap_mail');
	effacer_meta($nom_meta_base_version);
}

?>
