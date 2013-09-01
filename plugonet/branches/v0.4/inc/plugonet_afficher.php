<?php

/**
 * Pipeline d'affichage des blocs de navigation droite
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


/**
 * Pipeline d'affichage des blocs de navigation gauche
 *
 */
function plugonet_affiche_gauche($flux) {

	$exec = $flux["args"]["exec"];

	// On affiche le bloc d'infos du plugin quelque soit la page de plugonet
	// -- extraction des informations a fournir au bloc
	if (($exec == "plugonet_valider") 
	OR ($exec == "plugonet_generer")
	OR ($exec == "plugonet_verifier")) {
		$informer = chercher_filtre('info_plugin');
		$infos = $informer('plugonet', 'tout');
		$infos['description'] = propre($infos['description']);
	
		$flux['data'] .= recuperer_fond("prive/navigation/info_plugin", array('infos' => $infos));
	}

	return $flux;
}

?>