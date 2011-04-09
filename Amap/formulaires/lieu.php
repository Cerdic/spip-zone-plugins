<?php
function formulaires_lieu_charger_dist() {
	$valeurs = array(
		"lieux_nom" => "",
		"lieux_rue" => "",
		"lieux_cp" => "",
		"lieux_ville" => "",
		"lieux_telephone" => "",
	);
	return $valeurs;
}

function formulaires_lieu_traiter_dist(){
	refuser_traiter_formulaire_ajax();

	$lieux_nom = _request('lieux_nom');
	$lieux_rue = _request('lieux_rue');
	$lieux_cp = _request('lieux_cp');
	$lieux_ville = _request('lieux_ville');
	$lieux_telephone = _request('lieux_telephone');

	$id_lieu = sql_insertq (
						'spip_amap_lieux', 
						array(
							"lieux_nom" => $lieux_nom,
							"lieux_rue" => $lieux_rue,
							"lieux_cp" => $lieux_cp,
							"lieux_ville" => $lieux_ville,
							"lieux_telephone" => $lieux_telephone
							)
	);
	// Valeurs de retours
	$message['message_ok'] = _T('amap:lieux_enregistre');
	return $message;
}
?>