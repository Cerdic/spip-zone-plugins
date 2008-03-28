<?php

/*
 * Photospip
 * Un Photoshop-light dans spip?
 *
 * Auteurs :
 * Quentin Drouet
 *
 * © 2008 - Distribue sous licence GNU/GPL
 *
 */
$GLOBALS['photospip_base_version'] = 0.03;
	
	function photospip_upgrade(){
		$version_base = $GLOBALS['photospip_base_version'];
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta']['photospip_base_version']) )
				|| (($current_version = $GLOBALS['meta']['photospip_base_version'])!=$version_base)){
			include_spip('base/photospip');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta('photospip_base_version',$current_version=$version_base,'non');
				echo "Installation des tables de 'modifier images'";
			}
			if ($current_version<0.02){
				spip_query("ALTER TABLE spip_documents_inters  ADD `filtre` text AFTER `version` ");
				echo "Modifier Images upgrade @ 0.02";
				ecrire_meta('photospip_base_version',$current_version=0.02);
			}
			if ($current_version<0.03){
				spip_query("ALTER TABLE spip_documents_inters  ADD `param` text AFTER `filtre` ");
				echo "Modifier Images upgrade @ ".$version_base;
				ecrire_meta('photospip_base_version',$current_version=0.03);
			}
			ecrire_metas();
		}
	}
	
	function photospip_vider_tables() {
		include_spip('base/photospip');
		include_spip('base/abstract_sql');
		spip_query("DROP TABLE spip_documents_inters");
		effacer_meta('photospip_base_version');
		ecrire_metas();
	}
	
	function photospip_install($action){
		global $photospip_base_version;
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['photospip_base_version']) AND ($GLOBALS['meta']['photospip_base_version']>=$photospip_base_version));
				break;
			case 'install':
				photospip_upgrade();
				break;
			case 'uninstall':
				photospip_vider_tables();
				break;
		}
	}	
?>