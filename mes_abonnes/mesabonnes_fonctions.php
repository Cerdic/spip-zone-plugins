<?php
//
// ajout bouton 
function mesabonnes_ajouter_boutons($boutons_admin) {
		// si on est admin full
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) { 
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['naviguer']->sousmenu['mesabonnes']= new Bouton(
			_DIR_PLUGIN_MESABONNES."/images/logo_mesabonnes_24.png",  // icone
			_T("mesabonnes:mes_abonnes")	// titre
			);
		}
		return $boutons_admin;
}

?>