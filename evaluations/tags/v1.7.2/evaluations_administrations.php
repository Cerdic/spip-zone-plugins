<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Évaluations
 *
 * @plugin     Évaluations
 * @copyright  2013
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Evaluations\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Évaluations.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function evaluations_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_evaluations', 'spip_evaluations_liens', 'spip_evaluations_criteres', 'spip_evaluations_critiques', 'spip_evaluations_syntheses')));

	/* renommage et créations de colonnes d'aide */
	$maj['1.1.0'] = array(
		array('sql_alter', 'TABLE spip_evaluations_criteres RENAME COLUMN texte_commentaire aide_commenter'),
		array('sql_alter', 'TABLE spip_evaluations_criteres DROP COLUMN rang'),
		array('maj_tables', 'spip_evaluations_criteres')
	);

	/* création de spip_evaluations_syntheses */
	$maj['1.2.0'] = array(
		array('maj_tables', 'spip_evaluations_syntheses')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Évaluations.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function evaluations_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_evaluations");
	sql_drop_table("spip_evaluations_liens");
	sql_drop_table("spip_evaluations_criteres");
	sql_drop_table("spip_evaluations_critiques");
	sql_drop_table("spip_evaluations_syntheses");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('evaluation', 'evaluations_critere', 'evaluations_critique', 'evaluations_synthese')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('evaluation', 'evaluations_critere', 'evaluations_critique', 'evaluations_synthese')));
	sql_delete("spip_forum",                 sql_in("objet", array('evaluation', 'evaluations_critere', 'evaluations_critique', 'evaluations_synthese')));

	effacer_meta($nom_meta_base_version);
}

?>
