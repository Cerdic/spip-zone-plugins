<?php

include_spip('inc/indexation');
include_spip('inc/meta');
include_spip('base/indexation_etendue');


	function Recherche_etendue_verifier_base(){
		$version_base = 0.12;
		$current_version = 0.0;
		if (   (isset($GLOBALS['meta']['Recherche_etendue_base_version']) )
				&& (($current_version = $GLOBALS['meta']['Recherche_etendue_base_version'])==$version_base))
			return;
	
		include_spip('base/indexation_etendue');
		if ($current_version==0.0){
			include_spip('base/abstract_sql');
			creer_base();
			spip_query("ALTER IGNORE TABLE spip_index ADD PRIMARY KEY (id_table, id_objet, hash)");
			ecrire_meta('Recherche_etendue_base_version',$current_version=$version_base);
		}
		if ($current_version<0.11){
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('Recherche_etendue_base_version',$current_version=0.11);
		}
		if ($current_version<0.12){
			spip_query("ALTER TABLE spip_index DROP PRIMARY KEY");
			spip_query("ALTER IGNORE TABLE spip_index ADD PRIMARY KEY (id_table, id_objet, hash)");
			spip_query("ALTER TABLE spip_index DROP INDEX id_table");
			ecrire_meta('Recherche_etendue_base_version',$current_version=0.12);
		}
		ecrire_metas();
	}

	function update_index_tables_sql_from_meta(){
		global $table_des_tables;
		Recherche_etendue_verifier_base();

		// mettre a jour le contenu de spip_types_tables en fonction de la meta
		$liste_tables = liste_index_tables();
		spip_query("DELETE FROM spip_types_tables WHERE ".calcul_mysql_in('id_table',implode(",",array_keys($liste_tables)),'NOT'));
		foreach($liste_tables as $id=>$table){
			spip_query("DELETE FROM spip_types_tables WHERE id_table=$id");
			spip_query("INSERT INTO spip_types_tables (id_table,type) VALUES ($id,'$table')");
			if ((substr($table,0,5)=="spip_")&&(isset($table_des_tables[$t=substr($table,5)])))
				spip_query("INSERT INTO spip_types_tables (id_table,type) VALUES ($id,'$t')");
		}

		// on prepare exception des tables (les as pour chaque id)
		$table_objet_vers_id=array();
		foreach($liste_tables as $id=>$table){
			$primary = primary_index_table($table);
			if ($primary)
				$table_objet_vers_id[$primary] = 'id_objet*(id_table='. $id .')';
		}
		ecrire_meta('Recherche_etendue_exceptions',serialize($table_objet_vers_id));
		ecrire_metas();
		//$exceptions_des_tables['index']['table'] = 'id_table';
	}
?>