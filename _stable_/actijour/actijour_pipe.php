<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.52 - 08/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Declare pipeline
+--------------------------------------------+
*/

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ACTIJOUR',(_DIR_PLUGINS.end($p)));
 

	function actijour_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "statistiques"
		  $boutons_admin['statistiques_visites']->sousmenu["actijour_pg"]= new Bouton(
			_DIR_PLUGIN_ACTIJOUR."img_pack/activ_jour.gif",  // icone
			_T('acjr:activite_du_jour')	// titre  
			);
		}
		return $boutons_admin;
	}

	// style
	function actijour_header_prive($flux) {
		$flux .= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_ACTIJOUR.'actijour_styles.css" >'."\n";
		$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_ACTIJOUR.'func_js_acj.js"></script>'."\n";
		return $flux;
	}
?>
