<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Réservations Bank
 *
 * @plugin     Réservations Bank
 * @copyright  2015-2020
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_bank\Installation
 */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Fonction d'installation et de mise à jour du plugin Réservations Bank.
 *
 * Vous pouvez :
 *
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 **/
function reservation_bank_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'] = array(
		array(
			'maj_tables',
			array('spip_transactions','spip_reservations_details')
		),
		array(
			'sql_alter',
			'TABLE spip_transactions ADD INDEX `id_reservation` (`id_reservation`)'
		)
	);
	$maj['1.1.0']  = array(
		array('sql_alter','TABLE spip_reservations_details CHANGE montant_paye montant_paye decimal(15,2) NOT NULL DEFAULT "0.00"'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin Réservations Bank.
 *
 * Vous devez :
 *
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 **/
function reservation_bank_vider_tables($nom_meta_base_version) {

	effacer_meta($nom_meta_base_version);
}
?>