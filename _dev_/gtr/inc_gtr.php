<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_GTR',(_DIR_PLUGINS.end($p)));
define('_NOM_PLUGIN_GTR', (end($p)));

// ajout bouton ds interface admin
function gtr_ajouterBoutons($boutons_admin) {
  
		  $boutons_admin['naviguer']->sousmenu['traducteur']= new Bouton(
			'../'._DIR_PLUGIN_GTR.'/images/icone.png', // icone
       _T("Traducteur") // titre
       );
		return $boutons_admin;
}
	
?>