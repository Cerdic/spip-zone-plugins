<?php
//
// ajout bouton
// 
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
  $flux .= "<link rel='stylesheet' type='text/css' href='?page=css_coloriage' />\n";   
  return $flux;
}

function coloriagedist_header_prive($flux){
  $flux .= "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('farbtastic/farbtastic.css'))."' />\n";     
  $flux .= "<script src='".url_absolue(find_in_path('farbtastic/farbtastic.js'))."' type=\"text/javascript\"></script>\n";
  $flux .= "<script src='".url_absolue(find_in_path('farbtastic/farbtastic_go.js'))."' type=\"text/javascript\"></script>\n";
	return $flux;

}
?>
