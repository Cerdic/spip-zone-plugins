<?php


function stats_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		
		if ($current_version==0.0){
			include_spip('base/stats');
			include_spip('base/create');
			// creer les tables
			creer_base();
			// mettre les metas par defaut
			$config = charger_fonction('config','inc');
			$config();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
		}
	}
}

function stats_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_visites");
	sql_drop_table("spip_visites_articles");
	sql_drop_table("spip_referers");
	sql_drop_table("spip_referers_articles");
	
	effacer_meta("activer_statistiques");
	effacer_meta("activer_captures_referers");

	effacer_meta($nom_meta_base_version);
}
?>
