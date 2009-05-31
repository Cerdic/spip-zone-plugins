<?php
/*
 * Open Layers plugin
 * free WMS map layers for SPIP
 *
 * Authors :
 * Horacio Gonz‡lez
 * (c) 2007 - Distributed under GNU/GPL licence
 *
 */
	$GLOBALS['openlayer_version'] = 0.0;
	
	function openlayer_verifier_base(){
		$version_base = $GLOBALS['openlayer_version'];
		$current_version = 0.0;
		if ((!isset($GLOBALS['meta']['openlayer_version']) )
				|| (($current_version = $GLOBALS['meta']['openlayer_version'])!=$version_base)){
			if ($current_version==0.0){
				ecrire_meta('openlayer_version',$current_version=$version_base,'non');
			}
		}
		ecrire_meta('gis_map','openlayer');
	}
	
	function openlayer_vider_tables(){
		ecrire_meta('gis_map','no');
		effacer_meta("openlayer_wms");
		effacer_meta("openlayer_version");
	}

	function openlayer_install($action){
		$version_base = $GLOBALS['openlayer_version'];
		switch ($action){
			case 'test':
				ecrire_meta('gis_map','openlayer');
				return (isset($GLOBALS['meta']['openlayer_version']) 
					AND ($GLOBALS['meta']['openlayer_version']>=$version_base));
				break;
			case 'install':
				openlayer_verifier_base();
				break;
			case 'uninstall':
				openlayer_vider_tables();
				break;
		}
	}
?>