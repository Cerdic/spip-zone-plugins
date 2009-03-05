<?php


	/**
	 * SPIP-Notifications
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function notifications_declarer_tables_interfaces($interface) {
		$interface['table_des_tables']['notifications'] = 'notifications';
		return $interface;
	}


	function notifications_declarer_tables_principales($tables_principales) {
		$spip_notifications = array(
							"id_notification"	=> "BIGINT(21) NOT NULL",
							"notification"		=> "TEXT NOT NULL",
							"titre"				=> "TEXT NOT NULL",
							"descriptif"		=> "TEXT NOT NULL",
							"texte"				=> "LONGBLOB NOT NULL",
							"ps"				=> "MEDIUMTEXT NOT NULL"
						);
		$spip_notifications_key = array(
							"PRIMARY KEY" 		=> "id_notification"
						);
		$tables_principales['spip_notifications'] =
			array('field' => &$spip_notifications, 'key' => &$spip_notifications_key);
		return $tables_principales;
	}


	function notifications_install($action){
		include_spip('inc/plugin');
		$info_plugin_notifications = plugin_get_infos(_NOM_PLUGIN_NOTIFICATIONS);
		$version_plugin = $info_plugin_notifications['version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['spip_notifications_version']) AND ($GLOBALS['meta']['spip_notifications_version'] >= $version_plugin));
				break;
			case 'install':
				include_spip('base/create');
				include_spip('base/abstract_sql');
				if (!isset($GLOBALS['meta']['spip_notifications_version'])) {
					creer_base();
					ecrire_meta('spip_notifications_version', $version_plugin);
					ecrire_meta('spip_notifications_smtp', 'non');
					ecrire_meta('spip_notifications_smtp_auth', 'non');
					ecrire_meta('spip_notifications_smtp_secure', 'non');
					ecrire_meta('spip_notifications_smtp_sender', '');
					ecrire_meta('spip_notifications_filtre_images', 1);
					ecrire_meta('spip_notifications_filtre_css', 1);
					ecrire_meta('spip_notifications_adresse_envoi', 'non');
					ecrire_meta('spip_notifications_filtre_iso_8859', 1);
					ecrire_metas();
				} else {
					$version_base = $GLOBALS['meta']['spip_notifications_version'];
					if ($version_base < 1.1) {
						ecrire_meta('spip_notifications_filtre_images', 1);
						ecrire_meta('spip_notifications_filtre_css', 1);
						ecrire_meta('spip_notifications_version', $version_base = 1.1);
						ecrire_metas();
					}
					if ($version_base < 1.2) {
						ecrire_meta('spip_notifications_adresse_envoi', 'non');
						ecrire_meta('spip_notifications_filtre_accents', 1);
						ecrire_meta('spip_notifications_version', $version_base = 1.2);
						ecrire_metas();
					}
					if ($version_base < 1.3) {
						ecrire_meta('spip_notifications_version', $version_base = 1.3);
						ecrire_metas();
					}
					if ($version_base < 1.4) {
						creer_base();
						ecrire_meta('spip_notifications_version', $version_base = 1.4);
						ecrire_metas();
					}
					if ($version_base < 1.5) {
						sql_alter("TABLE spip_notifications ADD ps MEDIUMTEXT NOT NULL");
						ecrire_meta('spip_notifications_version', $version_base = 1.5);
						ecrire_metas();
					}
					if ($version_base < 1.6) {
						// correction déclaration table spip_notifications
						creer_base();
						ecrire_meta('spip_notifications_version', $version_base = 1.6);
						ecrire_metas();
					}
					if ($version_base < 1.7) {
						global $table_logos;
						$table_logos['id_notification'] = 'not';
						$chercher_logo = charger_fonction('chercher_logo', 'inc');
						// logo des notifications par défaut
						if ($logo_on = $chercher_logo(0, 'id_notification', 'on')) {
							$ancien_nom = $logo_on[0];
							$nouveau_nom = $logo_on[1].'notificationon0.'.$logo_on[3];
							rename($ancien_nom, $nouveau_nom);
						}
						if ($logo_off = $chercher_logo(0, 'id_notification', 'off')) {
							$ancien_nom = $logo_off[0];
							$nouveau_nom = $logo_off[1].'notificationoff0.'.$logo_off[3];
							rename($ancien_nom, $nouveau_nom);
						}
						ecrire_meta('spip_notifications_version', $version_base = 1.7);
						ecrire_metas();
					}
					if ($version_base < 1.8) {
						// correction effet de bord inc/notifications
						ecrire_meta('spip_notifications_version', $version_base = 1.8);
						ecrire_metas();
					}
					if ($version_base < 1.9) {
						// correction logos notifications restés nommés noton...
						ecrire_meta('spip_notifications_version', $version_base = 1.9);
						ecrire_metas();
					}
					if ($version_base < 2.0) {
						ecrire_meta('spip_notifications_filtre_accents', 0);
						ecrire_meta('spip_notifications_filtre_iso_8859', 1);
						ecrire_meta('spip_notifications_version', $version_base = 2.0);
						ecrire_metas();
					}
					if ($version_base < 2.1) {
						ecrire_meta('spip_notifications_smtp_secure', 'non');
						ecrire_meta('spip_notifications_version', $version_base = 2.1);
						ecrire_metas();
					}
					if ($version_base < 2.2) {
						sql_drop_table('spip_documents_notifications', true);
						ecrire_meta('spip_notifications_version', $version_base = 2.2);
						ecrire_metas();
					}
				}
				break;
			case 'uninstall':
				include_spip('base/abstract_sql');
				sql_drop_table('spip_notifications', true);
				effacer_meta('spip_notifications_version');
				effacer_meta('spip_notifications_smtp');
				effacer_meta('spip_notifications_smtp_auth');
				effacer_meta('spip_notifications_smtp_secure');
				effacer_meta('spip_notifications_smtp_sender');
				effacer_meta('spip_notifications_filtre_images');
				effacer_meta('spip_notifications_filtre_css');
				effacer_meta('spip_notifications_adresse_envoi');
				effacer_meta('spip_notifications_filtre_iso_8859');
				break;
		}
	}


?>