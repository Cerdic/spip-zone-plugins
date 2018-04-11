<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Prestations
 *
 * @plugin     Prestations
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Prestations\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Prestations.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function prestations_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_prestations', 'spip_prestations_types', 'spip_prestations_unites')),
		array('prestations_initialiser_unites'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function prestations_initialiser_unites() {
	include_spip('action/editer_objet');
	
	objet_inserer(
		'prestations_unite',
		0,
		array('titre' => _T('prestations_unite:unite_jours'))
	);
	
	objet_inserer(
		'prestations_unite',
		0,
		array('titre' => _T('prestations_unite:unite_heures'))
	);
	
	objet_inserer(
		'prestations_unite',
		0,
		array('titre' => _T('prestations_unite:unite_forfait'))
	);
}

/**
 * Fonction de désinstallation du plugin Prestations.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function prestations_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_prestations');
	sql_drop_table('spip_prestations_types');
	sql_drop_table('spip_prestations_unites');

	# Nettoyer les liens courants (le génie optimiser_base_disparus se chargera de nettoyer toutes les tables de liens)
	sql_delete('spip_documents_liens', sql_in('objet', array('prestation', 'prestations_type', 'prestations_unite')));
	sql_delete('spip_mots_liens', sql_in('objet', array('prestation', 'prestations_type', 'prestations_unite')));
	sql_delete('spip_auteurs_liens', sql_in('objet', array('prestation', 'prestations_type', 'prestations_unite')));
	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('prestation', 'prestations_type', 'prestations_unite')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('prestation', 'prestations_type', 'prestations_unite')));
	sql_delete('spip_forum', sql_in('objet', array('prestation', 'prestations_type', 'prestations_unite')));

	effacer_meta('prestations');
	effacer_meta($nom_meta_base_version);
}
