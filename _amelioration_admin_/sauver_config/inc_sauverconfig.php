<?php

define('_DIR_PLUGIN_SAUVERCONFIG',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));

// ajout bouton ds interface admin
function Sauverconfig_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {		  
		  $boutons_admin['configuration']->sousmenu['sauverconfig']= new Bouton(
			'../'._DIR_PLUGIN_SAUVERCONFIG.'/img_pack/icon.png', // icone
       _L("Sauver la configuration") // titre
       );
		} 
		return $boutons_admin;
}
	
?>
