<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_RANGEMENT_PLUGS',(_DIR_PLUGINS.end($p)));

/* public static */
	function rangement_plugs_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['configuration']->sousmenu['rangement_plugin']= new Bouton(
			"../"._DIR_PLUGIN_RANGEMENT_PLUGS."/img_pack/rangement.png",  // icone
			_L('Rangement plugins')	// titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function rangement_plugs_ajouter_onglets($flux) {
		
		if(_request('exec')=='rangement_plugin') {
			if ($handle = opendir('../plugins')) {
    		while (false !== ($dossier_plugin = readdir($handle))) {
        		if ($dossier_plugin != "." && $dossier_plugin != "..") {
	        		$flux['data'][$dossier_plugin] = new Bouton('', $dossier_plugin, generer_url_ecrire('rangement_plugin', 'famille='.$dossier_plugin));
        		}
    		}
    		closedir($handle);
	}
    	}
    return $flux;
}

?>