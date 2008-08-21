<?php
	
	$GLOBALS['spipmotion_base_version'] = 0.2;
	function spipmotion_verifier_base(){
		$version_base = $GLOBALS['spipmotion_base_version'];
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta']['spipmotion_base_version']) )
				|| (($current_version = $GLOBALS['meta']['spipmotion_base_version'])!=$version_base)){
			include_spip('base/spipmotion');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				echo 'Cr&eacute;ation de la base de spipmotion';
				ecrire_meta('spipmotion_base_version',$current_version=$version_base);
			}
			if ($current_version<0.2){
				sql_alter("TABLE spip_spipmotion_attentes ADD `id_auteur` BIGINT(21) NOT NULL DEFAULT '0' AFTER `id_article`");
				sql_alter("TABLE spip_spipmotion_attentes ADD INDEX ( `id_auteur` )");
				ecrire_meta('spipmotion_base_version',$current_version=0.2);
				echo 'Mise &agrave; jour de la base de spipmotion en 0.2';
			}
			ecrire_metas();
		}
	}
	
	function spipmotion_vider_tables() {
		include_spip('base/spipmotion');
		include_spip('base/abstract_sql');
		sql_drop_table("spip_spipmotion_attentes");
		effacer_meta('spipmotion_base_version');
	}
	
	function spipmotion_install($action){
		$version_base = $GLOBALS['spipmotion_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['spipmotion_base_version']) AND ($GLOBALS['meta']['spipmotion_base_version']>=$version_base));
				break;
			case 'install':
				spipmotion_verifier_base();
				break;
			case 'uninstall':
				spipmotion_vider_tables();
				break;
		}
	}	
?>