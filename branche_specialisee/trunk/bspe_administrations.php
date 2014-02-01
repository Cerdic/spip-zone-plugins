<?php
/**
 * Ce fichier contient les fonctions de création, de mise à jour et de suppression
 * du schéma de données propres au plugin (tables et configuration).
 *
 * @package SPIP\BSPE\Schema\Installation
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Installation du schéma de données propre au plugin et gestion des migrations suivant
 * les évolutions du schéma.
 *
 * Le schéma comprend des tables et des variables de configuration.
 *
 * @api
 * @see bspe_declarer_tables_interfaces()
 *
 * @param string $nom_meta_base_version
 * 		Nom de la meta dans laquelle sera rangée la version du schéma
 * @param string $version_cible
 * 		Version du schéma de données en fin d'upgrade
 *
 * @return void
 */
function bspe_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();

	// Configuration par défaut à la première activation du plugin
	$maj['create'] = array(
		array('maj_tables', array('spip_branches_specialisees')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Suppression de l'ensemble du schéma de données propre au plugin, c'est-à-dire
 * les tables et les variables de configuration.
 *
 * @api
 *
 * @param string $nom_meta_base_version
 * 		Nom de la meta dans laquelle sera rangée la version du schéma
 *
 * @return void
 */
function bspe_vider_tables($nom_meta_base_version) {
	// on efface ensuite la table et la meta habituelle designant la version du plugin
	sql_drop_table('spip_branches_specialisees');

	// on efface la meta de configuration du plugin
	effacer_meta('bspe');

	// on efface la meta du schéma du plugin
	effacer_meta($nom_meta_base_version);
}

?>
