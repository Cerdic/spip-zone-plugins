<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Disponibilites objets
 *
 * @plugin     Disponibilites objets
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Objets_disponibilites\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Disponibilites objets.
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
function objets_disponibilites_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	# quelques exemples
	# (que vous pouvez supprimer !)
	#
	# $maj['create'] = array(array('creer_base'));
	#
	# include_spip('inc/config')
	# $maj['create'] = array(
	#	array('maj_tables', array('spip_xx', 'spip_xx_liens')),
	#	array('ecrire_config', 'objets_disponibilites', array('exemple' => "Texte de l'exemple"))
	#);
	#
	# $maj['1.1.0']  = array(array('sql_alter','TABLE spip_xx RENAME TO spip_yy'));
	# $maj['1.2.0']  = array(array('sql_alter','TABLE spip_xx DROP COLUMN id_auteur'));
	# $maj['1.3.0']  = array(
	#	array('sql_alter','TABLE spip_xx CHANGE numero numero int(11) default 0 NOT NULL'),
	#	array('sql_alter','TABLE spip_xx CHANGE texte petit_texte mediumtext NOT NULL default \'\''),
	# );
	# ...

	$maj['create'] = array(array('maj_tables', array('spip_disponibilite_dates', 'spip_disponibilite_dates_liens')));
	$maj['1.0.1'] = array(array('maj_tables', array('spip_disponibilite_dates', 'spip_disponibilite_dates_liens')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Disponibilites objets.
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
function objets_disponibilites_vider_tables($nom_meta_base_version) {
	# quelques exemples
	# (que vous pouvez supprimer !)
	# sql_drop_table('spip_xx');
	# sql_drop_table('spip_xx_liens');

	sql_drop_table('spip_disponibilite_dates');
	sql_drop_table('spip_disponibilite_dates_liens');

	# Nettoyer les liens courants (le génie optimiser_base_disparus se chargera de nettoyer toutes les tables de liens)
	sql_delete('spip_documents_liens', sql_in('objet', array('disponibilite_date')));
	sql_delete('spip_mots_liens', sql_in('objet', array('disponibilite_date')));
	sql_delete('spip_auteurs_liens', sql_in('objet', array('disponibilite_date')));
	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('disponibilite_date')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('disponibilite_date')));
	sql_delete('spip_forum', sql_in('objet', array('disponibilite_date')));

	effacer_meta($nom_meta_base_version);
}
