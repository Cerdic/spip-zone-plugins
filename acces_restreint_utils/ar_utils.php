<?php
/**
 * Plugin acces_restreint_utils pour Spip 2.0
 * Des utilitaires pour faciliter l'utilisation du plugin Acces Restreint
 * Auteur : Cyril Marion
 */


function ar_utils_affiche_gauche($flux) {

	$exec =  $flux['args']['exec'];
	
	// si on est sur la page ?exec=naviguer
	if ($exec=='naviguer'){
		// on charge la petite boite
		$flux['data'] .= recuperer_fond('prive/contenu/acces_rubrique', $_GET);
	}

	return $flux;
}




?>
