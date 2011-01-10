<?php

/***************************************************************************\
 *  SPIPAL, Utilitaire de paiement en ligne pour SPIP                      *
 *                                                                         *
 *  Copyright (c) 2007 Thierry Schmit                                      *
 *  Copyright (c) 2011 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

global $spipal_tables_principales, $spipal_tables_auxiliaires, $tables_auxiliaires, $tables_principales, $table_des_tables;

$produits = array(
        'id_article'       => 'bigint(21) NOT NULL',
        'ref_produit'      => 'tinytext',
        'nom_com'          => 'tinytext',
        'don'              => 'tinyint(1) NOT NULL DEFAULT 0',
        'prix_unitaire_ht' => 'float NOT NULL DEFAULT 0.0',
        'tva'              => 'float NOT NULL DEFAULT 0.0'
		);

$produits_keys = array(
		"PRIMARY KEY"	=> "id_article"
		);
//---------------------------

$versements = array(
        'id_versement'     => 'bigint(21) NOT NULL',
        'item_number'      => 'tinytext',  //ref_produit, mais pas forcément, correspond au champ paypal du meme nom
                                           //devra correspondre à un idnetifiant de facture
        'id_auteur'        => 'bigint(21) NOT NULL default 0',
        'versement_ht'     => 'FLOAT not null default 0.0', //mc_gross
        'versement_taxes'  => 'FLOAT not null default 0.0', //mc_tax
        'versement_charges' => 'FLOAT not null default 0.0', //mc_fees
        'devise'        => "varchar(3) not null default 'EUR'",
        'date_versement'   => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
        'servi'            => 'tinyint(1) NOT NULL DEFAULT 0', //les actions liées au versement ont elles été effectuées ?
        'notification'     => 'text' //on sauvegarde la notification reçu de PayPal
		);

$versements_keys = array(
		"PRIMARY KEY"	=> "id_versement"
		);

$spipal_metas = array(
		"nom"	=> "VARCHAR (255) NOT NULL",
		"valeur"	=> "text DEFAULT ''",
		"impt"	=> "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
		"maj"	=> "TIMESTAMP");

$spipal_metas_keys = array(
		"PRIMARY KEY"	=> "nom");


$spipal_tables_principales['spip_spipal_produits'] =
	array('field' => &$produits, 'key' => &$produits_keys);
$spipal_tables_principales['spip_spipal_versements'] =
	array('field' => &$versements, 'key' => &$versements_keys);

$spipal_tables_auxiliaires['spip_spipal_metas']=
array('field' => &$spipal_metas, 'key' => &$spipal_metas_keys);

include_spip('base/serial');
$tables_principales = array_merge($tables_principales,  $spipal_tables_principales);

include_spip('base/auxiliaires');
$tables_auxiliaires = array_merge($tables_auxiliaires,  $spipal_tables_auxiliaires);

// si on declare les tables dans $table_des_tables, il faut mettre le prefixe
// 'spip_' dans l'index de $tables_principales

$table_des_tables['spip_spipal_produits']    = 'spipal_produits';
$table_des_tables['spip_spipal_versements']  = 'spipal_versements';

?>
