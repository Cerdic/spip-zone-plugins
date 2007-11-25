<?php

	$GLOBALS['spipicious_base_version'] = 0.2;

	function spipicious_upgrade(){
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
			if($current_version<0.2){
				spip_query("ALTER TABLE `spip_spipicious` ADD PRIMARY KEY (`id_mot`) ");
				spip_query("ALTER TABLE `spip_spipicious` ADD KEY (`id_auteur`) ");	
				spip_query("ALTER TABLE `spip_spipicious` ADD maj timestamp AFTER position ");
				echo "spipicious update @ 0.2<br/>";
				ecrire_meta('spipicious_base_version',$current_version=0.2,'non');
			}
			ecrire_metas();
		}
	}
	
	function spipicious_vider_tables() {
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
				spipicious_upgrade();
				break;
			case 'uninstall':
				spipicious_vider_tables();
				break;
		}
	}
?>