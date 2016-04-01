<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function videos_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		/* Ajouter les nouvelles extensions en base pour qu'elles soient prises en compte comme des vidéos */
		array('sql_insertq_multi', 'spip_types_documents', array(
			array('extension' => 'dist_daily', 'titre' => 'Dailymotion',   'inclus' => 'embed', 'upload' => 'oui', 'media_defaut' => 'video'),
			array('extension' => 'dist_vimeo', 'titre' => 'Vimeo',         'inclus' => 'embed', 'upload' => 'oui', 'media_defaut' => 'video'),
			array('extension' => 'dist_youtu', 'titre' => 'Youtube',       'inclus' => 'embed', 'upload' => 'oui', 'media_defaut' => 'video'),
			array('extension' => 'dist_cubox', 'titre' => 'CultureBox',    'inclus' => 'embed', 'upload' => 'oui', 'media_defaut' => 'video'),
		)),
	);

	$maj['0.1'] = array(
		/* Mettre à jour la table DOCUMENTS pour regrouper les extensions "distantes" (changement de nomenclature) */
		array('sql_updateq', 'spip_documents', array('extension' => 'dist_daily'), "extension='dailym'"),
		array('sql_updateq', 'spip_documents', array('extension' => 'dist_vimeo'), "extension='vimeo'"),
		array('sql_updateq', 'spip_documents', array('extension' => 'dist_youtu'), "extension='youtube'"),

		/* Ajouter les nouvelles extensions en base pour qu'elles soient prises en compte comme des vidéos */
		array('sql_insertq_multi', 'spip_types_documents', array(
			array('extension' => 'dist_daily', 'titre' => 'Dailymotion',   'inclus' => 'embed', 'upload' => 'oui'),
			array('extension' => 'dist_vimeo', 'titre' => 'Vimeo',         'inclus' => 'embed', 'upload' => 'oui'),
			array('extension' => 'dist_youtu', 'titre' => 'Youtube',       'inclus' => 'embed', 'upload' => 'oui'),
		)),
	);

	$maj['0.2'] = array(
		array('sql_updateq', 'spip_documents', array('extension' => 'dist_cubox'), "extension='culturebox'"),
		array('sql_insertq', 'spip_types_documents', array('extension' => 'dist_cubox', 'titre' => 'CultureBox', 'inclus' => 'embed', 'upload' => 'oui')),
	);

	$maj['0.3'] = array(
		/* 0.1 et 0.2 rataient leurs insertions SI le plugin Mediatheque n'avait pas inséré le champ media, 
		 * on relance donc l'insertion pour ceux qui ont raté le coche */
		array('sql_insertq_multi', 'spip_types_documents', array(
			array('extension' => 'dist_daily', 'titre' => 'Dailymotion',   'inclus' => 'embed', 'upload' => 'oui'),
			array('extension' => 'dist_vimeo', 'titre' => 'Vimeo',         'inclus' => 'embed', 'upload' => 'oui'),
			array('extension' => 'dist_youtu', 'titre' => 'Youtube',       'inclus' => 'embed', 'upload' => 'oui'),
			array('extension' => 'dist_cubox', 'titre' => 'CultureBox',    'inclus' => 'embed', 'upload' => 'oui'),
		)),
	);

	$maj['0.4'] = array(
		/* corriger le champ media_defaut si besoin */
		array('sql_updateq', 'spip_types_documents', array('media_defaut' => 'video'), "extension REGEXP '^dist_'")
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function videos_vider_tables($nom_meta_base_version) {
	sql_delete("spip_types_documents", sql_in("extension", array('dist_daily', 'dist_vimeo', 'dist_youtu', 'dist_cubox')));

	effacer_meta('videos');
	effacer_meta($nom_meta_base_version);
}
