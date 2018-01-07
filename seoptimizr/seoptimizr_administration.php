<?php
	if (!defined('_ECRIRE_INC_VERSION')) {
		return;
	}

	include_spip('inc/meta');

	// fonction d'installation, mise a jour de la base
	function seoptimizr_upgrade($nom_meta_base_version, $version_cible) {
		
		$maj = array();
		$maj['create'] = array(
			array('maj_tables', array('spip_seobjets','spip_seobjets_liens')),
		);

		// comme c'est un ajout de colonne, pas besoin d'utiliser un sqal_alter
		// $maj['1.0.1'] = array(
		// 	array('sql_alter',"TABLE spip_seobjet ADD mon_nouveau_champ TEXT NOT NULL DEFAULT ''"),
		// );

		include_spip('base/upgrade');
		maj_plugin($nom_meta_base_version, $version_cible, $maj);
	}

	// fonction de desinstallation
	function seoptimizr_vider_tables($nom_meta_base_version) {
		include_spip('base/abstract_sql');
		include_spip('inc/meta');

		sql_drop_table('spip_seobjets');
		sql_drop_table('spip_seobjets_liens');
		// concernerait à priori les infos generiques, pas utilisées ici
		// effacer_meta('spip_metas_title');
		// effacer_meta('spip_metas_description');
		// effacer_meta('spip_metas_mots_importants');
		// effacer_meta('spip_metas_mots_keywords');
		effacer_meta($nom_meta_base_version);
	}