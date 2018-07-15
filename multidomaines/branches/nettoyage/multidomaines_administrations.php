<?php
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip('inc/cextras');
include_spip('base/multidomaines');

function multidomaines_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	
	$maj['0.1.0'] = array(
		array('multidomaines_maj_0_1_0'),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

}

function multidomaines_maj_0_1_0() {
	include_spip('inc/config');
	
	$config = lire_config('multidomaines');
	$nouvelle_config = array();
	
	if (isset($config['editer_url'])) {
		$nouvelle_config['defaut']['url'] = $config['editer_url'];
		unset($config['editer_url']);
	}
	if (isset($config['squelette'])) {
		$nouvelle_config['defaut']['squelette'] = $config['squelette'];
		unset($config['squelette']);
	}
	
	foreach ($config as $champ => $valeur) {
		if ($valeur and strpos($champ, 'editer_url_') === 0) {
			$id_rubrique = explode('_', $champ);
			$id_rubrique = array_pop($id_rubrique);
			$nouvelle_config[$id_rubrique]['url'] = $valeur;
		}
		elseif ($valeur and strpos($champ, 'squelette_') === 0) {
			$id_rubrique = explode('_', $champ);
			$id_rubrique = array_pop($id_rubrique);
			$nouvelle_config[$id_rubrique]['squelette'] = $valeur;
		}
	}
	
	effacer_meta('multidomaines');
	ecrire_config('multidomaines', $nouvelle_config);
}

function multidomaines_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
