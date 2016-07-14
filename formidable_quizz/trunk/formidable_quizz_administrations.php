<?php

/**
 * Fichier gérant l'installation et désinstallation du plugin
 *
 * @package SPIP\Formidable\Quizz\Installation
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation/maj des champs des quizz...
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function formidable_quizz_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	
	// On ajoute les champs nécessaires aux quizz
	$maj['create'] = array(
		array('maj_tables', array('spip_formulaires_reponses', 'spip_formulaires_reponses_champs'))
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Désinstallation/suppression des champs des quizz
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 */
function formidable_quizz_vider_tables($nom_meta_base_version) {
	// On efface les champs des quizz
	sql_alter('TABLE spip_formulaires_reponses DROP quizz_score');
	sql_alter('TABLE spip_formulaires_reponses DROP quizz_total');
	sql_alter('TABLE spip_formulaires_reponses_champs DROP quizz_score');

	// On efface la version entregistrée
	effacer_meta($nom_meta_base_version);
}
