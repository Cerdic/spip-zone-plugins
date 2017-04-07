<?php

/*
 * Le prix HT Existe-t-il une fonction précise pour le prix HT de ce type d'objet : prix_ht_<objet>() dans prix/<objet>.php
 * if ($fonction_ht = charger_fonction('ht', "prix/$type_objet", true)){
 * On passe la ligne SQL en paramètre pour ne pas refaire la requête
 * $prix_ht = $fonction_ht($id_objet, $ligne);
 */
function prix_reservations_detail_ht($id_objet, $les_prix) {
	if ($les_prix['prix_ht'] != '0.00') {
		$prix_ht = $les_prix['prix_ht'];
	}
	else {
		$taxe = isset($les_prix['taxe']) ? $les_prix['taxe'] : 0;

		if ($taxe > 0.00) {
			$prix_ht = $les_prix['prix'] / (1 + $taxe);
		}
		else {
			$prix_ht = $les_prix['prix'];
		}
	}
	return $prix_ht;
}

// Le prix TTC
function prix_reservations_detail_dist($id_reservations_detail) {
	$les_prix = sql_fetsel('prix,prix_ht,taxe', 'spip_reservations_details', 'id_reservations_detail=' . $id_reservations_detail);

	if ($les_prix['prix'] != '0.00')
		$prix = $les_prix['prix'];
		else {
			$taxe = isset($les_prix['taxe']) ? $les_prix['taxe'] : 0;

			if ($taxe > 0.00) {
				$prix = $les_prix['prix_ht'] + ($les_prix['prix_ht'] * $taxe);
			}
			else {
				$prix = $les_prix['prix_ht'];
			}
		}
		return $prix;
}

?>