<?php
	include_spip('base/create');
	
	function geographie_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			if ($current_version==0.0){
				if (include_spip('base/geographie')){
					creer_base();
					echo "Geographie Install<br/>";
					$importer_geographie = charger_fonction('geographie','imports');
					$importer_geographie();
					ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
				}
				else return;
			}
			ecrire_metas();
		}
	}
	
	function geographie_vider_tables($nom_meta_base_version) {
		spip_query("DROP TABLE spip_geo_pays");
		spip_query("DROP TABLE spip_geo_regions");
		spip_query("DROP TABLE spip_geo_departements");
		spip_query("DROP TABLE spip_geo_communes");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>