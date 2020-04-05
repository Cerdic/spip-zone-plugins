<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Réservations Bank
 * @copyright  2015-2020
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Reservations_credits\Pipelines
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

function reservation_bank_declarer_tables_objets_sql($tables) {
	//Ajouter un champ id_reservation et paiement_detail à la table transaction
	$tables['spip_transactions']['field']['id_reservation'] = "bigint(21) NOT NULL DEFAULT 0";
	$tables['spip_transactions']['field']['paiement_detail'] = "varchar(255)  DEFAULT '0' NOT NULL";

	//Ajouter un champ montant_paye à la table spip_reservations_details
	$tables['spip_reservations_details']['field']['montant_paye'] = "decimal(15,2) NOT NULL DEFAULT '0.00'";
	$tables['spip_reservations_details']['champs_editables'][] = "montant_paye";

	// Ajouter les nouveax statuts pour réservation événements
	$tables['spip_reservations_details']['statut_textes_instituer']['attente_paye'] = 'reservation_bank:texte_statut_attente_paye';
	$tables['spip_reservations_details']['statut_images']['attente_paye'] = 'puce-reservation-attente-paye-16.png';

	$tables['spip_reservations']['statut_textes_instituer']['attente_paye'] = 'reservation_bank:texte_statut_attente_paye';
	$tables['spip_reservations']['statut_images']['attente_paye'] = 'puce-reservation-attente-paye-16.png';

	$tables['spip_reservations_details']['statut_textes_instituer']['attente_part'] = 'reservation_bank:texte_statut_attente_part';
	$tables['spip_reservations_details']['statut_images']['attente_part'] = 'puce-reservation-attente-part-16.png';

	$tables['spip_reservations']['statut_textes_instituer']['attente_part'] = 'reservation_bank:texte_statut_attente_part';
	$tables['spip_reservations']['statut_images']['attente_part'] = 'puce-reservation-attente-part-16.png';

	return $tables;
}

