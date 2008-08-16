<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_MODERATION',(_DIR_PLUGINS.end($p)));
define('_NOM_PLUGIN_MODERATION', (end($p)));

// ajout bouton ds interface admin
function moderation_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {		  
		  $boutons_admin['forum']->sousmenu['moderation']= new Bouton(
			'../'._DIR_PLUGIN_MODERATION.'/img_pack/icone.png', // icone
       _T("Mod&eacute;ration Mod&eacute;r&eacute;e") // titre
       );
		} 
		return $boutons_admin;
}
	
?>