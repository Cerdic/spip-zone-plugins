<?php


	/**
	 * SPIP-Sondages
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('sondages_fonctions');


	function sondages_declarer_tables_interfaces($interface) {
		$interface['table_des_tables']['sondages'] = 'sondages';
		$interface['table_des_tables']['choix'] = 'choix';
		$interface['table_des_tables']['avis'] = 'avis';
		$interface['table_date']['sondages'] = 'date';
		$interface['tables_jointures']['spip_sondages'][] = 'mots_sondages';
		$interface['tables_jointures']['spip_sondages'][] = 'mots';
		$interface['tables_jointures']['spip_sondages'][] = 'rubriques';
		$interface['tables_jointures']['spip_sondages'][] = 'choix';
		$interface['tables_jointures']['spip_sondages'][] = 'avis';
		$interface['tables_jointures']['spip_sondages'][] = 'documents_liens';
		$interface['tables_jointures']['spip_choix'][] = 'sondages';
		$interface['tables_jointures']['spip_choix'][] = 'avis';
		$interface['tables_jointures']['spip_avis'][] = 'sondages';
		$interface['tables_jointures']['spip_avis'][] = 'choix';
		$interface['tables_jointures']['spip_mots'][] = 'mots_sondages';
		$interface['tables_jointures']['spip_rubriques'][] = 'sondages';
		$interface['table_des_traitements']['URL_SONDAGE'][] = 'quote_amp(%s)';
		return $interface;
	}


	function sondages_declarer_tables_principales($tables_principales) {
		$spip_sondages = array(
							"id_sondage"		=> "BIGINT(21) NOT NULL",
							"id_rubrique"		=> "BIGINT(21) NOT NULL",
							"id_secteur"		=> "BIGINT(21) NOT NULL",
							"titre"				=> "TEXT NOT NULL",
							"texte"				=> "LONGBLOB NOT NULL",
							"date"				=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
							"lang"				=> "VARCHAR(10) NOT NULL",
							"langue_choisie"	=> "VARCHAR(3) DEFAULT 'non'",
							"maj"				=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
							"statut"			=> "ENUM('prepa','publie','termine') NOT NULL DEFAULT 'prepa'"
						);
		$spip_sondages_key = array(
							"PRIMARY KEY" 	=> "id_sondage"
						);
		$spip_choix = array(
							"id_choix"		=> "BIGINT(21) NOT NULL",
							"id_sondage"	=> "BIGINT(21) NOT NULL",
							"ordre"			=> "BIGINT(21) NOT NULL DEFAULT '0'",
							"titre"			=> "TEXT NOT NULL"
						);
		$spip_choix_key = array(
							"PRIMARY KEY" => "id_choix"
						);
		$spip_avis = array(
							"id_avis"		=> "BIGINT(21) NOT NULL",
							"id_sondage" 	=> "BIGINT(21) NOT NULL",
							"id_choix" 		=> "BIGINT(21) NOT NULL"
						);
		$spip_avis_key = array(
							"PRIMARY KEY" => "id_avis"
						);
		$tables_principales['spip_sondages'] =
			array('field' => &$spip_sondages, 'key' => &$spip_sondages_key);
		$tables_principales['spip_choix'] =
			array('field' => &$spip_choix, 'key' => &$spip_choix_key);
		$tables_principales['spip_avis'] =
			array('field' => &$spip_avis, 'key' => &$spip_avis_key);
		return $tables_principales;
	}


	function sondages_declarer_tables_auxiliaires($tables_auxiliaires) {
		$spip_mots_sondages = array(
							"id_mot"		=> "BIGINT (21) DEFAULT '0' NOT NULL",
							"id_sondage"	=> "BIGINT (21) DEFAULT '0' NOT NULL"
						);
		$spip_mots_sondages_key = array(
							"PRIMARY KEY"	=> "id_sondage, id_mot",
							"KEY id_mot"	=> "id_mot"
						);
		$tables_auxiliaires['spip_mots_sondages'] = 
			array('field' => &$spip_mots_sondages, 'key' => &$spip_mots_sondages_key);
		return $tables_auxiliaires;
	}


	function sondages_install($action){
		include_spip('inc/plugin');
		global $spip_version_branche;
		
		preg_match('#^2.0#',$spip_version_branche) ? $info_plugin_sondages = plugin_get_infos(_NOM_PLUGIN_SONDAGES) :  $info_plugin_sondages = plugins_get_infos_dist(_NOM_PLUGIN_SONDAGES) ;
		$version_plugin = $info_plugin_sondages['version'];
		switch ($action) {
			case 'test':
				return (isset($GLOBALS['meta']['spip_sondages_version']) AND ($GLOBALS['meta']['spip_sondages_version'] >= $version_plugin));
				break;
			case 'install':
				include_spip('base/create');
				include_spip('base/abstract_sql');
				if (!isset($GLOBALS['meta']['spip_sondages_version'])) {
					creer_base();
					ecrire_meta('spip_sondages_version', $version_plugin);
					ecrire_metas();
				} else {
					$version_base = $GLOBALS['meta']['spip_sondages_version'];
					if ($version_base < 1.1) {
						creer_base();
						ecrire_meta('spip_sondages_version', $version_base = 1.1);
						ecrire_metas();
					}
					if ($version_base < 1.2) {
						creer_base();
						maj_tables('spip_avis');
						$res = sql_select('C.id_sondage AS id_sondage, A.id_avis AS id_avis', 'spip_avis AS A INNER JOIN spip_choix AS C ON C.id_choix=A.id_choix');
						while ($arr = sql_fetch($res)) 
							sql_updateq('spip_avis', array('id_sondage' => intval($arr['id_sondage'])), 'id_avis='.intval($arr['id_avis']));
						ecrire_meta('spip_sondages_version', $version_base = 1.2);
						ecrire_metas();
					}
					if ($version_base < 1.3) {
						maj_tables('spip_sondages');
						ecrire_meta('spip_sondages_version', $version_base = 1.3);
						ecrire_metas();
					}
					if ($version_base < 1.4) {
						creer_base();
						ecrire_meta('spip_sondages_version', $version_base = 1.4);
						ecrire_metas();
					}
					if ($version_base < 1.5) {
						$res = sql_select('A.id_avis AS id_avis, S.id_sondage AS id_sondage', 'spip_avis AS A INNER JOIN spip_sondes AS S ON S.id_sonde=A.id_sonde');
						while ($arr = sql_fetch($res))
							sql_updateq('spip_avis', array('id_sondage' => intval($arr['id_sondage'])), 'id_avis='.intval($arr['id_avis']));
						ecrire_meta('spip_sondages_version', $version_base = 1.5);
						ecrire_metas();
					}
					if ($version_base < 1.6) {
						$id_rubrique_publiee = sql_getfetsel('id_rubrique', 'spip_rubriques', 'statut="publie"', 'id_rubrique', '1');
						$res = sql_select('id_sondage', 'spip_sondages', 'id_rubrique=0');
						while ($arr = sql_fetch($res))
							sql_updateq('spip_sondages', array('id_rubrique' => intval($id_rubrique_publiee)), 'id_sondage='.intval($arr['id_sondage']));
						sondages_trig_propager_les_secteurs($dummy);
						sondages_calculer_langues_rubriques($dummy);
						ecrire_meta('spip_sondages_version', $version_base = 1.6);
						ecrire_metas();
					}
					if ($version_base < 1.7) {
						$tous_les_sondages = sql_select('id_sondage', 'spip_sondages');
						global $table_logos;
						$table_logos['id_sondage'] = 'son';
						$chercher_logo = charger_fonction('chercher_logo', 'inc');
						while ($arr = sql_fetch($tous_les_sondages)) {
							$id_sondage = $arr['id_sondage'];
							if ($logo_on = $chercher_logo($id_sondage, 'id_sondage', 'on')) {
								$ancien_nom = $logo_on[0];
								$nouveau_nom = $logo_on[1].'sondageon'.$id_sondage.'.'.$logo_on[3];
								rename($ancien_nom, $nouveau_nom);
							}
							if ($logo_off = $chercher_logo($id_sondage, 'id_sondage', 'off')) {
								$ancien_nom = $logo_off[0];
								$nouveau_nom = $logo_off[1].'sondageoff'.$id_sondage.'.'.$logo_off[3];
								rename($ancien_nom, $nouveau_nom);
							}
						}
						ecrire_meta('spip_sondages_version', $version_base = 1.7);
						ecrire_metas();
					}
					if ($version_base < 1.8) {
						sql_alter("TABLE spip_sondages CHANGE date_debut date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
						sql_alter("TABLE spip_sondages CHANGE statut statut ENUM('en_attente','prepa','publie','termine') NOT NULL DEFAULT 'en_attente'");
						sql_updateq('spip_sondages', array('statut' => 'prepa'), 'en_ligne="non"');
						sql_updateq('spip_sondages', array('statut' => 'publie'), 'en_ligne="oui" AND statut<>"termine"');
						sql_alter("TABLE spip_avis DROP id_sonde");
						sql_alter("TABLE spip_sondages DROP type");
						sql_alter("TABLE spip_sondages DROP en_ligne");
						sql_alter("TABLE spip_sondages CHANGE statut statut ENUM('prepa','publie','termine') NOT NULL DEFAULT 'prepa'");
						maj_tables('spip_sondages');
						sql_drop_table('spip_sondes', true);
						sql_drop_table('spip_auteurs_sondages', true);
						creer_base();
						ecrire_meta('spip_sondages_version', $version_base = 1.8);
						effacer_meta('fond_sondage');
						ecrire_metas();
					}
					if ($version_base < 2.0) {
						// pour faire coïncider avec spip 2
						ecrire_meta('spip_sondages_version', $version_base = 2.0);
						ecrire_metas();
					}
					if ($version_base < 2.1) {
						// modeles
						ecrire_meta('spip_sondages_version', $version_base = 2.1);
						ecrire_metas();
					}
				}
				break;
			case 'uninstall':
				include_spip('base/abstract_sql');
				$res = sql_select('id_sondage', 'spip_sondages');
				while ($arr = sql_fetch($res)) {
					$sondage = new sondage($id_sondage);
					$sondage->supprimer();
				}
				sql_drop_table('spip_sondages', true);
				sql_drop_table('spip_choix', true);
				sql_drop_table('spip_avis', true);
				sql_drop_table('spip_mots_sondages', true);
				effacer_meta('spip_sondages_version');
				effacer_meta('fond_sondage');
				break;
		}
	}


?>