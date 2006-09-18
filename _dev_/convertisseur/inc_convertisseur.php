<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_CONVERTISSEUR',(_DIR_PLUGINS.end($p)));

// ajout bouton ds interface admin
function Convertisseur_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {    // rendre aussi accessible aux redacteurs ??		  
		  $boutons_admin['naviguer']->sousmenu['convertisseur']= new Bouton(
			'../'._DIR_PLUGIN_CONVERTISSEUR.'/img_pack/icon.png', // icone
       _L("convertisseur:convertir_titre") // titre
       );
		} 
		return $boutons_admin;
}
	
?>
