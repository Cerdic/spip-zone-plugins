<?php


	/**
	 * SPIP-Météo : prévisions météo dans vos squelettes
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
	 

	$GLOBALS['spip_meteo_version'] = 1.3;	
	
	function meteo_verifier_base() {
		$version_base = $GLOBALS['spip_meteo_version'];
		$current_version = 0.0;
		
if (   (!isset($GLOBALS['meta']['spip_meteo_version']) ) || (($current_version = $GLOBALS['meta']['spip_meteo_version'])!=$version_base)){
			include_spip('base/meteo');
			
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('spip_meteo_version',$current_version=$version_base,'non');
		} else {
			$version_base = $GLOBALS['meta']['spip_meteo_version'];
			if ($version_base < 1.1) {
				ecrire_meta('spip_meteo_version',$current_version=1.1,'non');
			}
			if ($version_base < 1.2) {
				sql_alter("TABLE spip_meteo ADD idx ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL;");
				ecrire_meta('spip_meteo_version',$current_version=1.2,'non');
			}
		}
		if (isset($GLOBALS['meta']['INDEX_elements_objet'])){
			$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
			if (!isset($INDEX_elements_objet['spip_meteo'])){
				$INDEX_elements_objet['spip_meteo'] = array('ville'=>20,'code'=>10);
				ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
			}
		}
		return true;
	}
	}
	
	
	
	function meteo_vider_tables() {
		include_spip('base/meteo');
		include_spip('base/abstract_sql');
		sql_drop_table("spip_meteo");
		effacer_meta('spip_meteo_version');
	}
	
	function meteo_install($action){
		$version_base = $GLOBALS['spip_meteo_version'];
		switch ($action){
			/*case 'test':
				return (isset($GLOBALS['meta']['spip_meteo_version']) AND ($GLOBALS['meta']['spip_meteo_version']>=$version_base)
				AND isset($GLOBALS['meta']['INDEX_elements_objet'])
				AND $t = unserialize($GLOBALS['meta']['INDEX_elements_objet'])
				AND isset($t['spip_evenements'])
				AND isset($GLOBALS['meta']['INDEX_objet_associes'])
				AND $t = unserialize($GLOBALS['meta']['INDEX_objet_associes'])
				AND isset($t['spip_articles']['spip_evenements'])
				AND isset($GLOBALS['meta']['INDEX_elements_associes'])
				AND $t = unserialize($GLOBALS['meta']['INDEX_elements_associes'])
				AND isset($t['spip_evenements']));
				break;*/
			case 'install':
				meteo_verifier_base();
				break;
			case 'uninstall':
				meteo_vider_tables();
				break;
		}
	}	


?>