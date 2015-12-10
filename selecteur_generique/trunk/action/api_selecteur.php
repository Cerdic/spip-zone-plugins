<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Retourner un JSON listant les résultats d'une recherche de chaîne pour une autocomplétion
 *
 * Le format a retourner est décrit ici : http://jqueryui.com/demos/autocomplete/
 * Liste de résultats où chacun peut être soit une chaîne soit un tableau.
 * Si c'est un tableau alors la clé "label" correspond à ce qui est affiché dans le sélecteur, tandis que la clé "value" correspond à ce qui sera placé dans le champ.
 * Si c'est une chaîne, la même chose sera affiché dans le sélecteur et inséré dans le champ.
 * array('machin', 'truc', array('label' => 'Un mot', value => 123))
 */
function action_api_selecteur_dist() {
	// Il faut au moins le sélecteur dans l'argument sinon rien
	if (!$selecteur = _request('arg')){
		header('Status: 404 Not Found');
		exit;
	}
	
	// On cherche le JSON en passant les params de l'URL
	if (
		(_request('php') and $fonction = charger_fonction($selecteur, 'selecteurs', true) and $json = $fonction())
		or $json = recuperer_fond("selecteurs/$selecteur", $_GET)
	) {
		// On renvoie une ressource JSON
		header('Status: 200 OK');
		header("Content-type: application/json; charset=utf-8");
		echo $json;
		exit;
	}
	// Si on ne trouve rien c'est que ça n'existe pas
	else{
		header('Status: 404 Not Found');
		exit;
	}
}
