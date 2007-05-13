<?php
	
	include_spip('inc/meta');
	function urls_libres_upgrade($nom_meta_base_version, $version_cible){
		$current_version = isset($GLOBALS['meta'][$nom_meta_base_version]) ?
			$GLOBALS['meta'][$nom_meta_base_version] : 0.0;
		if ($current_version != $version_cible) {
			include_spip('base/urls_libres_serial');
			if ($current_version < 0.1) {
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta($nom_meta_base_version, $current_version = $version_cible, 'non');
			}
			ecrire_metas();
		}
	}
	
	function urls_libres_vider_tables($nom_meta_base_version) {
		include_spip('base/urls_libres_serial');
		include_spip('base/abstract_sql');
		spip_query("DROP TABLE spip_urls");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>
