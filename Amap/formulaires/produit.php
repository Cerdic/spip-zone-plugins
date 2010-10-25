<?php
function formulaires_produit_charger_dist() {
	$valeurs = array(
		"id_auteur" => "",
		"label_produit" => "",
	);
	return $valeurs;
}

function formulaires_produit_traiter_dist(){
	refuser_traiter_formulaire_ajax();

	$id_auteur = _request('id_auteur');
	$label_produit = _request('label_produit');

	$id_produit = sql_insertq (
						'spip_amap_produits', 
						array(
							"id_auteur" => $id_auteur,
							"label_produit" => $label_produit,
							)
	);
	// Valeurs de retours
	$message['message_ok'] = _T('amap:produits_enregistre');
	return $message;
}
?>
