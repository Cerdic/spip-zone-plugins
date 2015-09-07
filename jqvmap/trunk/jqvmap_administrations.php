<?php

/**
 * Fichier gérant l'installation et désinstallation du plugin jQuery Vector Maps.
 *
 * @plugin     jQuery Vector Maps
 *
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

/**
 * Fonction d'installation et de mise à jour du plugin jQuery Vector Maps.
 *
 * @param string $nom_meta_base_version
 *                                      Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *                                      Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 **/
function jqvmap_upgrade($nom_meta_base_version, $version_cible)
{
    $maj = array();

    $maj['create'] = array(array('maj_tables', array('spip_maps', 'spip_vectors')));
    // include_spip('base/importer_spip_maps');
    // $maj['create'][] = array('importer_spip_maps');
    // include_spip('base/importer_spip_vectors');
    // $maj['create'][] = array('importer_spip_vectors');
    $maj['1.1.0'] = array(
        array('sql_alter', "TABLE spip_vectors ADD color tinytext NOT NULL DEFAULT '' AFTER code_vector"),
    );
    include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin jQuery Vector Maps.
 *
 * @param string $nom_meta_base_version
 *                                      Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 **/
function jqvmap_vider_tables($nom_meta_base_version)
{
    sql_drop_table('spip_maps');
    sql_drop_table('spip_vectors');

    # Nettoyer les versionnages et forums
    sql_delete('spip_versions', sql_in('objet', array('map', 'vector')));
    sql_delete('spip_versions_fragments', sql_in('objet', array('map', 'vector')));
    sql_delete('spip_forum', sql_in('objet', array('map', 'vector')));

    effacer_meta($nom_meta_base_version);
}
