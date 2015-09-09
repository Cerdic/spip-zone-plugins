<?php

/**
 * Déclarations relatives à la base de données.
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
 * Déclaration des alias de tables et filtres automatiques de champs.
 *
 * @pipeline declarer_tables_interfaces
 *
 * @param array $interfaces
 *                          Déclarations d'interface pour le compilateur
 *
 * @return array
 *               Déclarations d'interface pour le compilateur
 */
function jqvmap_declarer_tables_interfaces($interfaces)
{
    $interfaces['table_des_tables']['maps'] = 'maps';
    $interfaces['table_des_tables']['vectors'] = 'vectors';

    return $interfaces;
}

/**
 * Déclaration des objets éditoriaux.
 *
 * @pipeline declarer_tables_objets_sql
 *
 * @param array $tables
 *                      Description des tables
 *
 * @return array
 *               Description complétée des tables
 */
function jqvmap_declarer_tables_objets_sql($tables)
{
    $tables['spip_maps'] = array(
        'type' => 'map',
        'principale' => 'oui',
        'page' => false,
        'field' => array(
            'id_map' => 'bigint(21) NOT NULL',
            'titre' => "text NOT NULL DEFAULT ''",
            'descriptif' => "text NOT NULL DEFAULT ''",
            'width' => "tinytext NOT NULL DEFAULT ''",
            'height' => "tinytext NOT NULL DEFAULT ''",
            'code_map' => "mediumtext NOT NULL DEFAULT ''",
            'background_color' => "tinytext NOT NULL DEFAULT ''",
            'border_color' => "tinytext NOT NULL DEFAULT ''",
            'border_opacity' => 'decimal(3,2) NOT NULL DEFAULT 1',
            'border_width' => 'int(6) NOT NULL DEFAULT 0',
            'color' => "tinytext NOT NULL DEFAULT ''",
            'enable_zoom' => "ENUM('true','false') DEFAULT 'true'",
            'hover_color' => "tinytext NOT NULL DEFAULT ''",
            'hover_opacity' => 'decimal(3,2) NOT NULL DEFAULT 1',
            'normalize_function' => "text NOT NULL DEFAULT ''",
            'scale_colors' => "text NOT NULL DEFAULT ''",
            'selected_color' => "tinytext NOT NULL DEFAULT ''",
            'selected_region' => "text NOT NULL DEFAULT ''",
            'show_tooltip' => "ENUM('true','false') DEFAULT 'true'",
            'data_name' => "text NOT NULL DEFAULT ''",
            'statut' => "varchar(20)  DEFAULT '0' NOT NULL",
            'date' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
            'maj' => 'TIMESTAMP',
        ),
        'key' => array(
            'PRIMARY KEY' => 'id_map',
            'KEY statut' => 'statut',
        ),
        'titre' => "titre AS titre, '' AS lang",
        'date' => 'date',
        'champs_editables' => array('titre', 'descriptif', 'width', 'height', 'code_map', 'background_color', 'border_color', 'border_opacity', 'border_width', 'color', 'enable_zoom', 'hover_color', 'hover_opacity', 'normalize_function', 'scale_colors', 'selected_color', 'selected_region', 'show_tooltip', 'data_name'),
        'champs_versionnes' => array('titre', 'descriptif', 'width', 'height', 'code_map', 'background_color', 'border_color', 'border_opacity', 'border_width', 'color', 'enable_zoom', 'hover_color', 'hover_opacity', 'normalize_function', 'scale_colors', 'selected_color', 'selected_region', 'show_tooltip', 'data_name'),
        'rechercher_champs' => array('titre' => 8, 'descriptif' => 7),
        'tables_jointures' => array(),
        'statut_textes_instituer' => array(
            'prepa' => 'texte_statut_en_cours_redaction',
            'prop' => 'texte_statut_propose_evaluation',
            'publie' => 'texte_statut_publie',
            'refuse' => 'texte_statut_refuse',
            'poubelle' => 'texte_statut_poubelle',
        ),
        'statut' => array(
            array(
                'champ' => 'statut',
                'publie' => 'publie',
                'previsu' => 'publie,prop,prepa',
                'post_date' => 'date',
                'exception' => array('statut','tout'),
            ),
        ),
        'texte_changer_statut' => 'map:texte_changer_statut_map',

    );

    $tables['spip_vectors'] = array(
        'type' => 'vector',
        'principale' => 'oui',
        'page' => false,
        'field' => array(
            'id_vector' => 'bigint(21) NOT NULL',
            'id_map' => 'bigint(21) NOT NULL DEFAULT 0',
            'titre' => "text NOT NULL DEFAULT ''",
            'descriptif' => "text NOT NULL DEFAULT ''",
            'code_vector' => "text NOT NULL DEFAULT ''",
            'color' => "tinytext NOT NULL DEFAULT ''",
            'data' => 'decimal(10,2) NOT NULL DEFAULT 0',
            'path' => "text NOT NULL DEFAULT ''",
            'url_site' => "text NOT NULL DEFAULT ''",
            'maj' => 'TIMESTAMP',
        ),
        'key' => array(
            'PRIMARY KEY' => 'id_vector',
        ),
        'titre' => "titre AS titre, '' AS lang",
         #'date' => "",
        'champs_editables' => array('id_map', 'titre', 'descriptif', 'code_vector', 'color', 'path', 'url_site', 'data'),
        'champs_versionnes' => array('id_map', 'titre', 'descriptif', 'code_vector', 'color', 'path', 'url_site', 'data'),
        'rechercher_champs' => array('titre' => 8, 'code_vector' => 8),
        'tables_jointures' => array(),

    );

    return $tables;
}
