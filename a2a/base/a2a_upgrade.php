<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
include_spip('base/create');

function a2a_upgrade($nom_meta_base_version,$version_cible){
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
		
	if ($current_version=="0.0") {
		include_spip('base/a2a');
		creer_base();
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}
	if (version_compare($current_version,"0.2","<")){
		//modifications de la table spip_articles_lies
		sql_alter("TABLE spip_articles_lies  ADD rang BIGINT( 21 ) NOT NULL");
		ecrire_meta($nom_meta_base_version,$current_version="0.2");
	}
	if (version_compare($current_version,"0.3","<")){
		//modifications de la table spip_articles_lies
		sql_alter("TABLE spip_articles_lies CHANGE rang rang bigint(21) NOT NULL DEFAULT '0'");
		ecrire_meta($nom_meta_base_version,$current_version="0.3");
	}
	if (version_compare($current_version,"0.4","<")){
		//ajout du type de liaison
		maj_tables('spip_articles_lies');
		ecrire_meta($nom_meta_base_version,$current_version="0.4");
	}
		ecrire_metas();
}

function a2a_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_articles_lies");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

?>
