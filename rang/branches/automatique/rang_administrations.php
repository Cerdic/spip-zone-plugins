<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Rang
 *
 * @plugin     Rang
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Rang\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Rang.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function rang_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	
	// Déplacer l'ancienne config
	$maj['1.0.0'] = array(
		array('rang_maj_1_0_0'),
	);
	
	// Transformation de la config en liste tableau normal, pas à virgule
	$maj['1.0.1'] = array(
		array('rang_maj_1_0_1'),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Maj 1.0.0 : déplacer l'ancienne config
 **/
function rang_maj_1_0_0() {
	include_spip('inc/config');
	
	if ($objets = lire_config('rang_objets')) {
		ecrire_config('rang/rang_objets', $objets);
		effacer_config('rang_objets');
	}
}

/**
 * Maj 1.0.1 : pas de config à virgule alors qu'on sait très bien stocker des listes et tableaux
 **/
function rang_maj_1_0_1() {
	include_spip('inc/config');
	
	if (
		$config_actuelle = lire_config('rang/rang_objets')
		and is_string($config_actuelle)
	) {
		// On transforme en tableau liste
		$config_nouvelle = explode(',', $config_actuelle);
		$config_nouvelle = array_map('trim', $config_nouvelle);
		$config_nouvelle = array_filter($config_nouvelle);
		
		// On enregistre
		ecrire_config('rang/objets', $config_nouvelle);
		effacer_config('rang/rang_objets');
	}
}

/**
 * Fonction de désinstallation du plugin Rang.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function rang_vider_tables($nom_meta_base_version) {
	include_spip('inc/rang_api');

	// supprimer les champs 'rang'
	// note : ici que faire si un objet a ete selectionne, puis deselectionne dans la config ?
	$objets = lire_config('rang/objets');
	foreach ($objets as $value) {
		$champs_table = sql_showtable($value);
		if (isset($champs_table['field']['rang'])) {
			sql_alter("TABLE $value DROP rang");
		}
	}

	// Effacer les metas
	effacer_meta('rang');
	effacer_meta($nom_meta_base_version);
}
