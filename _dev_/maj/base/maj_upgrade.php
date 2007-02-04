<?php
	
	include_spip('inc/meta');
	function maj_upgrade($nom_meta_base_version, $version_cible){
		$current_version = 0.0;
		if (	!isset($GLOBALS['meta'][$nom_meta_base_version]) OR
			(($current_version = $GLOBALS['meta'][$nom_meta_base_version]) != $version_cible)
		) {
			include_spip('base/maj_serial');
			if ($current_version < 0.1) {
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta($nom_meta_base_version, $current_version = $version_cible, 'non');
			}
			if ($current_version < 0.2){
				spip_query("ALTER TABLE spip_paquets ADD `date_reference` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL AFTER date_verif");
				spip_query("ALTER TABLE spip_paquets ADD `id_auteur` bigint(21) DEFAULT NULL");
				spip_query("ALTER TABLE spip_paquets ADD `revision` VARCHAR(255)");
				spip_query("ALTER TABLE spip_paquets ADD `user` VARCHAR(255)");
				spip_query("ALTER TABLE spip_paquets ADD `methode` VARCHAR(255)");
				ecrire_meta($nom_meta_base_version, $current_version = 0.2, 'non');
			}
			if ($current_version < 0.3){
				spip_query("ALTER TABLE spip_paquets ADD `categorie` VARCHAR(255)");
				ecrire_meta($nom_meta_base_version, $current_version = 0.3, 'non');
			}
			ecrire_metas();
		}
	}
	
	function maj_vider_tables($nom_meta_base_version) {
		include_spip('base/maj_serial');
		include_spip('base/abstract_sql');
		spip_query("DROP TABLE spip_paquets");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>