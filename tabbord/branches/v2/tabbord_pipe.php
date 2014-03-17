<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
*/

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_TABBORD',(_DIR_PLUGINS.end($p)));
 

	function tabbord_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "statistiques"
		  $boutons_admin['statistiques_visites']->sousmenu["tabbord_gen"]= new Bouton(
			"../"._DIR_PLUGIN_TABBORD."/img_pack/tabbord-24.png",  // icone
			_T('tabbord:titre_plugin')	// titre  
			);
		}
		return $boutons_admin;
	}

	// style
	function tabbord_header_prive($flux) {
		$flux .= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_TABBORD.'tabbord_styles.css" />'."\n";
		return $flux;
	}
?>
