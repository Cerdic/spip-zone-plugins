<?php

/**
 * definition du plugin "Lilyspip" version "classe statique"
 * creation du bouton
 */

function lilyspip_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {

	  // Pour voir le bouton dans la barre "configuration"
	  $boutons_admin['configuration']->sousmenu['lilyspip']= new Bouton(
		find_in_path("img_pack/lilyspip-32.png"), // image
		'Lilyspip'	// titre
		);
	}

	return $boutons_admin;
}


function lilyspip_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}

?>