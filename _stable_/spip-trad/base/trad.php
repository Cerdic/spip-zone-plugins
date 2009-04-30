<?php


	/**
	 * SPIP-Trad
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function trad_install($action){
		include_spip('inc/plugin');
		$info_plugin_trad = plugin_get_infos(_NOM_PLUGIN_TRAD);
		$version_plugin = $info_plugin_trad['version'];
		switch ($action) {
			case 'test':
				return (isset($GLOBALS['meta']['spip_trad_version']) AND ($GLOBALS['meta']['spip_trad_version'] >= $version_plugin));
				break;
			case 'install':
				include_spip('base/create');
				include_spip('base/abstract_sql');
				if (!isset($GLOBALS['meta']['spip_trad_version'])) {
					sql_alter('TABLE spip_rubriques ADD id_trad BIGINT(21) NOT NULL DEFAULT "0"');
					sql_update('spip_rubriques', array('id_trad' => 'id_rubrique'));
					ecrire_meta('spip_trad_version', $version_plugin);
					ecrire_metas();
				} else {
					$version_base = $GLOBALS['meta']['spip_trad_version'];
					if ($version_base < 1.1) {
						sql_update('spip_rubriques', array('id_trad' => 'id_rubrique'), 'id_trad=0');
						ecrire_meta('spip_trad_version', $version_base = 1.1);
						ecrire_metas();
					}
				}
				break;
			case 'uninstall':
				include_spip('base/abstract_sql');
				sql_alter('TABLE spip_rubriques DROP id_trad');
				effacer_meta('spip_trad_version');
				break;
		}
	}


?>