<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/meta');

// Installation et mise à jour
function noizetier_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables',array('spip_noisettes')),
	);

	$maj['0.2.0'] = array(
		array('maj_tables',array('spip_noisettes')),
	);

	$maj['0.3.0'] = array(
		array('sql_alter','TABLE spip_noisettes DROP COLUMN contexte'),
	);

	$maj['0.4.0'] = array(
		array('maj_tables',array('spip_noisettes')),
	);
	
	$maj['0.5.0'] = array(
		array('maj_tables',array('spip_noisettes')),
		array('sql_alter', 'TABLE spip_noisettes ADD INDEX (type(255))'),
		array('sql_alter', 'TABLE spip_noisettes ADD INDEX (composition(255))'),
		array('sql_alter', 'TABLE spip_noisettes ADD INDEX (bloc(255))'),
		array('sql_alter', 'TABLE spip_noisettes ADD INDEX (noisette(255))'),
		array('sql_alter', 'TABLE spip_noisettes ADD INDEX (objet)'),
		array('sql_alter', 'TABLE spip_noisettes ADD INDEX (id_objet)'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

// Désinstallation
function noizetier_vider_tables($nom_meta_version_base) {
	// On efface les tables du plugin
	sql_drop_table('spip_noisettes');
	// On efface la version enregistrée
	effacer_meta($nom_meta_version_base);
	// On efface les compositions enregistrées
	effacer_meta('noizetier_compositions');
	// Effacer les fichiers du cache créés par le noizetier
	include_spip('inc/flock');
	include_spip('noizetier_fonctions');
	supprimer_fichier(_DIR_CACHE._CACHE_AJAX_NOISETTES);
	supprimer_fichier(_DIR_CACHE._CACHE_CONTEXTE_NOISETTES);
	supprimer_fichier(_DIR_CACHE._CACHE_INCLUSIONS_NOISETTES);
	supprimer_fichier(_DIR_CACHE._CACHE_DESCRIPTIONS_NOISETTES);
}
