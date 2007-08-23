<?php
	
	$GLOBALS['spipicious_base_version'] = 0.01;
	
	function spipicious_verifier_base(){
		$version_base = $GLOBALS['spipicious_base_version'];
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta']['spipicious_base_version']) )
				|| (($current_version = $GLOBALS['meta']['spipicious_base_version'])!=$version_base)){
			include_spip('base/spipicious');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta('spipicious_base_version',$current_version=$version_base,'non');
				echo "Installation des tables de spip.icio.us";
			}
			ecrire_metas();
		}
	}
	
	function spipicious_vider_tables() {
		include_spip('base/spipicious');
		include_spip('base/abstract_sql');
		spip_query("DROP TABLE spip_spipicious");
		effacer_meta('spipicious_base_version');
		ecrire_metas();
	}
	
	function spipicious_install($action){
		$version_base = $GLOBALS['spipicious_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['spipicious_base_version']) AND ($GLOBALS['meta']['spipicious_base_version']>=$version_base));
				break;
			case 'install':
				spipicious_verifier_base();
				break;
			case 'uninstall':
				spipicious_vider_tables();
				break;
		}
	}	
?>