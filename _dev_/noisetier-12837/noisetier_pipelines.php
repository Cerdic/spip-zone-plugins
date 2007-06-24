<?php

	function noisetier_ajouterBoutons($boutons_admin) {
		// si on est admin
		if (autoriser('gerer','noisetier')) {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['configuration']->sousmenu['noisetier']= new Bouton(
			"../"._DIR_PLUGIN_NOISETIER."/img_pack/noisetier-24.png",  // icone
			_T('noisetier:icone_menu')	// titre
			);
		}
		return $boutons_admin;
	}

?>