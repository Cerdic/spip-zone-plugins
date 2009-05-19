<?php


	/**
	 * SPIP-Plans
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('plans_fonctions');
	

	function plans_declarer_tables_interfaces($interface) {
		$interface['table_des_tables']['plans'] = 'plans';
		$interface['table_des_tables']['points'] = 'points';
		$interface['tables_jointures']['spip_plans'][] = 'points';
		$interface['tables_jointures']['spip_points'][] = 'plans';
		$interface['table_date']['plans'] = 'maj';
		$interface['table_des_traitements']['URL_POINT'][] = 'quote_amp(%s)';
		return $interface;
	}


	function plans_declarer_tables_principales($tables_principales) {
		$spip_plans = array(
							"id_plan"		=> "BIGINT(21) NOT NULL",
							"titre"			=> "TEXT NOT NULL",
							"descriptif"	=> "TEXT NOT NULL",
							"maj"			=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
							"statut"		=> "ENUM('hors_ligne','en_ligne') DEFAULT 'hors_ligne' NOT NULL"
						);
		$spip_plans_key = array(
							"PRIMARY KEY" 	=> "id_plan"
						);
		$spip_points = array(
							"id_point"		=> "BIGINT(21) NOT NULL",
							"id_plan"		=> "BIGINT(21) NOT NULL",
							"titre"			=> "TEXT NOT NULL",
							"lien"			=> "VARCHAR(255) NOT NULL",
							"descriptif"	=> "TEXT NOT NULL",
							"abscisse"		=> "INTEGER NOT NULL",
							"ordonnee"		=> "INTEGER NOT NULL",
							"z_index"		=> "BIGINT(21) NOT NULL DEFAULT '0'"
						);
		$spip_points_key = array(
							"PRIMARY KEY" 	=> "id_point"
						);
		$tables_principales['spip_plans'] =
			array('field' => &$spip_plans, 'key' => &$spip_plans_key);
		$tables_principales['spip_points'] =
			array('field' => &$spip_points, 'key' => &$spip_points_key);
		return $tables_principales;
	}


	function plans_declarer_tables_auxiliaires($tables_auxiliaires) {
		$spip_mots_plans = array(
							"id_mot"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
							"id_plan"	=> "BIGINT (21) DEFAULT '0' NOT NULL"
						);
		$spip_mots_plans_key = array(
							"PRIMARY KEY"	=> "id_plan, id_mot",
							"KEY id_mot"	=> "id_mot"
						);
		$tables_auxiliaires['spip_mots_plans'] = 
			array('field' => &$spip_mots_plans, 'key' => &$spip_mots_plans_key);
		return $tables_auxiliaires;
	}


	function plans_install($action) {
		include_spip('inc/plugin');
		$info_plugin = plugin_get_infos(_NOM_PLUGIN_PLAN);
		$version_plugin = $info_plugin['version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['spip_plans_version']) AND ($GLOBALS['meta']['spip_plans_version'] >= $version_plugin));
				break;
			case 'install':
				include_spip('base/create');
				include_spip('base/abstract_sql');
				if (!isset($GLOBALS['meta']['spip_plans_version'])) {
					creer_base();
					ecrire_meta('spip_plans_version', $version_plugin);
					ecrire_metas();
				} else {
					$version_base = $GLOBALS['meta']['spip_plans_version'];
					if ($version_base < 1.1) {
						ecrire_meta('spip_plan_version', $version_base = 1.1);
						ecrire_metas();
					}
					if ($version_base < 1.2) {
						effacer_meta('spip_plan_version');
						ecrire_meta('spip_plans_version', $version_base = 1.2);
						ecrire_metas();
					}
					if ($version_base < 1.3) {
						effacer_meta('spip_plan_version');
						sql_alter("TABLE spip_plans ADD idx ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL;");
						sql_alter("TABLE spip_points ADD idx ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL;");
						ecrire_meta('spip_plans_version', $version_base = 1.3);
						ecrire_metas();
					}
					if ($version_base < 1.4) {
						ecrire_meta('spip_plans_version', $version_base = 1.4);
						ecrire_metas();
					}
					if ($version_base < 2.0) {
						creer_base();
						sql_alter("TABLE spip_plans DROP idx");
						sql_alter("TABLE spip_points DROP idx");
						sql_alter("TABLE spip_points CHANGE date maj DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
						sql_alter("TABLE spip_points CHANGE abcisse abscisse INTEGER NOT NULL");
						sql_alter("TABLE spip_points ADD z_index BIGINT(21) NOT NULL DEFAULT '0' AFTER ordonnee");
						$points = sql_select('DISTINCT(id_plan), id_point', 'spip_points', '', 'id_plan');
						while ($arr = sql_fetch($points)) {
							$point = new point($arr['id_plan'], $arr['id_point']);
							$point->enregistrer_z_index(0); // ordonnera les autres points
						}
						ecrire_meta('spip_plans_version', $version_base = 2.0);
						ecrire_metas();
					}
				}
				break;
			case 'uninstall':
				include_spip('base/abstract_sql');
				$res = sql_select('id_plan', 'spip_plans');
				while ($arr = sql_fetch($res)) {
					$plan = new plan($arr['id_plan']);
					$plan->supprimer();
				}
				sql_drop_table('spip_plans', true);
				sql_drop_table('spip_points', true);
				sql_drop_table('spip_mots_plans', true);
				effacer_meta('spip_plans_version');
				break;
		}
	}


?>