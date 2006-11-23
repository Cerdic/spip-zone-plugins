<?php

//
// ajout bouton 
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_COLORIAGEDIST',(_DIR_PLUGINS.end($p)));
 
function coloriagedist_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['naviguer']->sousmenu['coloriagedist']= new Bouton(
			"../"._DIR_PLUGIN_COLORIAGEDIST."/img_pack/icon.png",  // icone
			_T("coloriagedist:change_fond")	// titre
			);
		}
		return $boutons_admin;
}

//
// functions
//
function coloriagedist_insert_head($flux){
  $flux .= "<link rel='stylesheet' type='text/css' href='"._DIR_PLUGIN_COLORIAGEDIST."colorpicker/colorPicker.css'>\n";
  $flux .= "<link rel='stylesheet' type='text/css' href='spip.php?page=css_coloriage'>\n";
  $flux .= "<script src='"._DIR_PLUGIN_COLORIAGEDIST."colorpicker/lib/prototype.js' type=\"text/javascript\"></script>\n";
  $flux .= "<script src='"._DIR_PLUGIN_COLORIAGEDIST."colorpicker/scriptaculous/scriptaculous.js' type=\"text/javascript\"></script>\n";
  $flux .= "<script src='"._DIR_PLUGIN_COLORIAGEDIST."colorpicker/yahoo.color.js' type=\"text/javascript\"></script>\n";
  $flux .= "<script src='"._DIR_PLUGIN_COLORIAGEDIST."colorpicker/colorPicker.js' type=\"text/javascript\"></script>\n";  
	return $flux;
}

?>


