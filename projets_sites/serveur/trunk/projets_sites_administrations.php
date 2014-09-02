<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Sites pour projets
 *
 * @plugin     Sites pour projets
 * @copyright  2013-2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Sites pour projets.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function projets_sites_upgrade($nom_meta_base_version, $version_cible)
{
    $maj = array();

    $maj['create'] = array(array('maj_tables', array('spip_projets_sites', 'spip_projets_sites_liens')));
    $maj['1.1.0'] = array(
        array('sql_alter', "TABLE spip_projets_sites CHANGE sas_dpi sas_serveur varchar(255) NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE application_serveur serveur_nom varchar(255) NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE application_path serveur_path varchar(255) NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE application_surveillance serveur_surveillance varchar(255) NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE svn_path versioning_path varchar(255) NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE svn_trac versioning_trac varchar(255) NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites ADD titre text DEFAULT '' NOT NULL AFTER id_site"),
        array('sql_alter', "TABLE spip_projets_sites ADD descriptif text DEFAULT '' NOT NULL AFTER titre"),
        array('sql_alter', "TABLE spip_projets_sites ADD webservice text DEFAULT '' NOT NULL AFTER uniqid"),
        array('sql_alter', "TABLE spip_projets_sites ADD serveur_logiciel text DEFAULT '' NOT NULL AFTER serveur_path"),
        array('sql_alter', "TABLE spip_projets_sites ADD versioning_type varchar(25) NOT NULL DEFAULT '' AFTER versioning_trac"),
        array('sql_alter', "TABLE spip_projets_sites ADD sas_protocole varchar(50) NOT NULL DEFAULT '' AFTER sas_serveur"),
        array('sql_alter', "TABLE spip_projets_sites ADD sas_login varchar(25) NOT NULL DEFAULT '' AFTER sas_protocole"),
        array('sql_alter', "TABLE spip_projets_sites ADD sas_password varchar(25) NOT NULL DEFAULT '' AFTER sas_login"),
        array('sql_alter', "TABLE spip_projets_sites ADD apache_modules text DEFAULT '' NOT NULL AFTER sgbd_password"),
        array('sql_alter', "TABLE spip_projets_sites ADD php_version varchar(25) NOT NULL DEFAULT '' AFTER apache_modules"),
        array('sql_alter', "TABLE spip_projets_sites ADD php_memory varchar(10) NOT NULL DEFAULT '' AFTER php_version"),
        array('sql_alter', "TABLE spip_projets_sites ADD php_extensions text DEFAULT '' NOT NULL AFTER php_memory"),
    );

    /* un port peut avoir 5 chiffres (même si ce n'est utilisé dans notre cas)
     * On met logiciel_nom après webservice et suivi de logiciel_version
     * Ajout de :
     * - logiciel_revision
     * - logiciel_plugins
     * - sgbd_port
     * - sgbd_prefixe
     * - sgbd_version
    **/
    $maj['1.2.0'] = array(
        array('sql_alter', "TABLE spip_projets_sites CHANGE serveur_port serveur_port varchar(5) NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE logiciel_nom logiciel_nom varchar(25) NOT NULL DEFAULT '' AFTER webservice"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE logiciel_version logiciel_version varchar(25) NOT NULL DEFAULT '' AFTER logiciel_nom"),
        array('sql_alter', "TABLE spip_projets_sites ADD logiciel_revision varchar(25) NOT NULL DEFAULT '' AFTER logiciel_version"),
        array('sql_alter', "TABLE spip_projets_sites ADD logiciel_plugins text DEFAULT '' NOT NULL AFTER logiciel_revision"),
        array('sql_alter', "TABLE spip_projets_sites ADD sgbd_port varchar(5) NOT NULL DEFAULT '' AFTER sgbd_serveur"),
        array('sql_alter', "TABLE spip_projets_sites ADD sgbd_prefixe varchar(25) NOT NULL DEFAULT '' AFTER sgbd_nom"),
        array('sql_alter', "TABLE spip_projets_sites ADD sgbd_version varchar(25) NOT NULL DEFAULT '' AFTER sgbd_type"),
    );
    /*
     * On ajoute auteurs_admin et auteurs_webmestres après logiciel_plugins
    **/
    $maj['1.2.1'] = array(
        array('sql_alter', "TABLE spip_projets_sites ADD auteurs_webmestres text DEFAULT '' NOT NULL AFTER logiciel_plugins"),
        array('sql_alter', "TABLE spip_projets_sites ADD auteurs_admin text DEFAULT '' NOT NULL AFTER logiciel_plugins"),
    );

    /*
     * Le logo étant calculé à partir de la clé primaire, on ne peut avoir id_site.
     * On le  change pour id_projets_site
    **/
    $maj['1.3.0'] = array(
        array('sql_alter', "TABLE spip_projets_sites CHANGE id_site id_projets_site bigint(21) NOT NULL"),
        array('sql_alter', "TABLE spip_projets_sites_liens CHANGE id_site id_projets_site bigint(21) NOT NULL"),
    );

    include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Sites pour projets.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function projets_sites_vider_tables($nom_meta_base_version)
{

    sql_drop_table("spip_projets_sites");
    sql_drop_table("spip_projets_sites_liens");

    # Nettoyer les versionnages et forums
    sql_delete("spip_versions", sql_in("objet", array('projets_site')));
    sql_delete("spip_versions_fragments", sql_in("objet", array('projets_site')));
    sql_delete("spip_forum", sql_in("objet", array('projets_site')));

    effacer_meta($nom_meta_base_version);
}


?>