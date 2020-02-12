<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Réseŕvations Crédits
 *
 * @plugin     Réseŕvations Crédits
 * @copyright  2015-20
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservations_credits\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'installation et de mise à jour du plugin Réseŕvations Crédits.
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
function reservations_credits_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables',
			array(
				'spip_reservation_credit_mouvements',
				'spip_reservation_credits'
			)
		)
	);
	$maj['1.1.0'] = array(
		array(
			'maj_tables',
			array('spip_reservation_credit_mouvements')
		),
		array(
			'sql_alter',
			'TABLE spip_reservation_credit_mouvements ADD INDEX `id_reservation` (`id_reservation`)'
		)
	);

	$maj['1.1.1'] = array(
		array(
			'maj_tables',
			array('spip_reservation_credit_mouvements')
		),
		array(
			'sql_alter',
			'TABLE spip_reservation_credit_mouvements ADD INDEX `id_objet` (`id_objet`)',
			'TABLE spip_reservation_credit_mouvements ADD INDEX `objet` (`objet`)',
		),
	);


	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin Réseŕvations Crédits.
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
function reservations_credits_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_reservation_credit_mouvements");
	sql_drop_table("spip_reservation_credits");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('reservation_credit_mouvement', 'reservation_credit')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('reservation_credit_mouvement', 'reservation_credit')));
	sql_delete("spip_forum",                 sql_in("objet", array('reservation_credit_mouvement', 'reservation_credit')));

	effacer_meta($nom_meta_base_version);
}
