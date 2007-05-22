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
		//spip_query("DROP TABLE spip_noisettes");
		//spip_query("DROP TABLE spip_params_noisettes");
		// Suppression des mots clés inutiles
		include_spip('public/interfaces');
		global $tables_jointures;
		$res = spip_query("SELECT id_mot FROM spip_mots WHERE type REGESP '^noisetier-'");
		while ($row=spip_fetch_array($res)) {
			$id_mot = $row['id_mot'];
			foreach($tables_jointures['spip_mots'] as $table) {
				spip_query("DELETE FROM spip_$table WHERE id_mot=$id_mot");
			}
			spip_query("DELETE FROM spip_mots WHERE id_mot=$id_mot");
		}
		//Suppression des groupes de mots clés inutiles
		spip_query("DELETE FROM spip_groupes_mots WHERE titre REGEXP '^noisetier-'");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>