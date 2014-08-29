<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Commits de projet
 *
 * @plugin     Commits de projet
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Commits\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('inc/cextras');
include_spip('base/projets_depots');


/**
 * Fonction d'installation et de mise à jour du plugin Commits de projet.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function commits_upgrade($nom_meta_base_version, $version_cible)
{
    $maj = array();

    $maj['create'] = array(array('maj_tables', array('spip_commits')));
    cextras_api_upgrade(commits_declarer_champs_extras(), $maj['create']);

    include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Commits de projet.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function commits_vider_tables($nom_meta_base_version)
{

    sql_drop_table("spip_commits");
    cextras_api_vider_tables(commits_declarer_champs_extras());

    # Nettoyer les versionnages et forums
    sql_delete("spip_versions", sql_in("objet", array('commit')));
    sql_delete("spip_versions_fragments", sql_in("objet", array('commit')));
    sql_delete("spip_forum", sql_in("objet", array('commit')));

    effacer_meta($nom_meta_base_version);
}

?>