<?php


	function facteur_install($action){
		include_spip('inc/plugin');
		$info_plugin_facteur = plugin_get_infos(_NOM_PLUGIN_FACTEUR);
		$version_plugin = $info_plugin_facteur['version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['facteur_version']) AND ($GLOBALS['meta']['facteur_version'] >= $version_plugin));
				break;
			case 'install':
				if (!isset($GLOBALS['meta']['facteur_version'])) {
					if (isset($GLOBALS['meta']['spip_notifications_version'])) {
						include_spip('base/abstract_sql');
						ecrire_meta('facteur_version', $version_plugin);
						ecrire_meta('facteur_smtp', $GLOBALS['meta']['spip_notifications_smtp']);
						ecrire_meta('facteur_smtp_auth', $GLOBALS['meta']['spip_notifications_smtp_auth']);
						ecrire_meta('facteur_smtp_secure', $GLOBALS['meta']['spip_notifications_smtp_secure']);
						ecrire_meta('facteur_smtp_sender', $GLOBALS['meta']['spip_notifications_smtp_sender']);
						ecrire_meta('facteur_filtre_images', $GLOBALS['meta']['spip_notifications_filtre_images']);
						ecrire_meta('facteur_filtre_css', $GLOBALS['meta']['spip_notifications_filtre_css']);
						ecrire_meta('facteur_filtre_iso_8859', $GLOBALS['meta']['spip_notifications_filtre_iso_8859']);
						ecrire_meta('facteur_adresse_envoi', $GLOBALS['meta']['spip_notifications_adresse_envoi']);
						ecrire_meta('facteur_adresse_envoi_nom', $GLOBALS['meta']['spip_notifications_adresse_envoi_nom']);
						ecrire_meta('facteur_adresse_envoi_email', $GLOBALS['meta']['spip_notifications_adresse_envoi_email']);
						effacer_meta('spip_notifications_smtp');
						effacer_meta('spip_notifications_smtp_auth');
						effacer_meta('spip_notifications_smtp_secure');
						effacer_meta('spip_notifications_smtp_sender');
						effacer_meta('spip_notifications_filtre_images');
						effacer_meta('spip_notifications_filtre_css');
						effacer_meta('spip_notifications_filtre_iso_8859');
						effacer_meta('spip_notifications_adresse_envoi');
						effacer_meta('spip_notifications_adresse_envoi_nom');
						effacer_meta('spip_notifications_adresse_envoi_email');
						effacer_meta('spip_notifications_version');
						sql_drop_table('spip_notifications', true);
						ecrire_metas();
					} else {
						ecrire_meta('facteur_version', $version_plugin);
						ecrire_meta('facteur_smtp', 'non');
						ecrire_meta('facteur_smtp_auth', 'non');
						ecrire_meta('facteur_smtp_secure', 'non');
						ecrire_meta('facteur_smtp_sender', '');
						ecrire_meta('facteur_filtre_images', 1);
						ecrire_meta('facteur_filtre_css', 1);
						ecrire_meta('facteur_filtre_iso_8859', 1);
						ecrire_meta('facteur_adresse_envoi', 'non');
						ecrire_metas();
					}
				} else {
					$version_base = $GLOBALS['meta']['facteur_version'];
					if ($version_base < 1.1) {
						// version compatible php4
						ecrire_meta('facteur_version', $version_base = 1.1);
						ecrire_metas();
					}
				}
				break;
			case 'uninstall':
				effacer_meta('facteur_version');
				effacer_meta('facteur_smtp');
				effacer_meta('facteur_smtp_auth');
				effacer_meta('facteur_smtp_secure');
				effacer_meta('facteur_smtp_sender');
				effacer_meta('facteur_filtre_images');
				effacer_meta('facteur_filtre_css');
				effacer_meta('facteur_filtre_iso_8859');
				effacer_meta('facteur_adresse_envoi');
				effacer_meta('facteur_adresse_envoi_nom');
				effacer_meta('facteur_adresse_envoi_email');
				break;
		}
	}


?>