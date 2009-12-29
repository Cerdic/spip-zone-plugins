<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */

function catalogue_peupler_base() {
	// Attention, spécifier le NOM des TABLES et non le nom des BOUCLES !
	sql_insertq_multi('spip_variantes', array(
		array(
			'id_article' => 2,
			'titre' => 'Standard price',
			'descriptif' => '',
			'prix_ht' => '850.00',
			'tva' => '0.196',
			'statut' => 'publie'
			),
		array(
			'id_article' => 2,
			'titre' => 'Early Bird',
			'descriptif' => '',
			'prix_ht' => '750.00',
			'tva' => '0.196',
			'statut' => 'publie'
			),
		array(
			'id_article' => 2,
			'titre' => 'Concurrences subscribers',
			'descriptif' => '',
			'prix_ht' => '680.00',
			'tva' => '0.196',
			'statut' => 'publie'
			),
		array(
			'id_article' => 2,
			'titre' => 'Professor',
			'descriptif' => '',
			'prix_ht' => '380.00',
			'tva' => '0.196',
			'statut' => 'publie'
			),
		array(
			'id_article' => 2,
			'titre' => 'Student',
			'descriptif' => '',
			'prix_ht' => '250.00',
			'tva' => '0.196',
			'statut' => 'publie'
			),
		array(
			'id_article' => 80,
			'titre' => 'Tarif pr&eacute;-vente',
			'descriptif' => 'Valable jusqu\'au 31 janvier 2010 seulement',
			'prix_ht' => '795.00',
			'tva' => '0',
			'statut' => 'publie'
			),
		array(
			'id_article' => 80,
			'titre' => 'Tarif r&eacute;servation &agrave; l\'avance',
			'descriptif' => 'Valable du 1er f&eacute;vrier au 30 mars 2010',
			'prix_ht' => '860.00',
			'tva' => '0',
			'statut' => 'prepa'
			),
		array(
			'id_article' => 80,
			'titre' => 'Tarif normal',
			'descriptif' => 'Valable jusqu\'au 31 mai 2010',
			'prix_ht' => '890.00',
			'tva' => '0',
			'statut' => 'prepa'
			),
		array(
			'id_article' => 81,
			'titre' => 'Tarif pr&eacute;-vente',
			'descriptif' => 'Valable jusqu\'au 31 janvier 2010 seulement',
			'prix_ht' => '570.00',
			'tva' => '0',
			'statut' => 'publie'
			),
		array(
			'id_article' => 81,
			'titre' => 'Tarif r&eacute;servation &agrave; l\'avance',
			'descriptif' => 'Valable du 1er f&eacute;vrier au 30 mars 2010',
			'prix_ht' => '635.00',
			'tva' => '0',
			'statut' => 'prepa'
			),
		array(
			'id_article' => 81,
			'titre' => 'Tarif normal',
			'descriptif' => 'Valable jusqu\'au 31 mai 2010',
			'prix_ht' => '670.00',
			'tva' => '0',
			'statut' => 'prepa'
			),
		array(
			'id_article' => 82,
			'titre' => 'Tarif pr&eacute;-vente',
			'descriptif' => 'Valable jusqu\'au 31 janvier 2010 seulement',
			'prix_ht' => '150.00',
			'tva' => '0',
			'statut' => 'publie'
			),
		array(
			'id_article' => 82,
			'titre' => 'Tarif r&eacute;servation &agrave; l\'avance',
			'descriptif' => 'Valable du 1er f&eacute;vrier au 30 mars 2010',
			'prix_ht' => '165.00',
			'tva' => '0',
			'statut' => 'prepa'
			),
		array(
			'id_article' => 82,
			'titre' => 'Tarif normal',
			'descriptif' => 'Valable jusqu\'au 31 mai 2010',
			'prix_ht' => '185.00',
			'tva' => '0',
			'statut' => 'prepa'
			),
		)
	);
  
	sql_insertq_multi('spip_options', array(
		array(
			'id_option' => 1,
			'titre' => 'Suppl&eacute;ment chambre single semaine',
			'statut' => 'publie',
			'prix_ht' => '98.00',
			'tva' => 0
			),
		array(
			'id_option' => 2,
			'titre' => 'Suppl&eacute;ment All inclusive semaine',
			'statut' => 'publie',
			'prix_ht' => '119.00',
			'tva' => 0,
			),
		array(
			'id_option' => 3,
			'titre' => 'Suppl&eacute;ment chambre single week-end',
			'statut' => 'publie',
			'prix_ht' => '56.00',
			'tva' => 0,
			),
		array(
			'id_option' => 4,
			'titre' => 'Suppl&eacute;ment All inclusive week-end',
			'statut' => 'publie',
			'prix_ht' => '68.00',
			'tva' => 0,
			),
		)
	);
	
	sql_insertq_multi('spip_options_articles', array(
		array(
			'id_option' => 1,
			'id_article' => 80,
			),
		array(
			'id_option' => 2,
			'id_article' => 80,
			),
		array(
			'id_option' => 3,
			'id_article' => 81,
			),
		array(
			'id_option' => 4,
			'id_article' => 81,
			),
		)
	);

}
?>