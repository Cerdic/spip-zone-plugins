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
		$address = _request('address')."\n\n";	
		include_spip('inc/distant');
		include_spip('inc/xml');
		$url = 'http://maps.google.com/maps/api/geocode/xml';
		$url = parametre_url($url, 'sensor', 'false', '&');
		$url = parametre_url($url, 'address', $address, '&');

		$url = recuperer_page($url);
		$url = spip_xml_parse($url);

		// recuperation du resultat si OK
		$statut = $url['GeocodeResponse']['0']['status']['0'];
		if ( $statut != 'OK') {
			set_request('address', 'faute');
			return array("message_erreur" => "Pas de chance, faux retour de l'ami Google !");
		}

		// envoi au charger
		$lat = $url['GeocodeResponse'][0]['result'][0]['geometry'][0]['location'][0]['lat'][0];
		$lng = $url['GeocodeResponse'][0]['result'][0]['geometry'][0]['location'][0]['lng'][0];
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
