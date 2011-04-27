<?php

/**
 * Pipeline d'affichage des blocs de navigation
 *
 */
function plugonet_affiche_droite($flux) {

	$exec = $flux["args"]["exec"];

	// Si on est sur l'onglet valider pquet.xml ou generer paquet.xml on affiche l'aide en ligne
	// du paquet.xml
	if (($exec == "plugonet_valider") 
	OR ($exec == "plugonet_generer")) {
		$flux['data'] .= recuperer_fond("prive/navigation/aide_paquetxml");
	}

	return $flux;
}

?>