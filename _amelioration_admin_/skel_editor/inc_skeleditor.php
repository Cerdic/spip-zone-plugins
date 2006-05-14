<?php

define('_DIR_PLUGIN_SKELEDITOR',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));

// ajout bouton ds interface admin
function SkelEditor_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {		  
		  $boutons_admin['naviguer']->sousmenu['skeleditor']= new Bouton(
			'../'._DIR_PLUGIN_SKELEDITOR.'/img_pack/icon.png', // icone
       _T("skeleditor:editer_skel") // titre
       );
		} 
		return $boutons_admin;
}
	
?>
