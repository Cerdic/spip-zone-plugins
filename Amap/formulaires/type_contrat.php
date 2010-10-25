<?php
function formulaires_type_contrat_charger_dist() {
	$valeurs = array(
		"label_type_contrat" => "",
	);
	return $valeurs;
}

function formulaires_type_contrat_traiter_dist(){
	refuser_traiter_formulaire_ajax();

	$label_type_contrat = _request('label_type_contrat');

	$id_type_contrat = sql_insertq (
						'spip_amap_types_contrats', 
						array(
							"label_type_contrat" => $label_type_contrat,
							)
	);
	// Valeurs de retours
	$message['message_ok'] = _T('amap:types_contrats_enregistre');
	return $message;
}
?>
