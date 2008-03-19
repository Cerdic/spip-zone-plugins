<?php
//
// ajout bouton 
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_MALETTRE',(_DIR_PLUGINS.end($p)));
 
function malettre_ajouterBoutons($boutons_admin) {
		// si on est admin (deactive)		
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) { // admin full
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['naviguer']->sousmenu['malettre']= new Bouton(
			"../"._DIR_PLUGIN_MALETTRE."/img_pack/icon_malettre.png",  // icone
			_T("malettre:ma_lettre")	// titre
			);
		}
		return $boutons_admin;
}

//
// functions
function malettre_get_contents($file) {
	if (function_exists('file_get_contents')) return file_get_contents($file);   // >php4.3
                                      else  return implode('', file($file));
					          	
}


?>