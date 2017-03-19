<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/upgrade');
function maxigos_upgrade($nom_meta_base_version, $version_cible) {
	$maj['create'] = array(
		array(
				'sql_insertq',
				'spip_types_documents', 
				array('titre'=>'SGF', 
							'extension'=>'sgf', 
							'inclus'=>'non', 
							'mime_type'=>'application/x-go-sgf',
							'upload'=>'oui')
			)
		);
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function maxigos_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
