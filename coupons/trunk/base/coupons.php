<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Coupons de réduction
 * @copyright  2017
 * @author     Nicolas Dorigny
 * @licence    GNU/GPL
 * @package    SPIP\Coupons\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 *
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 *
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function coupons_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['coupons'] = 'coupons';

	return $interfaces;
}

/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 *
 * @param array $tables
 *     Description des tables
 *
 * @return array
 *     Description complétée des tables
 */
function coupons_declarer_tables_objets_sql($tables) {

	$tables['spip_coupons'] = array(
		'type'              => 'coupon',
		'principale'        => 'oui',
		'field'             => array(
			'id_coupon'                   => 'bigint(21) NOT NULL',
			'titre'                       => 'text',
			'code'                        => 'varchar(25) NOT NULL DEFAULT ""',
			'montant'                     => 'decimal(15,4)',
			'id_commandes_detail_origine' => 'bigint(21) NOT NULL DEFAULT 0',
			'id_commande'                 => 'bigint(21) NOT NULL DEFAULT 0',
			'restriction_taxe'            => 'decimal(4,4)',
			'date'                        => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'maj'                         => 'TIMESTAMP',
		),
		'key'               => array(
			'PRIMARY KEY' => 'id_coupon',
		),
		'titre'             => 'titre AS titre, "" AS lang',
		'date'              => 'date',
		'page'              => false,
		'champs_editables'  => array('titre', 'code', 'montant', 'restriction_taxe'),
		'champs_versionnes' => array('titre', 'code', 'montant', 'restriction_taxe'),
		'rechercher_champs' => array("titre" => 10),
		'tables_jointures'  => array(),

	);

	return $tables;
}

function coupons_declarer_champs_extras($champs = array()) {

	$champs['spip_produits']['bon_cadeau'] = array(
		'options' =>
			array(
				'nom'        => 'bon_cadeau',
				'label_case' => _T('coupons:bon_cadeau'),
				'li_class'   => 'pleine_largeur',
				'valeur_oui' => 'on',
				'sql'        => 'varchar(3) DEFAULT \'\' NOT NULL',
				'versionner' => 'on',
				'defaut'     => '',
			),
		'saisie'  => 'case',
	);

	return $champs;
}