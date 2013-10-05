<?php
function code2icone($icon){
	$r = "na";
	if (($icon >= 1) && ($icon < 48)) $r = strval($icon);
	return $r;
}


/**
 * lire le xml fournit par le service meteo et en extraire les infos interessantes
 * retournees en tableau jour par jour
 * utilise le parseur xml de Spip
 *
 * ne gere pas encore le jour et la nuit de la date courante suivant l'heure!!!!
 * @param array $xml
 * @return array
 * @author Cedric Morin
 */
function xml2tab_previsions($xml){
	$tableau = array();
	$n = spip_xml_match_nodes(",^dayf,",$xml,$previsions);
	if ($n==1){
		$previsions = reset($previsions['dayf']);
		// recuperer la date de debut des previsions (c'est la date de derniere maj)
		$date_maj = $previsions['lsup'][0];
		$date_maj = strtotime(preg_replace(',\slocal\s*time\s*,ims','',$date_maj));
		$index = 0;
		foreach($previsions as $day=>$p){
			if (preg_match(",day\s*d=['\"?]([0-9]+),Uims",$day,$regs)){
				$date_stamp = $date_maj+$regs[1]*24*3600;
				$p = reset($p);
				// Index du jour et date du jour
				$tableau[$index]['index'] = $index;
				$tableau[$index]['date'] = date('Y-m-d',$date_stamp);
				// Date complete des lever/coucher du soleil
				$date = getdate($date_stamp);
				$heure = getdate(strtotime($p['sunr'][0]));
				$sun = mktime($heure['hours'],$heure['minutes'],0,$date['mon'],$date['mday'],$date['year']);
				$tableau[$index]['lever_soleil'] = date('Y-m-d H:i:s',$sun);
				$heure = getdate(strtotime($p['suns'][0]));
				$sun = mktime($heure['hours'],$heure['minutes'],0,$date['mon'],$date['mday'],$date['year']);
				$tableau[$index]['coucher_soleil'] = date('Y-m-d H:i:s',$sun);
				// Previsions du jour
				$tableau[$index]['temperature_jour'] = intval($p['hi'][0]) ? intval($p['hi'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				$tableau[$index]['code_icone_jour'] = intval($p['part p="d"'][0]['icon'][0]) ? intval($p['part p="d"'][0]['icon'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				$tableau[$index]['vitesse_vent_jour'] = intval($p['part p="d"'][0]['wind'][0]['s'][0]) ? intval($p['part p="d"'][0]['wind'][0]['s'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				$tableau[$index]['angle_vent_jour'] = $p['part p="d"'][0]['wind'][0]['d'][0];
				$tableau[$index]['direction_vent_jour'] = $p['part p="d"'][0]['wind'][0]['t'][0];
				$tableau[$index]['risque_precipitation_jour'] = intval($p['part p="d"'][0]['ppcp'][0]);
				$tableau[$index]['humidite_jour'] = intval($p['part p="d"'][0]['hmid'][0]) ? intval($p['part p="d"'][0]['hmid'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				// Previsions de la nuit
				$tableau[$index]['temperature_nuit'] = intval($p['low'][0]) ? intval($p['low'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				$tableau[$index]['code_icone_nuit'] = intval($p['part p="n"'][0]['icon'][0]) ? intval($p['part p="n"'][0]['icon'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				$tableau[$index]['vitesse_vent_nuit'] = intval($p['part p="n"'][0]['wind'][0]['s'][0]) ? intval($p['part p="n"'][0]['wind'][0]['s'][0]) : _RAINETTE_VALEUR_INDETERMINEE;
				$tableau[$index]['angle_vent_nuit'] = $p['part p="n"'][0]['wind'][0]['d'][0];
				$tableau[$index]['direction_vent_nuit'] = $p['part p="n"'][0]['wind'][0]['t'][0];
				$tableau[$index]['risque_precipitation_nuit'] = intval($p['part p="n"'][0]['ppcp'][0]);
				$tableau[$index]['humidite_nuit'] = intval($p['part p="n"'][0]['hmid'][0]) ? intval($p['part p="n"'][0]['hmid'][0]) : _RAINETTE_VALEUR_INDETERMINEE;

				$index += 1;
			}
		}
		// On stocke en fin de tableau la date de derniere mise a jour
		$tableau[$index]['derniere_maj'] = date('Y-m-d H:i:s',$date_maj);
		// trier par date
		ksort($tableau);
	}
	return $tableau;
}

function xml2tab_conditions($xml){
	$tableau = array();
	$n = spip_xml_match_nodes(",^cc,",$xml,$conditions);
	if ($n==1){
		$conditions = reset($conditions['cc']);
		// recuperer la date de derniere mise a jour des conditions
		if ($conditions) {
			$date_maj = $conditions['lsup'][0];
			$date_maj = strtotime(preg_replace(',\slocal\s*time\s*,ims','',$date_maj));
			$tableau['derniere_maj'] = date('Y-m-d H:i:s',$date_maj);
			// station d'observation (peut etre differente de la ville)
			$tableau['station'] = $conditions['obst'][0];
			// Liste des conditions meteo
			$tableau['temperature_reelle'] = intval($conditions['tmp'][0]);
			$tableau['temperature_ressentie'] = intval($conditions['flik'][0]);
			$tableau['code_icone'] = intval($conditions['icon'][0]);
			$tableau['pression'] = intval($conditions['bar'][0]['r'][0]);
			$tableau['tendance_pression'] = $conditions['bar'][0]['d'][0];
			$tableau['vitesse_vent'] = intval($conditions['wind'][0]['s'][0]);
			$tableau['angle_vent'] = intval($conditions['wind'][0]['d'][0]);
			$tableau['direction_vent'] = $conditions['wind'][0]['t'][0];
			$tableau['humidite'] = intval($conditions['hmid'][0]);
			$tableau['point_rosee'] = intval($conditions['dewp'][0]);
			$tableau['visibilite'] = intval($conditions['vis'][0]);
		}
	}
	return $tableau;
}

function xml2tab_infos($xml, $code_meteo){
	$tableau = array();
	$regexp = 'loc id=\"'.$code_meteo.'\"';
	$n = spip_xml_match_nodes(",^$regexp,",$xml,$infos);
	if ($n==1){
		$infos = reset($infos['loc id="'.$code_meteo.'"']);
		// recuperer la date de debut des conditions
		$tableau['code_meteo'] = $code_meteo;
		$tableau['ville'] = $infos['dnam'][0];
		$tableau['longitude'] = floatval($infos['lon'][0]);
		$tableau['latitude'] = floatval($infos['lat'][0]);
		$tableau['zone'] = intval($infos['zone'][0]);
	}
	return $tableau;
}

/**
 * charger le fichier des infos meteos correspondant au code
 * si le fichier analyse est trop vieux ou absent, on charge le xml et on l'analyse
 * puis on stocke les infos apres analyse
 *
 * @param string $code_meteo
 * @return string
 * @author Cedric Morin
 */
function charger_meteo($code_meteo, $mode='previsions'){
	$code_meteo = strtoupper($code_meteo);
	$dir = sous_repertoire(_DIR_CACHE,"rainette");
	$dir = sous_repertoire($dir,substr(md5($code_meteo),0,1));
	$f = $dir . $code_meteo . "_".$mode . ".txt";

	if ($mode == 'infos') {
		// Traitement du fichier d'infos
		if (!file_exists($f)) {
			$flux = "http://xml.weather.com/weather/local/".$code_meteo."?unit="._RAINETTE_SYSTEME_MESURE;
			include_spip('inc/xml');
			$xml = spip_xml_load($flux);
			$tableau = xml2tab_infos($xml, $code_meteo);
			ecrire_fichier($f, serialize($tableau));
		}
	}
	else {
		// Traitement du fichier de donnees requis
		$reload_time = ($mode == 'previsions') ? _RAINETTE_RELOAD_TIME_PREVISIONS : _RAINETTE_RELOAD_TIME_CONDITIONS;
		if (!file_exists($f)
		  || !filemtime($f)
		  || (time()-filemtime($f)>$reload_time)) {
			$flux = "http://xml.weather.com/weather/local/".$code_meteo."?unit="._RAINETTE_SYSTEME_MESURE;
			$flux .= ($mode == 'previsions') ? "&dayf="._RAINETTE_JOURS_PREVISION : "&cc=*";
			include_spip('inc/xml');
			$xml = spip_xml_load($flux);
			$tableau = ($mode == 'previsions') ? xml2tab_previsions($xml) : xml2tab_conditions($xml);
			ecrire_fichier($f, serialize($tableau));
		}
	}
	return $f;
}

function charger_infos($code_meteo='', $type_infos=''){
	if (!$code_meteo) return '';
	$nom_fichier = charger_meteo($code_meteo, 'infos');
	lire_fichier($nom_fichier,$tableau);
	if (!$type_infos)
		return $tableau;
	else {
		$tableau = unserialize($tableau);
		$info = $tableau[strtolower($type_infos)];
		if (!$info) $info = ucfirst($type_infos) . "(" . $code_meteo . ")";
		return $info;
	}
}

?>