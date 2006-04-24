<?php

// ajout bouton ds interface admin
function Sauverconfig_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {		  
		  $boutons_admin['configuration']->sousmenu['sauverconfig']= new Bouton(
			'../'._DIR_PLUGINS.'sauver_config/img_pack/icon.png', // icone
       _L("Sauver la configuration") // titre
       );
		} 
		return $boutons_admin;
}
	
?>
