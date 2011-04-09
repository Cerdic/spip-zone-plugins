<?php
function formulaires_saison_charger_dist($id_saison) {
	$valeurs = array(
		"id_saison" => $id_saison,
		"id_agenda" => $titre,
                "id_contrat" => $titre,
                "id_sortie" => $titre,
                "id_responsable" => $titre,
                "id_vacance" => $titre,
        );
	return $valeurs;
}

function formulaires_saison_traiter_dist($id_saison){
	refuser_traiter_formulaire_ajax();

	$id_agenda = _request('id_agenda');
	$id_contrat = _request('id_contrat');
	$id_sortie = _request('id_sortie');
	$id_responsable = _request('id_responsable');
	$id_vacance = _request('id_vacance');

	$id_saison = sql_insertq (
						'spip_amap_saisons', 
						array(
							"id_agenda" => $id_agenda,
							"id_contrat" => $id_contrat,
							"id_sortie" => $id_sortie,
							"id_responsable" => $id_responsable,
							"id_vacance" => $id_vacance
							)
	);
	// Valeurs de retours
	$message['message_ok'] = _T('amap:saisons_enregistre');
	return $message;
}
?>