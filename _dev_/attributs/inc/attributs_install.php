<?php

	include_spip('inc/meta');
	function attributs_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			include_spip('base/attributs');
			if ($current_version<0.1){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
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
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>