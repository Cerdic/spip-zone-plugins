<?php

function grenouille_decode_icone($code){
 	$tableau_meteo	= array(
							"1"	=> "pluie",
							"2"	=> "pluie",
							"3"	=> "orage",
							"4"	=> "orage",
							"5"	=> "pluie",
							"6"	=> "neige",
							"7"	=> "verglas",
							"8"	=> "pluie",
							"9"	=> "pluie",
							"10"	=> "pluie",
							"11"	=> "pluie",
							"12"	=> "pluie",
							"13"	=> "neige",
							"14"	=> "neige",
							"15"	=> "neige",
							"16"	=> "neige",
							"17"	=> "orage",
							"18"	=> "neige",
							"19"	=> "brouillard",
							"20"	=> "brouillard",
							"21"	=> "brouillard",
							"22"	=> "brouillard",
							"23"	=> "vent",
							"24"	=> "vent",
							"25"	=> "vent",
							"26"	=> "nuages",
							"27"	=> "lune-nuages",
							"28"	=> "soleil-nuages",
							"29"	=> "lune-nuage",
							"30"	=> "soleil-nuage",
							"31"	=> "lune",
							"32"	=> "soleil",
							"33"	=> "lune-nuage",
							"34"	=> "soleil-nuage",
							"35"	=> "orage",
							"36"	=> "soleil",
							"37"	=> "orage",
							"38"	=> "orage",
							"39"	=> "pluie",
							"40"	=> "pluie",
							"41"	=> "neige",
							"42"	=> "neige",
							"43"	=> "neige",
							"44"	=> "soleil-nuage",
							"45"	=> "pluie",
							"46"	=> "neige",
							"47"	=> "orage",
							"48"	=> "inconnu",
						);
	return isset($tableau_meteo[$code])?$tableau_meteo[$code]:$tableau_meteo[48];
}

/**
 * filtre traduire_meteo
 *
 * @param string temps
 * @return string traduction
 * @author Pierre Basson
 **/
function grenouille_traduire_temps($temps) {
	if (!$temps) return '';
	return _T('grenouille:meteo_'.$temps);
}

/**
 * lire le xml fournit par le service meteo et en extraire les infos interessantes
 * retournees en tableau jour par jour
 * utilise le parseur xml de Spip
 *
 * @param array $xml
 * @return array
 * @author Cedric Morin
 */
function grenouille_xml2tab_previsions($xml){
	$tableau = array();
	$n = spip_xml_match_nodes(",^dayf,",$xml,$previsions);
	if ($n==1){
		$previsions = reset($previsions['dayf']);
		// recuperer la date de debut des previsions
		$date = $previsions['lsup'][0];
		$date = strtotime(preg_replace(',\slocal\s*time\s*,ims','',$date));
		foreach($previsions as $day=>$p){
			if (preg_match(",day\s*d=['\"?]([0-9]+),Uims",$day,$regs)){
				$jour = date('Y-m-d',$date+$regs[1]*24*3600);
				$p = reset($p);
				$tableau[$jour]['date'] = $jour;
				$tableau[$jour]['maxima']=intval($p['hi'][0])?intval($p['hi'][0]):'';
				$tableau[$jour]['minima']=intval($p['low'][0])?intval($p['low'][0]):'';
				$tableau[$jour]['icone']=intval($p['part p="d"'][0]['icon'][0]);
				$tableau[$jour]['humidite']=intval($p['hmid'][0])?intval($p['hmid'][0]):'';
			}
		}
		// trier par date
		ksort($tableau);
	}
	return $tableau;
}

function grenouille_xml2tab_conditions($xml){
	$tableau = array();
	$n = spip_xml_match_nodes(",^cc,",$xml,$conditions);
	if ($n==1){
		$conditions = reset($conditions['cc']);
		// recuperer la date de debut des conditions
		$date = $conditions['lsup'][0];
		$date = strtotime(preg_replace(',\slocal\s*time\s*,ims','',$date));
		$jour = date('Y-m-d',$date);
		$tableau['date'] = $jour;
		$tableau['t_reelle']=intval($conditions['tmp'][0])?intval($conditions['tmp'][0]):'';
		$tableau['t_ressentie']=intval($conditions['flik'][0])?intval($conditions['flik'][0]):'';
		$tableau['icone']=intval($conditions['icon'][0]);
		$tableau['pression']=intval($conditions['bar'][0]['r'][0]);
		$tableau['tendance_pression']=$conditions['bar'][0]['d'][0];
		$tableau['vitesse_vent']=intval($conditions['wind'][0]['s'][0]);
		$tableau['direction_vent']=$conditions['wind'][0]['t'][0];
		$tableau['humidite']=intval($conditions['hmid'][0])?intval($conditions['hmid'][0]):'';
		$tableau['point_rosee']=intval($conditions['dewp'][0])?intval($conditions['dewp'][0]):'';
		// trier par date
	}
	return $tableau;
}


/**
 * charger le fichier des infos meteos correspondant au code
 * si le fichier analyse est trop vieux ou absent, on charge le xml et on l'analyse
 * puis on stocke les infos apres analyse
 *
 * @param string $code_frxx
 * @return string
 * @author Cedric Morin
 */
function grenouille_charge_meteo($code_frxx, $mode='previsions'){
	$dir = sous_repertoire(_DIR_CACHE,"rainette");
	$dir = sous_repertoire($dir,substr(md5($code_frxx),0,1));
	$f = $dir . $code_frxx . "_".$mode . ".txt";
	$reload_time = ($mode == 'previsions') ? _RAINETTE_RELOAD_TIME_PREVISIONS : _RAINETTE_RELOAD_TIME_CONDITIONS;
	if (!file_exists($f)
	  || !filemtime($f)
	  || (time()-filemtime($f)>$reload_time)) {
		$flux = "http://xoap.weather.com/weather/local/".$code_frxx."?unit="._RAINETTE_SYSTEME_MESURE;
		$flux .= ($mode == 'previsions') ? "&dayf="._RAINETTE_JOURS_PREVISION : "&cc=*";
		include_spip('inc/xml');
		$xml = spip_xml_load($flux);
		$tableau = ($mode == 'previsions') ? grenouille_xml2tab_previsions($xml) : grenouille_xml2tab_conditions($xml);
		ecrire_fichier($f,serialize($tableau));
	}
  return $f;
}

/**
 * grenouille_fahrenheit2celsius
 *
 * @param int temperature en fahrenheit
 * @return int temperature en celcius
 * @author Pierre Basson
 **/
function grenouille_fahrenheit2celsius($t) {
	return round( ($t - 32) * 5 / 9 );
}


?>