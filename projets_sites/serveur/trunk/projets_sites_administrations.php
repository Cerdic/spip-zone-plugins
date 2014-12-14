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

    /*
     * Il peut arriver que le numéro de version de la SGBD soit plus grand que 25. Exemple rencontré : `5.1.73-1.1+squeeze+build0+1-log`
    **/
    $maj['1.3.2'] = array(
        array('sql_alter', "TABLE spip_projets_sites CHANGE sgbd_version sgbd_version varchar(50) NOT NULL DEFAULT ''")
    );

    /*
     * On ajoute sas_port après sas_serveur
    **/
    $maj['1.3.3'] = array(
        array('sql_alter', "TABLE spip_projets_sites ADD sas_port varchar(5) NOT NULL DEFAULT '' AFTER sas_serveur"),
    );

    /*
     * On change le type_site qui prend plus de galons.
    **/
    $maj['1.4.0'] = array(
        array('sql_alter', "TABLE spip_projets_sites CHANGE type_site type_site varchar(7) NOT NULL DEFAULT '05rec'"),
        array('projets_sites_maj140'),
    );

    /*
     * On ajoute :
     * - logiciel_charset ;
     * - sgbd_charset ;
     * - sgbd_collation ;
     * - php_timezone ;
     * Et on change les couples login/password pour suivre ce qui se fait dans spip_auteurs.
    **/
    $maj['1.5.0'] = array(
        array('sql_alter', "TABLE spip_projets_sites ADD logiciel_charset varchar(25) NOT NULL DEFAULT '' AFTER logiciel_plugins"),
        array('sql_alter', "TABLE spip_projets_sites ADD sgbd_charset tinytext NOT NULL DEFAULT '' AFTER sgbd_prefixe"),
        array('sql_alter', "TABLE spip_projets_sites ADD sgbd_collation tinytext NOT NULL DEFAULT '' AFTER sgbd_prefixe"),
        array('sql_alter', "TABLE spip_projets_sites ADD php_timezone tinytext NOT NULL DEFAULT '' AFTER php_extensions"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE fo_login fo_login varchar(255) NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE fo_password fo_password tinytext NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE bo_login bo_login varchar(255) NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE bo_password bo_password tinytext NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE sas_login sas_login varchar(255) NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE sas_password sas_password tinytext NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE sgbd_login sgbd_login varchar(255) NOT NULL DEFAULT ''"),
        array('sql_alter', "TABLE spip_projets_sites CHANGE sgbd_password sgbd_password tinytext NOT NULL DEFAULT ''"),
        array('projets_sites_maj150'),
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

function projets_sites_maj140() {
    $projets_sites = sql_allfetsel('id_projets_site,type_site', 'spip_projets_sites');

    if (is_array($projets_sites) and count($projets_sites) > 0) {
        foreach ($projets_sites as $key => $projets_site) {
            switch ($projets_site['type_site']) {
                case 'prod':
                    sql_updateq(
                        'spip_projets_sites',
                        array('type_site' => '07prod'),
                        'id_projets_site=' . $projets_site['id_projets_site']
                    );
                    break;
                case 'prep':
                    sql_updateq(
                        'spip_projets_sites',
                        array('type_site' => '06prep'),
                        'id_projets_site=' . $projets_site['id_projets_site']
                    );
                    break;
                case 'rec':
                    sql_updateq(
                        'spip_projets_sites',
                        array('type_site' => '05rec'),
                        'id_projets_site=' . $projets_site['id_projets_site']
                    );
                    break;
                case 'dev':
                    sql_updateq(
                        'spip_projets_sites',
                        array('type_site' => '02dev'),
                        'id_projets_site=' . $projets_site['id_projets_site']
                    );
                    break;
                case '07pr':
                    sql_updateq(
                        'spip_projets_sites',
                        array('type_site' => '07prod'),
                        'id_projets_site=' . $projets_site['id_projets_site']
                    );
                    break;
                case '06pr':
                    sql_updateq(
                        'spip_projets_sites',
                        array('type_site' => '06prep'),
                        'id_projets_site=' . $projets_site['id_projets_site']
                    );
                    break;
                case '05re':
                    sql_updateq(
                        'spip_projets_sites',
                        array('type_site' => '05rec'),
                        'id_projets_site=' . $projets_site['id_projets_site']
                    );
                    break;
                case '04te':
                    sql_updateq(
                        'spip_projets_sites',
                        array('type_site' => '04test'),
                        'id_projets_site=' . $projets_site['id_projets_site']
                    );
                    break;
                case '03in':
                    sql_updateq(
                        'spip_projets_sites',
                        array('type_site' => '03inte'),
                        'id_projets_site=' . $projets_site['id_projets_site']
                    );
                    break;
                case '02de':
                    sql_updateq(
                        'spip_projets_sites',
                        array('type_site' => 'O2dev'),
                        'id_projets_site=' . $projets_site['id_projets_site']
                    );
                    break;
                case '01lo':
                    sql_updateq(
                        'spip_projets_sites',
                        array('type_site' => '01local'),
                        'id_projets_site=' . $projets_site['id_projets_site']
                    );
                    break;
                default:
                    break;
            }
        }
    }
}

function projets_sites_maj150() {
    $projets_sites = sql_allfetsel('id_projets_site,type_site', 'spip_projets_sites');

    if (is_array($projets_sites) and count($projets_sites) > 0) {
        foreach ($projets_sites as $key => $projets_site) {
            switch ($projets_site['type_site']) {
                case '07prop':
                    sql_updateq(
                        'spip_projets_sites',
                        array('type_site' => '07prod'),
                        'id_projets_site=' . $projets_site['id_projets_site']
                    );
                    break;
                default:
                    break;
            }
        }
    }
}

?>