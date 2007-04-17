<?php

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
				// ajout des champs publique/prive à spip_zones_rubriques
				$desc = spip_abstract_showtable("spip_zones_rubriques", '', true);
				if (!isset($desc['field']['publique']))
					spip_query("ALTER TABLE spip_zones_rubriques ADD publique ENUM('non', 'oui') DEFAULT 'oui' NOT NULL AFTER id_rubrique");
				if (!isset($desc['field']['privee']))
					spip_query("ALTER TABLE spip_zones_rubriques ADD privee ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER publique");
				
				echo "AccesRestreint Install<br/>";
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
			if ($current_version<0.21){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				// ajout des champs publique/prive à spip_zones_rubriques
				$desc = spip_abstract_showtable("spip_zones_rubriques", '', true);
				if (!isset($desc['field']['publique']))
					spip_query("ALTER TABLE spip_zones_rubriques ADD publique ENUM('non', 'oui') DEFAULT 'oui' NOT NULL AFTER id_rubrique");
				if (!isset($desc['field']['privee']))
					spip_query("ALTER TABLE spip_zones_rubriques ADD privee ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER publique");
				echo "AccesRestreint@0.21<br />";
				ecrire_meta($nom_meta_base_version,$current_version=0.2,'non');
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