<?php


	/**
	 * SPIP-Météo
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function meteo_declarer_tables_interfaces($interface) {
		$interface['table_des_tables']['meteo'] = 'meteo';
		$interface['table_des_tables']['previsions'] = 'previsions';
		$interface['table_date']['meteo'] = 'maj';
		$interface['table_date']['previsions'] = 'date';
		return $interface;
	}


	function meteo_declarer_tables_principales($tables_principales) {
		$spip_meteo = array(
							"id_meteo"		=> "BIGINT(21) NOT NULL",
							"ville"			=> "VARCHAR(255) NOT NULL",
							"code"			=> "VARCHAR(255) NOT NULL",
							"maj"			=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
							"statut"		=> "ENUM('publie','en_erreur') DEFAULT 'en_erreur' NOT NULL",
							"idx"			=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL"
						);
		$spip_meteo_key = array(
							"PRIMARY KEY" 	=> "id_meteo"
						);
		$spip_previsions = array(
							"id_prevision"	=> "BIGINT(21) NOT NULL",
							"id_meteo"		=> "BIGINT(21) NOT NULL",
							"date"			=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
							"minima"		=> "VARCHAR(255) DEFAULT '' NOT NULL",
							"maxima"		=> "VARCHAR(255) DEFAULT '' NOT NULL",
							"id_temps"		=> "BIGINT(21) DEFAULT '48' NOT NULL",
							"maj"			=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL"
						);
		$spip_previsions_key = array(
							"PRIMARY KEY" 	=> "id_prevision"
						);
		$tables_principales['spip_meteo'] =
			array('field' => &$spip_meteo, 'key' => &$spip_meteo_key);
		$tables_principales['spip_previsions'] =
			array('field' => &$spip_previsions, 'key' => &$spip_previsions_key);
		return $tables_principales;
	}


	function meteo_install($action){
		include_spip('inc/plugin');
		$info_plugin_boutique = plugin_get_infos(_NOM_PLUGIN_METEO);
		$version_plugin = $info_plugin_boutique['version'];
		switch ($action) {
			case 'test':
				return (isset($GLOBALS['meta']['spip_meteo_version']) AND ($GLOBALS['meta']['spip_meteo_version'] >= $version_plugin));
				break;
			case 'install':
				include_spip('base/create');
				include_spip('base/abstract_sql');
				if (!isset($GLOBALS['meta']['spip_meteo_version'])) {
					creer_base();
					ecrire_meta('spip_meteo_version', $version_plugin);
					ecrire_metas();
				} else {
					$version_base = $GLOBALS['meta']['spip_meteo_version'];
					if ($version_base < 1.1) {
						ecrire_meta('spip_meteo_version', $version_base = 1.1);
						ecrire_metas();
					}
					if ($version_base < 1.2) {
						sql_alter("TABLE spip_meteo ADD idx ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL;");
						ecrire_meta('spip_meteo_version', $version_base = 1.2);
						ecrire_metas();
					}
					if ($version_base < 2.0) {
						// renommage pour spip 2.0
						ecrire_meta('spip_meteo_version', $version_base = 2.0);
						ecrire_metas();
					}
				}
				break;
			case 'uninstall':
				include_spip('base/abstract_sql');
				sql_drop_table('spip_meteo', true);
				sql_drop_table('spip_previsions', true);
				effacer_meta('spip_meteo_version');
				break;
		}
	}


?>