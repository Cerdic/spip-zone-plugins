<?php


$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_PHPMV',(_DIR_PLUGINS.end($p)));

	function phpmv_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
			if ($GLOBALS['meta']["activer_statistiques"] == 'non') {
				$start = array_slice($boutons_admin,0,4);
				$end = array_slice($boutons_admin,4);
				// pas de stat SPIP -> creer le menu
				$start['statistiques_visites']=
				  new Bouton('statistiques-48.png', 'icone_statistiques_visites','?exec=phpmv');
				$boutons_admin = array_merge($start,$end);
			}
			else{
				$boutons_admin['statistiques_visites']->sousmenu["phpmv"]= new Bouton(
				"../"._DIR_PLUGIN_PHPMV."/img_pack/phpmv-logo.png",  // icone
				_L("PHPMyVisites") //titre
				);
			}
		}
		return $boutons_admin;
	}


?>