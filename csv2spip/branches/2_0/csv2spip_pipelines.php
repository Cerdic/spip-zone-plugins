<?php
	function csv2spip_ajouter_boutons($boutons_admin) {
		if ($GLOBALS['connect_statut'] == "0minirezo") {
			include_spip('inc/plugin');
			$Tplugins = liste_plugin_actifs();
			if (!array_key_exists('BANDO', $Tplugins)) {
			  // on voit le bouton comme  sous-menu de "auteurs", si le plugin bando est actif c'est un bouton du menu Maintenance  (cf plugin.xml)
				$boutons_admin['auteurs']->sousmenu['csv2spip']= new Bouton("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.png", _T('csvspip:module_titre') );
			}
		}
		return $boutons_admin;
	}

?>
