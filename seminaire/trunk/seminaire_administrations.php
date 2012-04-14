<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


	include_spip('inc/cextras');
	include_spip("inc/lang");
	include_spip('inc/meta');
	include_spip('base/seminaire');

function seminaire_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	cextras_api_upgrade(seminaire_declarer_champs_extras(), $maj['create']);
	
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}

	if ($GLOBALS['meta']['articles_mots'] == 'non') {
		ecrire_meta('articles_mots', 'oui','oui');
		ecrire_metas();
	}
	
	include_spip('base/upgrade');//insertion des champs extra
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}



?>