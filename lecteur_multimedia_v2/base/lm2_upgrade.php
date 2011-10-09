<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function lm2_upgrade($nom_meta_base_version,$version_cible){
	$current_version = "0.0";

	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];

	if ($current_version=="0.0") {
		// players par defaut
		ecrire_meta(
			'lecteur_multimedia',
				serialize(array(
					'lecteur_audio' => 'neolao_audio',
					'lecteur_video' => 'neolao_video'
				))
			);
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}

}

function lm2_vider_tables($nom_meta_base_version) {
	effacer_meta('lecteur_multimedia');
	effacer_meta($nom_meta_base_version);
}


?>