<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */

function catalogue_peupler_base() {
	// Attention, spécifier le NOM des TABLES et non le nom des BOUCLES !
	sql_insertq_multi('cat_familles', array(
		array(
			'titre' => ('Conf&eacute;rences'),
			'id_parent' => 0
			),
		array(
			'titre' => ('Tunisia Salsa Congress 2010'),
			'id_parent' => 0
			)
		)
	);
	sql_insertq_multi('cat_produits', array(
		array(
			'id_produit' => 1,
			'titre' => 'New Frontiers of Antitrust auto'
			),
		array(
			'id_produit' => 2,
			'titre' => 'Formule voyage + s&eacute;jour semaine + congr&egrave;s'
			),
		array(
			'id_produit' => 3,
			'titre' => 'Formule voyage + s&eacute;jour weekend + congr&egrave;s'
			),
		array(
			'id_produit' => 4,
			'titre' => 'Formule Full-Pass congr&egrave;s'
			)
		)
	);
	sql_insertq_multi('cat_variantes', array(
		array(
			'id_produit' => 1,
			'titre' => 'Standard price',
			'descriptif' => '',
			'prix_ht' => '850.00'
			),
		array(
			'id_produit' => 1,
			'titre' => 'Early Bird',
			'descriptif' => '',
			'prix_ht' => '750.00'
			),
		array(
			'id_produit' => 1,
			'titre' => 'Concurrences subscribers',
			'descriptif' => '',
			'prix_ht' => '680.00'
			),
		array(
			'id_produit' => 1,
			'titre' => 'Professor',
			'descriptif' => '',
			'prix_ht' => '380.00'
			),
		array(
			'id_produit' => 1,
			'titre' => 'Student',
			'descriptif' => '',
			'prix_ht' => '250.00'
			),
		array(
			'id_produit' => 2,
			'titre' => 'Tarif pr&eacute;-vente',
			'descriptif' => 'Valable jusqu\'au 31 janvier 2010 seulement',
			'prix_ht' => '790.00'
			),
		array(
			'id_produit' => 2,
			'titre' => 'Tarif r&eacute;servation &agrave; l\'avance',
			'descriptif' => 'Valable du 1er f&eacute;vrier au 30 mars 2010',
			'prix_ht' => '860.00'
			),
		array(
			'id_produit' => 2,
			'titre' => 'Tarif normal',
			'descriptif' => 'Valable jusqu\'au 31 mai 2010',
			'prix_ht' => '890.00'
			),
		array(
			'id_produit' => 3,
			'titre' => 'Tarif pr&eacute;-vente',
			'descriptif' => 'Valable jusqu\'au 31 janvier 2010 seulement',
			'prix_ht' => '570.00'
			),
		array(
			'id_produit' => 3,
			'titre' => 'Tarif r&eacute;servation &agrave; l\'avance',
			'descriptif' => 'Valable du 1er f&eacute;vrier au 30 mars 2010',
			'prix_ht' => '635.00'
			),
		array(
			'id_produit' => 3,
			'titre' => 'Tarif normal',
			'descriptif' => 'Valable jusqu\'au 31 mai 2010',
			'prix_ht' => '670.00'
			),
		array(
			'id_produit' => 4,
			'titre' => 'Tarif pr&eacute;-vente',
			'descriptif' => 'Valable jusqu\'au 31 janvier 2010 seulement',
			'prix_ht' => '150.00'
			),
		array(
			'id_produit' => 4,
			'titre' => 'Tarif r&eacute;servation &agrave; l\'avance',
			'descriptif' => 'Valable du 1er f&eacute;vrier au 30 mars 2010',
			'prix_ht' => '165.00'
			),
		array(
			'id_produit' => 4,
			'titre' => 'Tarif normal',
			'descriptif' => 'Valable jusqu\'au 31 mai 2010',
			'prix_ht' => '185.00'
			)
		)
	);
}
?>