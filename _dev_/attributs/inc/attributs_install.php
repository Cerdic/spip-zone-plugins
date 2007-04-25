<?php

	include_spip('inc/meta');
	function attributs_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			include_spip('base/attributs');
			if ($current_version<1.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				echo "attributs install&eacute;<br/>";
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
			if ($current_version==1.1){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				// ajout des champs mots et groupes_mots si pas existants
				$desc = spip_abstract_showtable("spip_attributs", '', true);
				if (!isset($desc['field']['groupes_mots']))
					spip_query("ALTER TABLE spip_attributs ADD groupes_mots ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER auteurs");
				if (!isset($desc['field']['mots']))
					spip_query("ALTER TABLE spip_attributs ADD mots ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER groupes_mots");
				echo "attributs install&eacute;<br/>";
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
			ecrire_metas();
		}
	}
	
	function attributs_vider_tables($nom_meta_base_version) {
		spip_query("DROP TABLE spip_attributs");
		spip_query("DROP TABLE spip_attributs_articles");
		spip_query("DROP TABLE spip_attributs_rubriques");
		spip_query("DROP TABLE spip_attributs_breves");
		spip_query("DROP TABLE spip_attributs_auteurs");
		spip_query("DROP TABLE spip_attributs_syndic");
		spip_query("DROP TABLE spip_attributs_mots");
		spip_query("DROP TABLE spip_attributs_groupes_mots");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>