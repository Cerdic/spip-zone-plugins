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
					ecrire_meta('facteur_version', $version_plugin);
					ecrire_meta('facteur_smtp', 'non');
					ecrire_meta('facteur_smtp_auth', 'non');
					ecrire_meta('facteur_smtp_secure', 'non');
					ecrire_meta('facteur_smtp_sender', '');
					ecrire_meta('facteur_filtre_images', 1);
					ecrire_meta('facteur_filtre_css', 1);
					ecrire_meta('facteur_adresse_envoi', 'non');
					ecrire_meta('facteur_filtre_iso_8859', 1);
					ecrire_metas();
				} else {
					$version_base = $GLOBALS['meta']['facteur_version'];
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
				effacer_meta('facteur_adresse_envoi');
				effacer_meta('facteur_filtre_iso_8859');
				break;
		}
	}


?>