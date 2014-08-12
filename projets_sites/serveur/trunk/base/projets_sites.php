<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Sites pour projets
 * @copyright  2013
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function projets_sites_declarer_tables_interfaces($interfaces)
{
    $interfaces['table_des_tables']['projets_sites'] = 'projets_sites';

    $interfaces['table_des_traitements']['PERIMETRE_ACCES'][]= _TRAITEMENT_RACCOURCIS;
    $interfaces['table_des_traitements']['STATISTIQUES'][]= _TRAITEMENT_RACCOURCIS;
    $interfaces['table_des_traitements']['MOTEUR_RECHERCHE'][]= _TRAITEMENT_RACCOURCIS;
    $interfaces['table_des_traitements']['AUTRES_OUTILS'][]= _TRAITEMENT_RACCOURCIS;
    $interfaces['table_des_traitements']['REMARQUES'][]= _TRAITEMENT_RACCOURCIS;

    $interfaces['exceptions_des_jointures']['id_site'] = array('spip_projets_sites_liens', 'id_site');

    return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function projets_sites_declarer_tables_objets_sql($tables)
{

    $tables['spip_projets_sites'] = array(
        'type' => 'projets_site',
        'principale' => "oui",
        'table_objet_surnoms' => array('projetssite'), // table_objet('projets_site') => 'projets_sites'
        'field'=> array(
            "id_site"            => "bigint(21) NOT NULL",
            "titre"              => "text DEFAULT '' NOT NULL",
            "descriptif"         => "text DEFAULT '' NOT NULL",
            "type_site"          => "varchar(4) NOT NULL DEFAULT ''",
            "uniqid"             => "varchar(255) NOT NULL DEFAULT ''",
            "webservice"         => "text DEFAULT '' NOT NULL",
            "logiciel_nom"       => "varchar(25) NOT NULL DEFAULT ''",
            "logiciel_version"   => "varchar(25) NOT NULL DEFAULT ''",
            "logiciel_revision"  => "varchar(25) NOT NULL DEFAULT ''",
            "logiciel_plugins"   => "text DEFAULT '' NOT NULL",
            "fo_url"             => "varchar(255) NOT NULL DEFAULT ''",
            "fo_login"           => "varchar(25) NOT NULL DEFAULT ''",
            "fo_password"        => "varchar(25) NOT NULL DEFAULT ''",
            "bo_url"             => "varchar(255) NOT NULL DEFAULT ''",
            "bo_login"           => "varchar(25) NOT NULL DEFAULT ''",
            "bo_password"        => "varchar(25) NOT NULL DEFAULT ''",
            "serveur_nom"        => "varchar(255) NOT NULL DEFAULT ''",
            "serveur_port"       => "varchar(5) NOT NULL DEFAULT ''",
            "serveur_path"       => "varchar(255) NOT NULL DEFAULT ''",
            "serveur_logiciel"        => "text DEFAULT '' NOT NULL",
            "serveur_surveillance"    => "varchar(255) NOT NULL DEFAULT ''",
            "versioning_path"         => "varchar(255) NOT NULL DEFAULT ''",
            "versioning_trac"         => "varchar(255) NOT NULL DEFAULT ''",
            "versioning_type"         => "varchar(25) NOT NULL DEFAULT ''",
            "sas_serveur"        => "varchar(255) NOT NULL DEFAULT ''",
            "sas_protocole"      => "varchar(50) NOT NULL DEFAULT ''",
            "sas_login"          => "varchar(25) NOT NULL DEFAULT ''",
            "sas_password"       => "varchar(25) NOT NULL DEFAULT ''",
            "sgbd_type"          => "varchar(25) NOT NULL DEFAULT ''",
            "sgbd_version"       => "varchar(25) NOT NULL DEFAULT ''",
            "sgbd_serveur"       => "varchar(255) NOT NULL DEFAULT ''",
            "sgbd_port"          => "varchar(5) NOT NULL DEFAULT ''",
            "sgbd_nom"           => "varchar(50) NOT NULL DEFAULT ''",
            "sgbd_prefixe"       => "varchar(25) NOT NULL DEFAULT ''",
            "sgbd_login"         => "varchar(25) NOT NULL DEFAULT ''",
            "sgbd_password"      => "varchar(25) NOT NULL DEFAULT ''",
            "apache_modules"     => "text DEFAULT '' NOT NULL",
            "php_version"        => "varchar(25) NOT NULL DEFAULT ''",
            "php_memory"         => "varchar(10) NOT NULL DEFAULT ''",
            "php_extensions"     => "text DEFAULT '' NOT NULL",
            "sso"                => "varchar(25) NOT NULL DEFAULT ''",
            "perimetre_acces"    => "mediumtext NOT NULL",
            "statistiques"       => "mediumtext NOT NULL",
            "moteur_recherche"   => "mediumtext NOT NULL",
            "autres_outils"      => "mediumtext NOT NULL",
            "remarques"          => "text NOT NULL",
            "date_creation"      => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
            "maj"                => "TIMESTAMP"
        ),
        'key' => array(
            "PRIMARY KEY"        => "id_site",
        ),
        'titre' => "titre AS titre, '' AS lang",
        'date' => "date_creation",
        'champs_editables'  => array(
            'titre',
            'descriptif',
            'type_site',
            'uniqid',
            'webservice',
            'logiciel_nom',
            'logiciel_version',
            'logiciel_plugins',
            'logiciel_revision',
            'date_creation',
            'fo_fieldset',
            'fo_url',
            'fo_login',
            'fo_password',
            'bo_fieldset',
            'bo_url',
            'bo_login',
            'bo_password',
            'serveur_nom',
            'serveur_port',
            'serveur_path',
            'serveur_logiciel',
            'serveur_surveillance',
            'versioning_path',
            'versioning_trac',
            'versioning_type',
            'sas_serveur',
            'sas_protocole',
            'sas_login',
            'sas_password',
            "sgbd_type",
            "sgbd_version",
            "sgbd_serveur",
            "sgbd_port",
            "sgbd_nom",
            "sgbd_prefixe",
            "sgbd_login",
            "sgbd_password",
            'apache_modules',
            'php_extensions',
            'php_version',
            'php_memory',
            'sso',
            'perimetre_acces',
            'statistiques',
            'moteur_recherche',
            'autres_outils',
            'remarques'
            ),
        'champs_versionnes' => array(
            'titre',
            'descriptif',
            'type_site',
            'uniqid',
            'webservice',
            'logiciel_nom',
            'logiciel_version',
            'logiciel_plugins',
            'logiciel_revision',
            'date_creation',
            'fo_fieldset',
            'fo_url',
            'fo_login',
            'fo_password',
            'bo_fieldset',
            'bo_url',
            'bo_login',
            'bo_password',
            'serveur_nom',
            'serveur_port',
            'serveur_path',
            'serveur_logiciel',
            'serveur_surveillance',
            'versioning_path',
            'versioning_trac',
            'versioning_type',
            'sas_serveur',
            'sas_protocole',
            'sas_login',
            'sas_password',
            "sgbd_type",
            "sgbd_version",
            "sgbd_serveur",
            "sgbd_port",
            "sgbd_nom",
            "sgbd_prefixe",
            "sgbd_login",
            "sgbd_password",
            'apache_modules',
            'php_extensions',
            'php_version',
            'php_memory',
            'sso',
            'perimetre_acces',
            'statistiques',
            'moteur_recherche',
            'autres_outils',
            'remarques'
            ),
        'rechercher_champs' => array(
            "titre" => 8,
            "descriptif" => 5,
            "logiciel_nom" => 6,
            "logiciel_version" => 6,
            "type_site" => 6,
            "uniqid" => 6,
            "fo_url" => 6,
            "fo_login" => 6,
            "fo_password" => 6,
            "bo_url" => 6,
            "bo_login" => 6,
            "serveur_nom" => 6,
            "serveur_path" => 6,
            "serveur_surveillance" => 6,
            "versioning_path" => 6,
            "versioning_trac" => 6,
            "versioning_type" => 6,
            "sas_serveur" => 6,
            "sas_protocole" => 6,
            "sgbd_type" => 6,
            "sgbd_nom" => 6,
            "apache_modules" => 6,
            "php_extensions" => 6,
            "php_version" => 3
            ),
        'tables_jointures'  => array('spip_projets_sites_liens'),


    );

    return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function projets_sites_declarer_tables_auxiliaires($tables)
{

    $tables['spip_projets_sites_liens'] = array(
        'field' => array(
            "id_site"            => "bigint(21) DEFAULT '0' NOT NULL",
            "id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
            "objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
            "vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
        ),
        'key' => array(
            "PRIMARY KEY"        => "id_site,id_objet,objet",
            "KEY id_site"        => "id_site"
        )
    );

    return $tables;
}


?>