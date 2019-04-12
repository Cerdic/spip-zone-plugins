<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Location d&#039;immeubles
 *
 * @plugin     Location d&#039;immeubles
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_immeubles\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('base/location_immeubles');
include_spip('inc/cextras');

/**
 * Fonction d'installation et de mise à jour du plugin Location d&#039;immeubles.
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
function location_immeubles_upgrade($nom_meta_base_version, $version_cible) {
	include_spip('inc/config');

	$maj = array();
	$version_actuelle = lire_config($nom_meta_base_version, 0);

	// Définir les liaisons souhaités
	if (spip_version_compare($version_actuelle,'1.0.3', '<=')) {
		$config_prix_objets = lire_config('prix_objets/objets_prix', array());
		$config_objets_espaces = lire_config('objets_espaces/objets', array());
		$config_objets_infos_extras = lire_config('objets_infos_extras/objets', array());
		$config_objets_services_extras = lire_config('objets_services_extras/objets', array());
		$config_objets_disponibilites = lire_config('objets_disponibilites/objets', array());

		$config_prix_objets = array_merge(
				$config_prix_objets, array('objets_service', 'espace')
			);
		$config_objets_espaces = array_merge(
				$config_objets_services_extras, array('spip_immeubles')
			);
		$config_objets_infos_extras = array_merge(
				$config_objets_infos_extras, array('spip_immeubles', 'spip_espaces')
			);
		$config_objets_services_extras = array_merge(
				$config_objets_services_extras, array('spip_immeubles', 'spip_espaces')
			);
		$config_objets_disponibilites = array_merge(
				$config_objets_disponibilites, array('spip_espaces')
			);
		$config_location_objets = lire_config('config_location_objets', array());
		$config_location_objets = array_merge(
			$config_location_objets, array(
					'location_extras_objets' => array('spip_objets_services'),
					'statut_defaut' => 'attente',
					'activer' => 'on',
					'quand' => array('attente', 'partiel', 'accepte', 'paye', 'erreur'),
					'expediteur' => 'facteur',
					'vendeur' => 'webmaster',
					'vendeur_webmaster' => array(1),
					'client' => 'on',
				)
			);
	}

	$maj['create'] = array(
		array('ecrire_config', 'prix_objets', array('objets_prix' => $config_prix_objets)),
		array('ecrire_config', 'objets_espaces', array('objets' => $config_objets_espaces)),
		array('ecrire_config', 'objets_infos_extras', array('objets' => $config_objets_infos_extras)),
		array('ecrire_config', 'objets_services_extras', array('objets' => $config_objets_services_extras)),
		array('ecrire_config', 'objets_disponibilites', array('objets' => $config_objets_disponibilites)),
		array('ecrire_config', 'location_objets', $config_location_objets),
		array('ecrire_config', 'accepter_inscriptions', 'oui'),
		array('ecrire_config', 'accepter_visiteurs', 'oui'),
	);

	$maj['1.0.3'] = array(
		array('ecrire_config', 'location_objets', $config_location_objets),
		array('ecrire_config', 'accepter_inscriptions', 'oui'),
		array('ecrire_config', 'accepter_visiteurs', 'oui'),
	);

	cextras_api_upgrade(location_immeubles_declarer_champs_extras(), $maj['1.0.4']);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Location d&#039;immeubles.
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
function location_immeubles_vider_tables($nom_meta_base_version) {
	cextras_api_vider_tables(location_immeubles_declarer_champs_extras());
	effacer_meta($nom_meta_base_version);
}
