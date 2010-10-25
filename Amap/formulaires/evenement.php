<?php
function formulaires_evenement_charger_dist($id_saison) {
	$valeurs = array(
		"date_evenement" => "",
		"id_saison" => "$id_saison",
		"id_lieu" => "",
		"id_personne1" => "",
		"id_personne2" => "",
		"id_personne3" => "",
	);
	return $valeurs;
}

function formulaires_evenement_verifier_dist(){
	$erreurs = array();
	if (!_request('date_evenement'))
		$erreurs['date_evenement'] = _T('info_obligatoire');
	return $erreurs;
}

function formulaires_evenement_traiter_dist(){
	refuser_traiter_formulaire_ajax();

	$date_evenement = _request('date_evenement');
	$id_saison = _request('id_saison');
	$id_lieu = _request('id_lieu');
	$id_personne1 = _request('id_personne1');
	$id_personne2 = _request('id_personne2');
	$id_personne3 = _request('id_personne3');

	$id_produit = sql_insertq (
						'spip_amap_evenements', 
						array(
							"date_evenement" => $date_evenement,
							"id_saison" => $id_saison,
							"id_lieu" => $id_lieu,
							"id_personne1" => $id_personne1,
							"id_personne2" => $id_personne2,
							"id_personne3" => $id_personne3,
							)
	);
	// Valeurs de retours
	$message['message_ok'] = _T('amap:evenements_enregistre');
	return $message;
}
?>
