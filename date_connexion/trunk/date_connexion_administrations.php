<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Date de connexion
 *
 * @plugin     Date de connexion
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Date_connexion\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Date de connexion.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function date_connexion_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_auteurs')),
		array('date_connexion_maj_dates'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Date de connexion.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function date_connexion_vider_tables($nom_meta_base_version) {
	sql_alter('TABLE spip_auteurs DROP date_connexion');
	sql_alter('TABLE spip_auteurs DROP date_connexion_precedente');
	sql_alter('TABLE spip_auteurs DROP date_suivi_activite');
	effacer_meta($nom_meta_base_version);
}


/**
 * Crée les valeurs par défaut des champs dates créées
 */
function date_connexion_maj_dates() {
	sql_update(
		'spip_auteurs',
		array(
			'date_connexion' => 'maj',
			'date_connexion_precedente' => 'maj',
			'date_suivi_activite' => 'maj',
			'maj' => 'maj' // on évite une mise à jour de 'maj' !
		),
		array(
			'date_connexion = ' . sql_quote('0000-00-00 00:00:00')
		)
	);
}