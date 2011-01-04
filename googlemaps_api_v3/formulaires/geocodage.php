<?php
function formulaires_geocodage_charger_dist($id_article){
	include_spip('base/abstract_sql');
	$champs = array('latitude,longitude,adresse_complete');
	$from = 'spip_articles';
	$where = array("id_article=$id_article");
	$infos = sql_allfetsel($champs, $from, $where);
		
	$valeurs = array(
		'id_article' => $id_article,
		'address' => $infos[0]['adresse_complete'],
		'confirmer' => '',
		'latitude' => $infos[0]['latitude'],
		'longitude' => $infos[0]['longitude'],
		);
	return $valeurs;
}


function formulaires_geocodage_verifier_dist(){
	// Effectuer le placement sur la carte
	if(_request('address')){
		
		$address = _request('address');	
		include_spip('inc/google');		
		$geocodeResponse = geocodageGoogleJson($address);

		// Travailler si OK
		$statut = $geocodeResponse->status;
		if ( $statut != 'OK') {
			return array("message_erreur" => "Google n'a pas trouvé !");
		}
				
		// Envoyer au HTML et au Charger
		$lat = $geocodeResponse->results[0]->geometry->location->lat;
		$lng = $geocodeResponse->results[0]->geometry->location->lng;
		set_request('latitude', $lat);
		set_request('longitude', $lng);
	}	
	
	$erreurs = array();
	$champs_obligatoires = array(
		'confirmer' => ''
		// 'latitude' => '',
		// 'longitude' => ''
	);
	foreach($champs_obligatoires as $obligatoire => $valeur){
		if (!_request($obligatoire)) $erreurs[$obligatoire] = '*Ce champ est obligatoire';
	}
		
	if (count($erreurs)){
		$erreurs['message_confirmer_placement'] = _T('gmaps_v3:message_confirmer_placement');
	}

	return $erreurs;
}

function formulaires_geocodage_traiter_dist($id_article=''){
	// On rempli les champs
	include_spip('inc/acces');
	$latitude = _request('latitude');
	$longitude = _request('longitude');
	$adresse = _request('address');
	
	$n = sql_updateq('spip_articles',array('latitude'=>$latitude,'longitude'=>$longitude,'adresse_complete'=>$adresse),"id_article=".$id_article);
	
	$message_ok = '';
	return array("message_ok" => $message_ok);
}
