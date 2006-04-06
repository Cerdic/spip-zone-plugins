<?php

// ajout bouton ds interface admin
function SkelEditor_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {		  
		  $boutons_admin['naviguer']->sousmenu['skeleditor']= new Bouton(
			'../'._DIR_PLUGINS.'skel_editor/img_pack/icon.png', // icone
       _L("Editer les squelettes") // titre
       );
		} 
		return $boutons_admin;
}
	
?>
