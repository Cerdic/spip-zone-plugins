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
	$table_stocks = table_objet_sql('stocks');
	$stock = sql_fetsel(
		'id_stock, quantite',
		$table_stocks,
		array(
			'objet = '.sql_quote($objet),
			'id_objet = '.intval($id_objet)
		)
	);

	$quantite = is_numeric($stock['quantite']) ? $stock['quantite'] : false;

	$stock_default = lire_config('stocks/quantite_default');
	$valeurs = array(
		'objet' => $objet,
		'id_objet' => $id_objet,
		'id_stock' => $stock['id_stock'],
		'is_stock' => is_numeric($quantite) ? true : false ,
		'_quantite' => is_numeric($quantite) ? $quantite : $stock_default
	);

    return $valeurs;
}

function formulaires_gerer_stock_verifier_dist($objet,$id_objet,$retour = ''){
	$erreurs = array();
	//foreach(array('_quantite') as $champ) {
	//	if (!_request($champ)) {
	//		$erreurs[$champ] = "Cette information est obligatoire !";
	//	}
	//}
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
	$callback= "<script>(function(){ajaxReload('gestion_stock');return true;})()</script>";
	return array(
			'message_ok'=>_T('stocks:reponse_ok').$callback,
			'editable'=>true);
}

?>
