<?php
/*
 * Squelette
 * (c) 2016 
 * Distribue sous licence GPL
 *
 * @url - http://programmer.spip.net/-Formulaires-35-
 * 
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}



function formulaires_gerer_stock_charger_dist($objet, $id_objet, $retour = ''){
include_spip('inc/stocks');
	$quantite = get_quantite($objet,$id_objet);
	$stock_default = lire_config('stocks/quantite_default');
	$valeurs = array(
		'objet' => $objet,
		'id_objet' => $id_objet,
		'is_stock' => ($quantite) ? true : false ,
		'_quantite' => isset($quantite) ? $quantite : $stock_default
	);
	

	return $valeurs;
}

function formulaires_gerer_stock_verifier_dist($objet,$id_objet,$retour = ''){
	$erreurs = array();
	foreach(array('_quantite') as $champ) {
		if (!_request($champ)) {
			$erreurs[$champ] = "Cette information est obligatoire !";
		}
	}
	if (!is_numeric(_request('_quantite'))) {
		$erreurs['_quantite'] = "Doit être un nombre";
	}
	if (count($erreurs)) {
		$erreurs['message_erreur'] = "Erreur dans votre saisie";
	}
	return $erreurs;
}

function formulaires_gerer_stock_traiter_dist($objet,$id_objet,$retour = ''){
include_spip('inc/stocks');
	$quantite = _request('_quantite');
	set_quantite($objet,$id_objet,$quantite);
	set_request('is_stock', true);
	
	return array('message_ok'=>_T('stocks:reponse_ok'),
				 'editable'=>true);
}

?>