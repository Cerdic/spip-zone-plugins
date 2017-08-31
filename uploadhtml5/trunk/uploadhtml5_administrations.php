<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Formulaire upload html5
 *
 * @plugin	   Formulaire upload html5
 * @copyright  2014
 * @author	   Phenix
 * @licence	   GNU/GPL
 * @package	   SPIP\Uploadhtml5\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Formulaire upload html5.
 *
 * Vous pouvez :
 *
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL
 *
 * @param string $nom_meta_base_version
 *	   Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *	   Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function uploadhtml5_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$config_defaut = array(
		'max_file_size' => 5, // 5 Mb par défaut
		'max_file' => 0, // Nombre de fichier illimité par défaut
		'resizeQuality' => 80
	);

	// Configuration par défaut de la dropzone
	$maj['create'] = array(
		array('ecrire_meta', 'uploadhtml5', serialize($config_defaut))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Formulaire upload html5.
 *
 * Vous devez :
 *
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin.
 *
 * @param string $nom_meta_base_version
 *	   Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function uploadhtml5_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
