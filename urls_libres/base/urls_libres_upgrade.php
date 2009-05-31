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
// ca ne peut pas se faire depuis le prive ...
//				urls_libres_insert();
				ecrire_meta($nom_meta_base_version, $current_version = $version_cible, 'non');
			}
			ecrire_metas();
		}
		spip_log('install urls libres: ' . $version_cible);
	}
/*	
	function urls_libres_insert() {
//		include dirname(dirname(__FILE__)) . '/urls/urls_libres_generer.php';
		foreach (array(
				'article' => 'select id_article from spip_articles'
			) as $objet => $query) {
			$fun = 'generer_url_' . $objet;
			$result = spip_query($query);
			while ($row = spip_fetch_array($result, SPIP_NUM)) {
				spip_log($url=$fun($row[0]));
			}
		}
	}
*/	
	function urls_libres_vider_tables($nom_meta_base_version) {
		spip_log('uninstall urls libres: ' . $GLOBALS['meta'][$nom_meta_base_version]);
		include_spip('base/urls_libres_serial');
		include_spip('base/abstract_sql');
		spip_query("DROP TABLE spip_urls");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>
