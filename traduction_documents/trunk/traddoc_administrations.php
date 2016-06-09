<?php
/**
 * Création / suppression des champs dans la bdd
 *
 * @package SPIP\Traddoc\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Upgrade de la base
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function traddoc_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables', array('spip_documents')),
		array('sql_alter', 'TABLE spip_documents ADD INDEX (id_trad)'),
		array('sql_alter', 'TABLE spip_documents ADD INDEX (lang)'),
		array('sql_updateq', 'spip_documents', array('lang' => $GLOBALS['meta']['langue_site'])),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Desinstallation du plugin
 *
 * Suppression de la colonne id_trad uniquement s'il ne reste
 * pas de traduction.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 */
function traddoc_vider_tables($nom_meta_base_version) {
	// supprimer la colonne seulement s'il ne reste pas de traductions
	$il_en_reste = sql_countsel(
		'spip_documents',
		array(
			'id_trad <> ' . sql_quote(0),
			'id_trad <> id_document',
		)
	);
	
	if (!$il_en_reste) {
		sql_alter('TABLE spip_documents DROP lang');
		sql_alter('TABLE spip_documents DROP langue_choisie');
		sql_alter('TABLE spip_documents DROP id_trad');
	}
	
	effacer_meta($nom_meta_base_version);
}
