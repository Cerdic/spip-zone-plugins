<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * get_quantite()
 *
 * @param $objet
 * @param $id_objet
*/
function get_quantite($objet, $id_objet) {

	$table_stocks = table_objet_sql('stocks');

	return sql_getfetsel(
		'quantite',
		$table_stocks,
		array(
			'objet = '.sql_quote($objet),
			'id_objet = '.intval($id_objet)
		)
	);
}

/**
 * set_quantite()
 *
 * @param $objet
 * @param $id_objet
 * @param $quantite
*/
function set_quantite($objet, $id_objet, $quantite) {
	$quantite = intval($quantite);

	// On cherche l'id_stock de l'objet
	$id_stock = sql_getfetsel(
		'id_stock',
		'spip_stocks',
		array('objet='.sql_quote($objet), 'id_objet='.intval($id_objet))
	);
//spip_log("STOCK : $id_stock",'stocks');
//spip_log("OBJET : $objet - $id_objet",'stocks');
	include_spip('action/editer_objet');
	if (!$id_stock) {
		$id_stock = objet_inserer('stock', null, null);
	}
	$res = objet_modifier('stocks', $id_stock, array('objet'=> $objet,
													 'id_objet'=>$id_objet,
													 'quantite' => $quantite));
	//spip_log("inssertion  : $res",'stocks');

	return ($res) ? $res : $quantite;
}

/**
 * incrementer_quantite
 *
 * @param $objet
 * @param $id_objet
 * @param $quantite {int} peut être négatif dans ce cas c'est une décrémentation
 * @return $update sql_update
*/
function incrementer_quantite($objet, $id_objet, $quantite) {

	$table_stocks = table_objet_sql('stocks');

	$quantite = intval($quantite);

	if ($quantite == 0) {
		return 0;
	}

	if ($quantite > 0) {
		$set = array('quantite' => 'quantite + '.abs($quantite));
	} else {
		$set = array('quantite' => 'quantite - '.abs($quantite));
	}

	$update = sql_update(
		$table_stocks,
		$set,
		array(
			'objet = '.sql_quote($objet),
			'id_objet = '.intval($id_objet)
		)
	);

	return $update;
}
