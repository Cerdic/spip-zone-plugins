<?php
function formulaires_banque_charger_dist() {
	$valeurs = array(
		"label_banque" => "",
	);
	return $valeurs;
}

function formulaires_banque_traiter_dist(){
	refuser_traiter_formulaire_ajax();

	$label_banque = _request('label_banque');

	$id_banque = sql_insertq (
						'spip_amap_banques', 
						array(
							"label_banque" => $label_banque,
							)
	);
	// Valeurs de retours
	$message['message_ok'] = _T('amap:banques_enregistre');
	return $message;
}
?>
