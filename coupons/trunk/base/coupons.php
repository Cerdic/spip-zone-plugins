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

	$interfaces['table_des_tables']['coupons']           = 'coupons';
	$interfaces['table_des_tables']['coupons_commandes'] = 'coupons_commandes';

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
		'type'                 => 'coupon',
		'principale'           => 'oui',
		'field'                => array(
			'id_coupon'           => 'bigint(21) NOT NULL',
			'titre'               => 'text',
			'code'                => 'varchar(25) NOT NULL DEFAULT ""',
			'montant'             => 'decimal(20,6)',
			'id_commandes_detail' => 'bigint(21) NOT NULL DEFAULT 0',
			'id_produit'          => 'bigint(21) NULL',
			'id_auteur'           => 'bigint(21) NULL',
			'actif'               => 'varchar(3)  DEFAULT "" NOT NULL',
			'date_validite'       => 'datetime NULL DEFAULT NULL',
			'restriction_taxe'    => 'decimal(20,6)',
			'date'                => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'maj'                 => 'TIMESTAMP',
		),
		'key'                  => array(
			'PRIMARY KEY' => 'id_coupon',
		),
		'titre'                => 'titre AS titre, "" AS lang',
		'date'                 => 'date',
		'page'                 => false,
		'champs_editables'     => array(
			'titre',
			'code',
			'montant',
			'id_produit',
			'id_auteur',
			'actif',
			'date_validite',
			'restriction_taxe',
		),
		'champs_versionnes'    => array(
			'titre',
			'code',
			'montant',
			'id_produit',
			'id_auteur',
			'id_commandes_detail',
			'actif',
			'date_validite',
			'restriction_taxe',
		),
		'rechercher_champs'    => array("titre" => 10),
		'tables_jointures'     => array(),
		'texte_changer_statut' => 'coupon:texte_changer_statut_coupon',
	);

	return $tables;
}

/**
 * Une table qui contient les différentes utilisations d'un coupon
 * une ligne par utilisation / par commande, avec le montant utilisé pour la commande
 *
 * @param $tables_auxiliaires
 *
 * @return array
 */
function coupons_declarer_tables_auxiliaires($tables_auxiliaires) {

	$spip_coupons_commandes = array(
		'id_coupon'   => 'bigint(21) DEFAULT "0" NOT NULL',
		'id_commande' => 'bigint(21) DEFAULT "0" NOT NULL',
		'id_auteur'   => 'bigint(21) DEFAULT "0" NOT NULL',
		'montant'     => 'decimal(20,6)',
		'maj'         => 'TIMESTAMP',
	);

	$spip_coupons_commandes_cles = array(
		'PRIMARY KEY' => 'id_coupon, id_commande',
	);

	$tables_auxiliaires['spip_coupons_commandes'] = array(
		'field' => &$spip_coupons_commandes,
		'key'   => &$spip_coupons_commandes_cles,
	);

	return $tables_auxiliaires;
}

function coupons_declarer_champs_extras($champs = array()) {

	$champs['spip_produits']['bon_cadeau'] = array(
		'options' =>
			array(
				'nom'        => 'bon_cadeau',
				'label_case' => _T('coupons:generer_bon_cadeau'),
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