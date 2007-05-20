<?php

	include_spip('inc/meta');
	function noisetier_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			include_spip('base/noisetier');
			if ($current_version<0.1){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				echo "Noisetier install&eacute;<br/>";
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
			ecrire_metas();
		}
	}

	function noisetier_vider_tables($nom_meta_base_version) {
		spip_query("DROP TABLE spip_noisettes");
		spip_query("DROP TABLE spip_params_noisettes");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>