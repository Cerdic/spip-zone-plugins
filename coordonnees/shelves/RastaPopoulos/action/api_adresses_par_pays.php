<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * API permettant d'avoir le HTML des champs d'une adresse suivant un pays
 * site.exemple/adresses_par_pays.api/FR
 * Il est possible de préremplir les champs
 */
function action_api_adresses_par_pays_dist() {
	// Il faut au moins le pays sinon rien
	if (
		$code_pays = _request('arg')
		and include_spip('inc/coordonnees')
		and $saisies_pays = coordonnees_adresses_saisies_par_pays($code_pays, _request('obligatoire'))
	){
		$contexte = array_merge($_GET, array('saisies' => $saisies_pays));
		
		// On génère le HTML de ces saisies, avec l'environnement envoyé
		$html = recuperer_fond('inclure/generer_saisies', $contexte);
		
		// On renvoie tout ça
		header('Status: 200 OK');
		header("Content-type: text/html; charset=utf-8");
		echo $html;
		exit;
	}
	
	header('Status: 404 Not Found');
	exit;
}
