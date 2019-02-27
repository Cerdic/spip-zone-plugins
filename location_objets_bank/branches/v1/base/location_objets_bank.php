<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Location d’objets - paiements
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets_bank\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */

	function location_objets_bank_declarer_tables_objets_sql($tables) {
		//Ajouter un champ id_objets_location  à la table transaction
	$tables['spip_transactions']['field']['id_objets_location'] = "bigint(21) NOT NULL DEFAULT 0";

	//Ajouter un champ montant_paye et date_paiement à la table spip_objets_locations
	$tables['spip_objets_locations']['field']['montant_paye'] = "decimal(15,2) NOT NULL DEFAULT '0.00'";
	$tables['spip_objets_locations']['champs_editables'][] = "montant_paye";
	$tables['spip_objets_locations']['field']['date_paiement'] = 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"';
	$tables['spip_objets_locations']['champs_editables'][] = "date_paiement";

	// Ajouter les nouveax statuts les locations
	$tables['spip_objets_locations']['statut_textes_instituer']['paye'] = 'location_objets_bank:texte_statut_paye';
	$tables['spip_objets_locations']['statut_images']['paye'] = 'puce-location-paye.png';

	$tables['spip_objets_locations']['statut_textes_instituer']['partiel'] = 'location_objets_bank:texte_statut_partiel';
	$tables['spip_objets_locations']['statut_images']['partiel'] = 'puce-location-partiel.png';

	$tables['spip_objets_locations_details']['statut_textes_instituer']['paye'] = 'location_objets_bank:texte_statut_paye';
	$tables['spip_objets_locations_details']['statut_images']['paye'] = 'puce-location-paye.png';

	$tables['spip_objets_locations_details']['statut_textes_instituer']['partiel'] = 'location_objets_bank:texte_statut_partiel';
	$tables['spip_objets_locations_details']['statut_images']['partiel'] = 'puce-location-partiel.png';

	return $tables;
}

