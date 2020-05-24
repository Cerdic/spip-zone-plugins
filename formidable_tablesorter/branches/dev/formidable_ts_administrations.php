<?php

/**
 * Fichier gérant l'installation et désinstallation du plugin
 *
 * @package SPIP\Formidable_ts\Installation
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj des config de formidable_ts
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function formidable_ts_upgrade($nom_meta_base_version, $version_cible) {
	// Création des tables

	$maj = array();
	$maj['create'] = array(
		array('formidable_ts_configurer_crayons'),
	);
	$maj['1'] = array(
		array('formidable_ts_configurer_crayons'),
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Configurer crayons pour pouvoir éditer côté privé formidable_ts
**/
function formidable_ts_configurer_crayons() {
	include_spip('inc/config');
	ecrire_config('crayons/espaceprive', 'on');
	$exec_autorise = lire_config('crayons/exec_autorise');
	if (!$exec_autorise) {
		ecrire_config('crayons/exec_autorise', 'formidable_ts');
	}	elseif ($exec_autorise !== '*' and strpos($exec_autorise, 'formidable_ts') === false) {
		ecrire_config('crayons/exec_autorise', $exec_autorise.',formidable_ts');
	}
}

/**
 * Désinstallation/suppression des config de formidable_ts
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 */
function formidable_ts_vider_tables($nom_meta_base_version) {
	// On efface la version enregistrée
	effacer_meta($nom_meta_base_version);
}

