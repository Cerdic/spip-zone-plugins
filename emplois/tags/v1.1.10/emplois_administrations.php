<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Emplois
 *
 * @plugin     Emplois
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Emplois\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Emplois.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function emplois_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_offres', 'spip_cvs')), array('emplois_init_metas'));

	$maj['1.0.1'] = array(
		array('emplois_maj_metas')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Déclarer les valeurs par défaut des métas (configuration)
**/
function emplois_init_metas() {
	// metas pour le formulaire Offres
	ecrire_config("emplois/offres/activer_offres", 'non');
	ecrire_config("emplois/offres/email", 'oui');
	ecrire_config("emplois/offres/telephone", 'non');
	
	ecrire_config("emplois/offres/emetteur", 'non');
	ecrire_config("emplois/offres/texte_offre", 'non');
	ecrire_config("emplois/offres/date_fin", 'non');
	ecrire_config("emplois/offres/offre_pdf", 'non');

	// metas pour le formulaire CVs
	ecrire_config("emplois/cvs/activer_cvs", 'non');
	ecrire_config("emplois/cvs/cv_pdf", 'non');

	// metas pour le formulaire Affichage Public
	ecrire_config("emplois/affichage_public/placeholder", 'non');
	ecrire_config("emplois/affichage_public/class_fiedset_deposant", '');
	ecrire_config("emplois/affichage_public/class_fiedset_description", '');
}

function emplois_maj_metas() {
	ecrire_config("emplois/offres/activer_deposant", 'oui');
}
/**
 * Fonction de désinstallation du plugin Emplois.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function emplois_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_offres");
	sql_drop_table("spip_cvs");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('offre', 'cv')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('offre', 'cv')));
	sql_delete("spip_forum",                 sql_in("objet", array('offre', 'cv')));

	effacer_meta('emplois');
	effacer_meta($nom_meta_base_version);
}