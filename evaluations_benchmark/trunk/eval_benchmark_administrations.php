<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function eval_benchmark_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		// vérifier que la gestion d'un annuaire de sites est active
		array('ecrire_meta', 'activer_sites', 'oui'),
		// vérifie qu'on a bien les urls arborescentes
		array('ecrire_meta', 'type_urls','arbo')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function eval_benchmark_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}

?>
