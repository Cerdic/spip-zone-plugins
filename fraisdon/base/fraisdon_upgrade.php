<?php
	$GLOBALS['fraisdon_base_version'] = 1.00;

	function fraisdon_upgrade_vers($version, $version_installee, $version_cible = 0){
		return ($version_installee<$version
			AND (($version_cible>=$version) OR ($version_cible==0))
		);
	}

	function fraisdon_verifier_base(){
		$fraisdon_version = $GLOBALS['fraisdon_base_version'];
		$version_installee = 0.0;
		if ( (!isset($GLOBALS['meta']['fraisdon_base_version']) )
		|| (($version_installee = $GLOBALS['meta']['fraisdon_base_version'])!=$fraisdon_version)){
			spip_log("FRAISDON : mise � jour version install�e = $version_installee / $fraisdon_version");
			// modification de la base
			include_spip('base/fraisdon_fraisdons');
			if ($version_installee==0.0){
				// 1�re installation
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta('fraisdon_base_version',$version_installee=$fraisdon_version,'non');
				return;
			}
			if (fraisdon_upgrade_vers(1.00, $version_installee, $fraisdon_version)){
				spip_log("FRAISDON : mise � jour fraisdons/affectation version install�e = $fraisdon_version");
				// spip_query("ALTER TABLE spip_fraisdons ADD `affectation` VARCHAR(16) DEFAULT '' NOT NULL AFTER `fraisdon`");
			}
			spip_log("FRAISDON : inscrire version install�e = $fraisdon_version");
			ecrire_meta('fraisdon_base_version',$version_installee=$fraisdon_version,'non');
		}

	}
	
	function fraisdon_vider_tables() {
		include_spip('base/fraisdon_fraisdons');
		include_spip('base/abstract_sql');
		spip_query("DROP TABLE spip_fraisdons");
		effacer_meta('fraisdon_base_version');
		ecrire_metas();
	}
	
	function fraisdon_install($action){
		$fraisdon_version = $GLOBALS['fraisdon_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['fraisdon_base_version']) AND ($GLOBALS['meta']['fraisdon_base_version']>=$fraisdon_version));
				break;
			case 'install':
				fraisdon_verifier_base();
				break;
			case 'uninstall':
				fraisdon_vider_tables();
				break;
		}
	}	

?>
