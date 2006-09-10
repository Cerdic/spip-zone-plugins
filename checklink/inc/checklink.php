<?php

function checklink_install(){
	checklink_verifier_base();
}

function checklink_uninstall(){
	include_spip('base/checklink');
	include_spip('base/abstract_sql');

	// suppression du champ evenements a la table spip_groupe_mots
	//spip_query("ALTER TABLE `spip_groupes_mots` DROP `evenements`");	
}

function checklink_verifier_base(){
	$version_base = 0.10;
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta']['checklink_base_version']) )
			|| (($current_version = $GLOBALS['meta']['checklink_base_version'])!=$version_base)){
		include_spip('base/checklink');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('checklink_base_version',$current_version=$version_base);
		}
		
		ecrire_metas();
	}
	
}


?>