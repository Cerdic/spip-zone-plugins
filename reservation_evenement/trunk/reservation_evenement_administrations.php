<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Réservation Événements
 *
 * @plugin     Réservation Événements
 * @copyright  2013 - 2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Installation
 */
if (! defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Fonction d'installation et de mise à jour du plugin Réservation Événements.
 *
 * Vous pouvez :
 *
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL
 *
 * @param string $nom_meta_base_version
 *        	Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *        	Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 *
 */
function reservation_evenement_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array ();

	if ($version_cible == '1.3.3') {
		include_spip('inc/config');
		$config = lire_config('reservation_evenement');
		if (isset($config['envoi_separe'])) {
			$config['envoi_separe'] = $config['envoi_separe'];
			unset($config['envoi_separe']);
		}
	}

	$maj['create'] = array (
		array (
			'maj_tables',
			array (
				'spip_reservations',
				'spip_reservations_details',
				'spip_articles',
				'spip_evenements'
			)
		)
	);
	$maj['1.1.0'] = array (
		array (
			'sql_alter',
			'TABLE spip_reservations_details CHANGE prix_unitaire_ht prix_ht float NOT NULL DEFAULT 0'
		),
		array (
			'maj_tables',
			array (
				'spip_reservations_details'
			)
		)
	);
	$maj['1.2.0'] = array (
		array (
			'maj_tables',
			array (
				'spip_reservations_details'
			)
		)
	);
	$maj['1.3.1'] = array (
		array (
			'maj_tables',
			array (
				'spip_reservations'
			)
		)
	);
	$maj['1.3.3'] = array (
		array (
			'ecrire_config',
			'reservation_evenement',
			$config
		)
	);
	$maj['1.4.1'] = array (
		array (
			'maj_tables',
			array (
				'spip_articles',
				'spip_evenements'
			)
		)
	);
	include_spip('inc/reservation_evenement_administrations');
	$maj['1.4.2'] = array (
		array (
			'sql_alter',
			'TABLE spip_reservations_details CHANGE quantite quantite int(11) NOT NULL DEFAULT 1'
		),
		array (
			'update_donnees_auteurs'
		)
	);
	$maj['1.5.2'] = array (
		array (
			'maj_tables',
			array (
				'spip_reservations'
			)
		),
		array (
			'sql_alter',
			'TABLE spip_reservations ADD INDEX `id_reservation_source` (`id_reservation_source`)'
		)
	);
	$maj['1.6.0'] = array (
		array (
			'maj_tables',
			array (
				'spip_reservations_details'
			)
		)
	);

	$maj['1.6.1'] = array (
		array (
			'maj_tables',
			array (
				'spip_reservations'
			)
		)
	);

	$maj['1.7.0']  = array(
		array('sql_alter','TABLE spip_reservations_details CHANGE prix_ht prix_ht decimal(15,2) NOT NULL DEFAULT "0.00"'),
		array('sql_alter','TABLE spip_reservations_details CHANGE prix prix decimal(15,2) NOT NULL DEFAULT "0.00"'),
		array('sql_alter','TABLE spip_reservations_details CHANGE taxe taxe decimal(15,2) NOT NULL DEFAULT "0.00"'),
	);

	// Ajouter le champ "destinataires_supplementaires".
	$maj['1.29.0']  = array(
		array (
			'maj_tables',
			array (
				'spip_reservations',
			)
		)
	);

	$maj['1.29.6']  = array(
		array (
			'maj_tables',
			array (
				'spip_reservations_details',
			)
		)
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin Réservation Événements.
 *
 * Vous devez :
 *
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin.
 *
 * @param string $nom_meta_base_version
 *        	Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 *
 */
function reservation_evenement_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_reservations");
	sql_drop_table("spip_reservations_details");

	// Nettoyer les versionnages et forums
	sql_delete("spip_versions", sql_in("objet", array (
		'reservation',
		'reservations_detail'
	)));
	sql_delete("spip_versions_fragments", sql_in("objet", array (
		'reservation',
		'reservations_detail'
	)));
	sql_delete("spip_forum", sql_in("objet", array (
		'reservation',
		'reservations_detail'
	)));

	effacer_meta($nom_meta_base_version);
}
