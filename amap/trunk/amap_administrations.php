<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Amap
 *
 * @plugin     Amap
 * @copyright  2013
 * @author     Pierre KUHN
 * @licence    GNU/GPL
 * @package    SPIP\Amap\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Amap.
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
				array('maj_tables', array('spip_amap_paniers', 'spip_amap_responsables', 'spip_amap_responsables_liens', 'spip_amap_livraisons', 'spip_amap_livraisons_liens')),
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
        create_rubrique("000. Agenda de la saison", "0");
        $id_rubrique = id_rubrique("000. Agenda de la saison");
        if ($id_rubrique >0) {
                create_rubrique("001. Distribution", $id_rubrique);
                create_rubrique("002. Événements", $id_rubrique);
        }
        create_rubrique("001. Archives", "0");
        ecrire_config('amap/email', 'oui');
}

/**
 * Fonction de désinstallation du plugin Amap.
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