<?php

function spip_loader_update_ajouter_boutons($boutons_admin) {
	if ( $GLOBALS['connect_statut'] == "0minirezo"
		&& $GLOBALS["connect_toutes_rubriques"]) {
		$boutons_admin['configuration']->sousmenu["spip_loader_update"] = new Bouton(
			find_in_path('images/spip_loader_update.png'),
			_L("Mises &agrave; jour")
		);
	}
	return $boutons_admin;
}

?>