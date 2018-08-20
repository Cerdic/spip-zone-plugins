<?php

/**
 * Interroge le Webservice Prestashop et retourne ce qui est demandé.
 *
 * @param array $command
 *     Le tableau command de l'iterateur
 * @param array $iterateur
 *     L'iterateur complet
 **/
function inc_prestashop_ws_products_select_dist(&$command, $iterateur) {
	$criteres = $command['where'];
	$resource = $iterateur->get_resource();

	$query = [
		'resource' => $resource,
	];

	// on peut fournir une liste l'id
	// ou egalement un critere id=x
	$ids = array();

	// depuis une liste
	if (isset($command['liste']) and is_array($command['liste']) and count($command['liste'])) {
		$ids = $command['liste'];
	}


	// depuis un critere id=x ou {id?}
	if ($id = prestashop_ws_critere_valeur($criteres, 'id')) {
		$ids = prestashop_ws_intersect_ids($ids, $id);
		// pas la peine de filtrer dessus...
		$iterateur->exception_des_criteres('id');
		$query['filter[id]'] = '[' . implode('|', $ids) . ']';
	}

	// liste des champs possibles pour cette ressource
	// cela permet de renseigner les filtres automatiquement
	// et réduire ainsi la taille de la requête retournée,
	// évitant que ce soit l'itérateur data qui filtre après coup.
	$champs = prestashop_ws_show_resource($resource);
	foreach ($champs as $champ => $desc) {
		if ($val = prestashop_ws_critere_valeur($criteres, $champ)) {
			// pas la peine de filtrer dessus...
			$iterateur->exception_des_criteres($champ);
			$query['filter[' . $champ . ']'] = '[' . implode('|', $val) . ']';
		}
	}

	// display (full par défaut)
	if (!$display = prestashop_ws_critere_valeur($criteres, 'display')) {
		$display = 'full';
	}
	$iterateur->exception_des_criteres('display');
	$query['display'] = $display;


    //#PRICE_IVA
	$iterateur->exception_des_criteres('price[price_iva][use_tax]');
	$query['price[price_iva][use_tax]'] = 1;
	$iterateur->exception_des_criteres('price[price_iva][use_reduction]');
	$query['price[price_iva][use_reduction]'] = 0;


	//#PRICE_PUBLIC
	$iterateur->exception_des_criteres('price[price_public][use_reduction]');
	$query['price[price_public][use_reduction]'] = 1;
	$iterateur->exception_des_criteres('price[price_public][use_tax]');
	$query['price[price_public][use_tax]'] = 0;

    //#PRICE_PUBLIC_IVA
	$iterateur->exception_des_criteres('price[price_public_iva][use_reduction]');
	$query['price[price_public_iva][use_reduction]'] = 1;
	$iterateur->exception_des_criteres('price[price_public_iva][use_tax]');
	$query['price[price_public_iva][use_tax]'] = 1;


	/*
		Si on met une limite… on ne sait plus paginer
		car on ne connait pas le nombre total de résultats.

		// si la boucle contient une pagination {pagination 5}
		// on retrouve les valeurs de position et de pas
		if (!empty($command['pagination'])) {
			list($debut, $nombre) = $command['pagination'];
			if (!$debut) $debut = 0;
			$query['limit'] = $debut . ',' . $nombre;
		}
	*/

	try {
		$lang = !empty($iterateur->info[4]) ? $iterateur->info[4] : null;
		$wsps = \SPIP\Prestashop\Webservice::getInstanceByLang($lang);
	} catch (PrestaShopWebserviceException $ex) {
		spip_log('Erreur Webservice Prestashop : ' . $ex->getMessage(), 'prestashop.' . _LOG_ERREUR);
		return [];
	}

	// Demander les données au Prestashop.
	try {
		if ($xml = $wsps->get($query)) {
			$arbre = prestashop_ws_nettoyer_reception($xml, $resource, $iterateur->get_langues());
			return $arbre;
		}
	} catch (PrestaShopWebserviceException $ex) {
		spip_log('Erreur Webservice Prestashop : ' . $ex->getMessage(), 'prestashop.' . _LOG_ERREUR);
		spip_log('Query : ', 'prestashop.' . _LOG_ERREUR);
		spip_log($query, 'prestashop.' . _LOG_ERREUR);
		return [];
	}

	return [];
}