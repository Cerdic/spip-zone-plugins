<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Agessa
 *
 * @plugin     Agessa
 * @copyright  2016
 * @licence    GNU/GPL
 * @package    SPIP\Agessa\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'installation et de mise à jour du plugin Contact Agessa
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function agessa_upgrade($nom_meta_base_version,$version_cible) {
	$maj = array();
	$maj['create'] = array(
		array('agessa_creer_repertoire'),
	);
	include_spip('base/upgrade'); 	
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Contact Agessa
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function agessa_vider_tables($nom_meta_base_version) {
}


/**
 * Fonction de création du répertoire pour l'upload des fichiers upload
 *
 * @return void
**/
function agessa_creer_repertoire() {
	include_spip('inc/documents');  
	$f = creer_repertoire_documents("pdf_agessa");   
}