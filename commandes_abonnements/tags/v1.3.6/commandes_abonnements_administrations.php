<?php

/**
 * Fichier gérant l'installation et désinstallation du plugin Commandes d’abonnements
 *
 * @plugin     Commandes d’abonnements
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\CommandesAbonements\Installation
 */
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Commandes d’abonnements
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 * */
function commandes_abonnements_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
	    array('maj_tables', array('spip_abonnements_offres')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin Commandes d’abonnements
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 * */
function commandes_abonnements_vider_tables($nom_meta_base_version) {
	sql_alter('TABLE spip_abonnements_offres DROP renouvellement_auto');
	sql_alter('TABLE spip_abonnements_offres DROP montant_perso');
	sql_alter('TABLE spip_abonnements_offres DROP montant_minimum');

	effacer_meta($nom_meta_base_version);
}
