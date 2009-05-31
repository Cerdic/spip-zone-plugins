<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

	$GLOBALS['agenda_base_version'] = 0.22;
	function agenda_verifier_base(){
		$version_base = $GLOBALS['agenda_base_version'];
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta']['agenda_base_version']) )
				|| (($current_version = $GLOBALS['meta']['agenda_base_version'])!=$version_base)){
			include_spip('base/agenda_evenements');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				maj_tables('spip_rubriques'); 
				ecrire_meta('agenda_base_version',$current_version=$version_base,'non');
			}
			if ($current_version<0.11){
				sql_alter("TABLE spip_evenements ADD `horaire` ENUM('oui','non') DEFAULT 'oui' NOT NULL AFTER `lieu`");
				ecrire_meta('agenda_base_version',$current_version=0.11,'non');
			}
			if ($current_version<0.12){
				sql_alter("TABLE spip_evenements ADD `id_article` bigint(21) DEFAULT '0' NOT NULL AFTER `id_evenement`");
				sql_alter("TABLE spip_evenements ADD INDEX ( `id_article` )");
				$res = sql_select("*", "spip_evenements_articles");
				while ($row = sql_fetch($res)){
					$id_article = $row['id_article'];
					$id_evenement = $row['id_evenement'];
					sql_update("spip_evenements", "id_article=$id_article", "id_evenement=$id_evenement");
				}
				sql_drop_table("spip_evenements_articles");
				ecrire_meta('agenda_base_version',$current_version=0.12,'non');
			}
			if ($current_version<0.13){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta('agenda_base_version',$current_version=0.13,'non');
			}
			if ($current_version<0.18){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				sql_update('spip_groupes_mots',array('tables_liees'=>"concat(tables_liees,'evenements,')"),"evenements='oui'");
				sql_alter("TABLE spip_groupes_mots DROP evenements");
				ecrire_meta('agenda_base_version',$current_version=0.18,'non');
			}
			if ($current_version<0.20){
				include_spip('base/abstract_sql');
				sql_alter("TABLE spip_rubriques ADD agenda tinyint(1) DEFAULT 0 NOT NULL");
				ecrire_meta('agenda_base_version',$current_version=0.20,'non');
			}
			if ($current_version<0.21){
				include_spip('base/abstract_sql');
				sql_alter("TABLE spip_evenements ADD adresse text NOT NULL");
				sql_alter("TABLE spip_evenements ADD inscription text NOT NULL");
				sql_alter("TABLE spip_evenements ADD places text NOT NULL");
				ecrire_meta('agenda_base_version',$current_version=0.21,'non');
			}
			if ($current_version<0.22){
				include_spip('base/abstract_sql');
				include_spip('base/create');
				include_spip('base/auxiliaires');
				maj_tables('spip_evenements_participants');
				#ecrire_meta('agenda_base_version',$current_version=0.22,'non');
			}
		}
	}
	
	function agenda_vider_tables() {
		include_spip('base/agenda_evenements');
		include_spip('base/abstract_sql');
		sql_drop_table("spip_evenements");
		sql_drop_table("spip_mots_evenements");
		sql_alter("TABLE spip_rubriques DROP COLUMN agenda");
		effacer_meta('agenda_base_version');
	}
	
	function agenda_install($action){
		$version_base = $GLOBALS['agenda_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['agenda_base_version']) AND ($GLOBALS['meta']['agenda_base_version']>=$version_base));
				break;
			case 'install':
				agenda_verifier_base();
				break;
			case 'uninstall':
				agenda_vider_tables();
				break;
		}
	}
?>
