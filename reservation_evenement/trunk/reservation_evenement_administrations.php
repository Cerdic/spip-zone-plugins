<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Réservation Événements
 *
 * @plugin     Réservation Événements
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


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
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function reservation_evenement_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_reservations', 'spip_reservations_details')));
	$maj['1.1.0'] = array(
	   array('sql_alter','TABLE spip_reservations_details CHANGE prix_unitaire_ht prix_ht float NOT NULL DEFAULT 0'),
       array('maj_tables', array('spip_reservations_details'))
       );    
	$maj['1.2.0'] = array( array('maj_tables', array('spip_reservations_details'))); 
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
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function reservation_evenement_vider_tables($nom_meta_base_version) {


	sql_drop_table("spip_reservations");
	sql_drop_table("spip_reservations_details");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('reservation', 'reservations_detail')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('reservation', 'reservations_detail')));
	sql_delete("spip_forum",                 sql_in("objet", array('reservation', 'reservations_detail')));

	effacer_meta($nom_meta_base_version);
}

?>