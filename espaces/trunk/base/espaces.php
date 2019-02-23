<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Espaces
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Espaces\Pipelines
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
function espaces_declarer_tables_interfaces($interfaces) {

  $interfaces['table_des_tables']['espaces'] = 'espaces';

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
function espaces_declarer_tables_objets_sql($tables) {

  $tables['spip_espaces'] = array(
    'type' => 'espace',
    'principale' => 'oui',
    'field'=> array(
      'id_espace'          => 'bigint(21) NOT NULL',
      'titre'              => 'varchar(255) NOT NULL DEFAULT ""',
      'descriptif'         => 'text NOT NULL DEFAULT ""',
      'texte'              => 'text NOT NULL DEFAULT ""',
      'mesure'             => 'int(11) NOT NULL DEFAULT 0',
      'unite'              => 'varchar(10) NOT NULL DEFAULT ""',
      'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
      'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
      'lang'               => 'VARCHAR(10) NOT NULL DEFAULT ""',
      'langue_choisie'     => 'VARCHAR(3) DEFAULT "non"',
      'id_trad'            => 'bigint(21) NOT NULL DEFAULT 0',
      'maj'                => 'TIMESTAMP'
    ),
    'key' => array(
      'PRIMARY KEY'        => 'id_espace',
      'KEY lang'           => 'lang',
      'KEY id_trad'        => 'id_trad',
      'KEY statut'         => 'statut',
    ),
    'titre' => 'titre AS titre, lang AS lang',
    #'date' => '',
    'champs_editables'  => array('titre', 'descriptif', 'texte',  'mesure', 'unite'),
    'champs_versionnes' => array('titre', 'descriptif', 'texte', 'mesure', 'unite'),
    'rechercher_champs' => array("titre" => 8, "descriptif" => 5, 'texte',),
    'tables_jointures'  => array('spip_espaces_liens'),
    'statut_textes_instituer' => array(
      'prepa'    => 'texte_statut_en_cours_redaction',
      'prop'     => 'texte_statut_propose_evaluation',
      'publie'   => 'texte_statut_publie',
      'refuse'   => 'texte_statut_refuse',
      'poubelle' => 'texte_statut_poubelle',
    ),
    'statut'=> array(
      array(
        'champ'     => 'statut',
        'publie'    => 'publie',
        'previsu'   => 'publie,prop,prepa',
        'post_date' => 'date',
        'exception' => array('statut','tout')
      )
    ),
    'texte_changer_statut' => 'espace:texte_changer_statut_espace',


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
function espaces_declarer_tables_auxiliaires($tables) {

  $tables['spip_espaces_liens'] = array(
    'field' => array(
      'id_espace'          => 'bigint(21) DEFAULT "0" NOT NULL',
      'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
      'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
      'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
      'rang_lien'          => 'int(4) NOT NULL DEFAULT "0"',
    ),
    'key' => array(
      'PRIMARY KEY'        => 'id_espace,id_objet,objet',
      'KEY id_espace'      => 'id_espace',
    )
  );

  return $tables;
}
