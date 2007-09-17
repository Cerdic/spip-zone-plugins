<?php

// * Acces restreint, plugin pour SPIP * //

if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('inc/meta');
	function AccesRestreint_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			include_spip('base/acces_restreint');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				// ajout des champs publique/privee si pas existants
				$desc = spip_abstract_showtable("spip_zones", '', true);
				if (!isset($desc['field']['publique']))
					spip_query("ALTER TABLE spip_zones ADD publique ENUM('non', 'oui') DEFAULT 'oui' NOT NULL AFTER descriptif");
				if (!isset($desc['field']['privee']))
					spip_query("ALTER TABLE spip_zones ADD privee ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER publique");
				echo "AccesRestreint Install<br/>";
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
			if ($current_version<0.2){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				// ajout des champs publique/privee si pas existants
				$desc = spip_abstract_showtable("spip_zones", '', true);
				if (!isset($desc['field']['publique']))
					spip_query("ALTER TABLE spip_zones ADD publique ENUM('non', 'oui') DEFAULT 'oui' NOT NULL AFTER descriptif");
				if (!isset($desc['field']['privee']))
					spip_query("ALTER TABLE spip_zones ADD privee ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER publique");
				echo "AccesRestreint@0.2<br />";
				ecrire_meta($nom_meta_base_version,$current_version=0.2,'non');
			}
			if ($current_version<0.3	){
				spip_query("ALTER TABLE `zones_auteurs` DROP INDEX `id_zone`");
				spip_query("ALTER TABLE `zones_auteurs` ADD PRIMARY KEY ( `id_zone` , `id_auteur` )");
				spip_query("ALTER TABLE `zones_rubriques` DROP INDEX `id_zone`");
				spip_query("ALTER TABLE `zones_rubriques` ADD PRIMARY KEY ( `id_zone` , `id_rubrique` )");
				echo "AccesRestreint@0.3<br />";
				ecrire_meta($nom_meta_base_version,$current_version=0.3,'non');
			}
			ecrire_metas();
		}
	}
	
	function AccesRestreint_vider_tables($nom_meta_base_version) {
		spip_query("DROP TABLE spip_zones");
		spip_query("DROP TABLE spip_zones_auteurs");
		spip_query("DROP TABLE spip_zones_rubriques");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>