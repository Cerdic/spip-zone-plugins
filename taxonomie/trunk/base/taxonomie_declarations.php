<?php
/**
 * Déclarations relatives à la base de données.
 *
 * @package SPIP\TAXONOMIE\CONFIGURATION
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des alias de tables et des filtres automatiques de champs.
 *
 * @pipeline declarer_tables_interfaces
 *
 * @param array $interfaces
 *        Déclarations d'interface pour le compilateur.
 *
 * @return array
 *        Déclarations d'interface pour le compilateur mises à jour.
 */
function taxonomie_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['taxons'] = 'taxons';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux du plugin. Le plugin ajoute l'objet taxon au travers de la
 * seule table `spip_taxons` qui contient aussi les taxons de type `espèce`.
 *
 * L'objet taxon est défini comme une arborescence de taxons du règne au rang le plus petit dans le règne.
 * Les taxons de rang égal ou inférieur à l'espèce font aussi partie de cette table. Les champs principaux sont les
 * suivants :
 *        - `nom_scientifique` est le nom en latin. Il est unique pour un rang taxonomique donné.
 *        - `rang` taxonomique est une valeur parmi `kingdom`, `phylum`, `class`, `order`, `family`, `genus`, `species`...
 *        - `nom_commun` est le nom vulgaire, si possible normalisé par une commission officielle. Il peut coïncider ou
 *           pas avec le nom vernaculaire.
 *        - `auteur` est une information composée d'un ou plusieurs noms complétés par une date (ex : Linneus, 1798).
 *        - `tsn` est l'identifiant numérique unique du taxon dans la base taxonomique ITIS.
 *        - `tsn_parent` permet de créer l'arborescence taxonomique du règne conformément à l'organisation de la base
 *        ITIS.
 *        - `espece` indique si oui ou non le taxon à un rang supérieur ou inférieur ou égal à `species`.
 *
 * @pipeline declarer_tables_objets_sql
 *
 * @param array $tables
 *        Description des tables de la base.
 *
 * @return array
 *        Description des tables de la base complétée par celles du plugin.
 */
function taxonomie_declarer_tables_objets_sql($tables) {

	$tables['spip_taxons'] = array(
		'type' => 'taxon',
		'principale' => 'oui',
		'field'=> array(
			'id_taxon'          => "bigint(21) NOT NULL",
			'nom_scientifique'	=> "varchar(35) DEFAULT '' NOT NULL",
			'indicateurs'       => "varchar(32) DEFAULT '' NOT NULL",
			'rang_taxon'		=> "varchar(15) DEFAULT '' NOT NULL",
			'regne'				=> "varchar(10) DEFAULT '' NOT NULL",
			'nom_commun'		=> "text DEFAULT '' NOT NULL",
			'auteur'			=> "varchar(100) DEFAULT '' NOT NULL",
			'descriptif'		=> "text DEFAULT '' NOT NULL",
			'texte'             => "longtext DEFAULT '' NOT NULL",
			'tsn'				=> "bigint(21) NOT NULL",
			'tsn_parent'		=> "bigint(21) NOT NULL",
			'sources'           => "text NOT NULL",
			'importe'           => "varchar(3) DEFAULT 'non' NOT NULL",
			'edite'             => "varchar(3) DEFAULT 'non' NOT NULL",
			'espece'            => "varchar(3) DEFAULT 'non' NOT NULL",
			'statut'            => "varchar(10) DEFAULT 'prop' NOT NULL",
			'maj'				=> "TIMESTAMP"
    ),
		'key' => array(
			'PRIMARY KEY' => 'id_taxon',
            'KEY tsn'     => 'tsn',
			'KEY statut'  => 'statut',
			'KEY espece'  => 'espece',
			'KEY importe' => 'importe',
			'KEY edite'   => 'edite',
		),
        'titre' => "nom_scientifique AS titre, '' AS lang",

        'champs_editables'  => array('nom_commun', 'descriptif', 'texte', 'sources'),
        'champs_versionnes' => array('nom_commun', 'descriptif', 'texte', 'sources'),
        'rechercher_champs' => array('nom_scientifique' => 10, 'nom_commun' => 10, 'auteur' => 2, 'descriptif' => 5, 'texte' => 5),
        'tables_jointures'  => array(),
        'statut_textes_instituer' => array(
            'prop'     => 'taxon:texte_statut_prop',
            'publie'   => 'taxon:texte_statut_publie',
            'poubelle' => 'taxon:texte_statut_poubelle',
        ),
        'statut'=> array(
            array(
                'champ'     => 'statut',
                'publie'    => 'publie',
                'previsu'   => 'publie,prop',
                'exception' => array('statut', 'tout')
            )
        ),
        'texte_changer_statut' => 'taxon:texte_changer_statut_taxon',

		// Textes standard
		'texte_retour' 			=> 'icone_retour',
		'texte_modifier' 		=> 'taxon:icone_modifier_taxon',
		'texte_creer' 			=> 'taxon:icone_creer_taxon',
		'texte_creer_associer' 	=> '',
		'texte_signale_edition' => '',
		'texte_objet' 			=> 'taxon:titre_taxon',
		'texte_objets' 			=> 'taxon:titre_taxons',
		'info_aucun_objet'		=> 'taxon:info_aucun_taxon',
		'info_1_objet' 			=> 'taxon:info_1_taxon',
		'info_nb_objets' 		=> 'taxon:info_nb_taxons',
		'texte_logo_objet' 		=> 'taxon:titre_logo_taxon',
	);

	return $tables;
}
