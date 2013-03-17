<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_RAINETTE_WUNDERGROUND_URL_BASE_REQUETE', 'http://api.wunderground.com/api');
define('_RAINETTE_WUNDERGROUND_URL_BASE_ICONE', 'http://icons.wxug.com/i/c');
define('_RAINETTE_WUNDERGROUND_JOURS_PREVISIONS', 4);
define('_RAINETTE_WUNDERGROUND_SUFFIXE_METRIQUE', 'c:mb:km:kph');
define('_RAINETTE_WUNDERGROUND_SUFFIXE_STANDARD', 'f:in:mi:mph');
define('_RAINETTE_WUNDERGROUND_LANGUE_DEFAUT', 'FR');

function wunderground_service2cache($lieu, $mode) {

	include_spip('inc/config');
	$condition = lire_config('rainette/wunderground/condition');
	$langue = $GLOBALS['spip_lang'];

	$dir = sous_repertoire(_DIR_CACHE, 'rainette');
	$dir = sous_repertoire($dir, 'wunderground');
	$fichier_cache = $dir . str_replace(array(',', '+', '.', '/'), '-', $lieu) 
				   . "_" . $mode 
				   . ((($condition == 'wunderground') AND ($mode != 'infos')) ? '-' . $langue : '')
				   . ".txt";

	return $fichier_cache;
}

function wunderground_service2url($lieu, $mode) {

	include_spip('inc/config');
	$cle = lire_config('rainette/wunderground/inscription');

	// Determination de la demande
	if ($mode == 'infos') {
		$demande = 'geolookup';
	}
	else {
		$demande = ($mode == 'previsions') ? 'forecast/astronomy' : 'conditions';
	}

	// Identification et formatage du lieu
	$query = str_replace(array(' ', ','), array('', '/'), trim($lieu));
	$index = strpos($query, '/');
	if ($index !== false) {
		$ville = substr($query, 0, $index);
		$pays = substr($query, $index+1, strlen($query)-$index-1);
		$query = $pays . '/' . $ville;
	}

	// Identification de la langue du resume.
	// Le choix de la langue n'a d'interet que si on utilise le resume natif du service. Si ce n'est pas le cas
	// on ne la precise pas et on laisse l'API renvoyer la langue par defaut
	$condition = lire_config('rainette/wunderground/condition', 'wunderground');
	$code_langue = '';
	if ($condition == 'wunderground')
		$code_langue = langue2code_wunderground($GLOBALS['spip_lang']);

	$url = _RAINETTE_WUNDERGROUND_URL_BASE_REQUETE
		.  '/' . $cle
		.  '/' . $demande
		.  ($code_langue ? '/lang:' . $code_langue : '')
		.  '/q'
		.  '/' . $query . '.xml';

	return $url;
}


function wunderground_service2reload_time($mode) {

	static $reload = array('conditions' => 1800, 'previsions' => 7200);

	return $reload[$mode];
}

function wunderground_url2flux($url) {

	include_spip('inc/phraser');
	$flux = url2flux_xml($url, false);

	return $flux;
}


/**
 * lire le xml fournit par le service meteo et en extraire les infos interessantes
 * retournees en tableau jour par jour
 * utilise le parseur xml de Spip
 *
 * ne gere pas encore le jour et la nuit de la date courante suivant l'heure!!!!
 * @param array $flux
 * @return array
 */
function wunderground_flux2previsions($flux, $lieu) {
	$tableau = array();
	$index = 0;

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? true : false;

	return $tableau;
}

function wunderground_flux2conditions($flux, $lieu) {
	static $tendance = array('0' => 'steady', '+' => 'rising', '-' => 'falling');
	$tableau = array();

	// On stocke les informations disponibles dans un tableau standard
	if (isset($flux['children']['current_observation'][0]['children'])) {
		$conditions = $flux['children']['current_observation'][0]['children'];

		// Date d'observation
		$date_maj = (isset($conditions['observation_epoch'])) ? intval($conditions['observation_epoch'][0]['text']) : 0;
		$tableau['derniere_maj'] = date('Y-m-d H:i:s', $date_maj);
		// Station d'observation
		// TODO : pour l'instant le champ full n'est pas complet et a une virgule apres la ville - http://gsfn.us/t/329p4
		$tableau['station'] = (isset($conditions['observation_location']))
			? trim($conditions['observation_location'][0]['children']['full'][0]['text'], ',')
			: '';

		// Identification des suffixes d'unite pour choisir le bon champ
		// -> wunderground fournit toujours les valeurs dans les deux systemes d'unites
		include_spip('inc/config');
		$unite = lire_config('rainette/wunderground/unite', 'm');
		if ($unite == 'm')
			$suffixes = explode(':', _RAINETTE_WUNDERGROUND_SUFFIXE_METRIQUE);
		else
			$suffixes = explode(':', _RAINETTE_WUNDERGROUND_SUFFIXE_STANDARD);
		list($ut, $up, $ud, $uv) = $suffixes;


		// Liste des conditions meteo extraites dans le systeme demande
		$tableau['vitesse_vent'] = (isset($conditions['wind_'.$uv])) ? floatval($conditions['wind_'.$uv][0]['text']) : '';
		$tableau['angle_vent'] = (isset($conditions['wind_degrees'])) ? intval($conditions['wind_degrees'][0]['text']) : '';
		// TODO : a confirmer suite a la reponse au post - http://gsfn.us/t/32w74
		// -> La documentation indique que les directions uniques sont fournies sous forme de texte comme North
		//    alors que les autres sont des acronymes. On passe donc tout en acronyme
		$tableau['direction_vent'] = (isset($conditions['wind_dir']))
			? (strlen($conditions['wind_dir'][0]['text']) <= 3 ? $conditions['wind_dir'][0]['text'] : strtoupper(substr($conditions['wind_dir'][0]['text'], 0, 1))) : '';

		$tableau['temperature_reelle'] = (isset($conditions['temp_'.$ut])) ? intval($conditions['temp_'.$ut][0]['text']) : '';
		$tableau['temperature_ressentie'] = (isset($conditions['feelslike_'.$ut])) ? intval($conditions['feelslike_'.$ut][0]['text']) : '';

		$tableau['humidite'] = (isset($conditions['relative_humidity'])) ? intval($conditions['relative_humidity'][0]['text']) : '';
		$tableau['point_rosee'] = (isset($conditions['dewpoint_'.$ut])) ? intval($conditions['dewpoint_'.$ut][0]['text']) : '';

		$tableau['pression'] = (isset($conditions['pressure_'.$up])) ? floatval($conditions['pressure_'.$up][0]['text']) : '';
		$tableau['tendance_pression'] = (isset($conditions['pressure_trend'])) ? $tendance[$conditions['pressure_trend'][0]['text']] : '';

		$tableau['visibilite'] = (isset($conditions['visibility_'.$ud])) ? floatval($conditions['visibility_'.$ud][0]['text']) : '';

		// Code meteo, resume et icone natifs au service
		$tableau['code_meteo'] = (isset($conditions['icon'])) ? $conditions['icon'][0]['text'] : '';
		$tableau['icon_meteo'] = (isset($conditions['icon_url'])) ? $conditions['icon_url'][0]['text'] : '';
		$tableau['desc_meteo'] = (isset($conditions['weather'])) ? $conditions['weather'][0]['text'] : '';

		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service (cas actuel) le nom du fichier icone commence par "nt_" pour la nuit.
		// TODO : prendre en compte a terme le nouvel indicateur de jour/nuit dans une prochaine version de WUI
		$icone = basename($tableau['icon_meteo']);
		if (strpos($icone, 'nt_') === false)
			$tableau['periode'] = 0; // jour
		else
			$tableau['periode'] = 1; // nuit

		// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
		$condition = lire_config('rainette/wunderground/condition', 'wunderground');
		if ($condition == 'wunderground') {
			// On affiche les conditions natives fournies par le service.
			// Celles-ci etant deja traduites dans la bonne langue on stocke le texte exact retourne par l'API
			$tableau['icone']['code'] = $tableau['code_meteo'];
			$theme = lire_config('rainette/wunderground/theme', 'a');
			$url = _RAINETTE_WUNDERGROUND_URL_BASE_ICONE . '/' . $theme 
				 . '/' . ($tableau['periode'] == 1 ? 'nt_' : '') . $tableau['code_meteo'] . '.gif';
			$tableau['icone']['url'] = copie_locale($url);
			$tableau['resume'] = ucfirst($tableau['desc_meteo']);
		}
		else {
			// On affiche les conditions traduites dans le systeme weather.com
			// Pour le resume on stocke le code et non la traduction pour eviter de generer 
			// un cache par langue comme pour le mode natif. La traduction est faite via les fichiers de langue
			$meteo = meteo_wunderground2weather($tableau['code_meteo'], $tableau['periode']);
			$tableau['icone'] = $meteo;
			$tableau['resume'] = $meteo;
		}
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? true : false;

	return $tableau;
}

function wunderground_flux2infos($flux, $lieu) {
	$tableau = array();

	// On stocke les informations disponibles dans un tableau standard
	if (isset($flux['children']['location'][0]['children'])) {
		$infos = $flux['children']['location'][0]['children'];

		if (isset($infos['city'])) {
			$tableau['ville'] = $infos['city'][0]['text'];
			$tableau['ville'] .= (isset($infos['country_name'])) ? ', ' . $infos['country_name'][0]['text'] : '';
		}
		$tableau['region'] = '';

		$tableau['longitude'] = (isset($infos['lon'])) ? floatval($infos['lon'][0]['text']) : '';
		$tableau['latitude'] = (isset($infos['lat'])) ? floatval($infos['lat'][0]['text']) : '';

		$tableau['population'] = '';
		$tableau['zone'] = '';
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? true : false;

	return $tableau;
}

function wunderground_service2credits() {

	$credits = array('titre' => '');
	$credits['lien'] = 'http://www.wunderground.com/';
	$credits['logo'] = 'wunderground-126.png';

	return $credits;
}


function meteo_wunderground2weather($meteo, $periode=0) {
	static $wunderground2weather = array(
							'chanceflurries'=> array(41,46),
							'chancerain'=> array(39,45),
							'chancesleet'=> array(39,45),
							'chancesleet'=> array(41,46),
							'chancesnow'=> array(41,46),
							'chancetstorms'=> array(38,47),
							'clear'=> array(32,31),
							'cloudy'=> array(26,26),
							'flurries'=> array(15,15),
							'fog'=> array(20,20),
							'hazy'=> array(21,21),
							'mostlycloudy'=> array(28,27),
							'mostlysunny'=> array(34,33),
							'partlycloudy'=> array(30,29),
							'partlysunny'=> array(28,27),
							'sleet'=> array(5,5),
							'rain'=> array(11,11),
							'sleet'=> array(5,5),
							'snow'=> array(16,16),
							'sunny'=> array(32,31),
							'tstorms'=> array(4,4),
							'thunderstorms'=> array(4,4),
							'unknown'=> array(4,4),
							'cloudy'=> array(26,26),
							'scatteredclouds'=> array(30,29),
							'overcast'=> array(26,26));

	$icone = 'na';
	if (array_key_exists($meteo,  $wunderground2weather))
		$icone = strval($wunderground2weather[$meteo][$periode]);
	return $icone;
}

function langue2code_wunderground($langue) {
	static $langue2wunderground = array(
		'aa' => array('', ''), 					// afar
		'ab' => array('', ''), 					// abkhaze
		'af' => array('AF', ''), 				// afrikaans
		'am' => array('', ''), 					// amharique
		'an' => array('', 'SP'),				// aragonais
		'ar' => array('AR', ''), 				// arabe
		'as' => array('', ''), 					// assamais
		'ast' => array('', 'SP'), 				// asturien - iso 639-2
		'ay' => array('', ''), 					// aymara
		'az' => array('AZ', ''), 				// azeri
		'ba' => array('', ''),					// bashkir
		'be' => array('BY', ''), 				// bielorusse
		'ber_tam' => array('', ''),				// berbère
		'ber_tam_tfng' => array('', ''),		// berbère tifinagh
		'bg' => array('BU', ''), 				// bulgare
		'bh' => array('', ''),					// langues biharis
		'bi' => array('', ''),					// bichlamar
		'bm' => array('', ''),					// bambara
		'bn' => array('', ''),					// bengali
		'bo' => array('', ''),					// tibétain
		'br' => array('', 'FR'),				// breton
		'bs' => array('', ''),					// bosniaque
		'ca' => array('CA', ''),				// catalan
		'co' => array('', 'FR'),				// corse
		'cpf' => array('', 'FR'), 				// créole réunionais
		'cpf_dom' => array('', 'FR'), 			// créole ???
		'cpf_hat' => array('HT', ''), 			// créole haïtien
		'cs' => array('CZ', ''),				// tchèque
		'cy' => array('CY', ''),				// gallois
		'da' => array('DK', ''),				// danois
		'de' => array('DL', ''),				// allemand
		'dz' => array('', ''),					// dzongkha
		'el' => array('GR', ''),				// grec moderne
		'en' => array('EN', ''),				// anglais
		'en_hx' => array('', 'EN'),				// anglais hacker
		'en_sm' => array('', 'EN'),				// anglais smurf
		'eo' => array('EO', ''),				// esperanto
		'es' => array('SP', ''),				// espagnol
		'es_co' => array('', 'SP'),				// espagnol colombien
		'es_mx_pop' => array('', 'SP'),			// espagnol mexicain
		'et' => array('ET', ''),				// estonien
		'eu' => array('EU', ''),				// basque
		'fa' => array('FA', ''),				// persan (farsi)
		'ff' => array('', ''),					// peul
		'fi' => array('FI', ''),				// finnois
		'fj' => array('', 'EN'),				// fidjien
		'fo' => array('', 'DK'),				// féroïen
		'fon' => array('', ''),					// fon
		'fr' => array('FR', ''),				// français
		'fr_sc' => array('', 'FR'),				// français schtroumpf
		'fr_lpc' => array('', 'FR'),			// français langue parlée
		'fr_lsf' => array('', 'FR'),			// français langue des signes
		'fr_spl' => array('', 'FR'),			// français simplifié
		'fr_tu' => array('', 'FR'),				// français copain
		'fy' => array('', 'DL'),				// frison occidental
		'ga' => array('IR', ''),				// irlandais
		'gd' => array('', 'EN'),				// gaélique écossais
		'gl' => array('GZ', ''),				// galicien
		'gn' => array('', ''),					// guarani
		'grc' => array('', 'GR'),				// grec ancien
		'gu' => array('GU', ''),				// goudjrati
		'ha' => array('', ''),					// haoussa
		'hac' => array('', 'KU'), 				// Kurdish-Horami
		'hbo' => array('', 'IL'),				// hebreu classique ou biblique
		'he' => array('IL', ''),				// hébreu
		'hi' => array('HI', ''),				// hindi
		'hr' => array('CR', ''),				// croate
		'hu' => array('HU', ''),	 			// hongrois
		'hy' => array('HY', ''), 				// armenien
		'ia' => array('', ''),					// interlingua (langue auxiliaire internationale)
		'id' => array('ID', ''),				// indonésien
		'ie' => array('', ''),					// interlingue
		'ik' => array('', ''),					// inupiaq
		'is' => array('IS', ''),				// islandais
		'it' => array('IT', ''),				// italien
		'it_fem' => array('', 'IT'),			// italien féminin
		'iu' => array('', ''),					// inuktitut
		'ja' => array('JP', ''),				// japonais
		'jv' => array('JW', ''),				// javanais
		'ka' => array('KA', ''),				// géorgien
		'kk' => array('', ''),					// kazakh
		'kl' => array('', 'DK'),				// groenlandais
		'km' => array('KM', ''),				// khmer central
		'kn' => array('', ''),					// Kannada
		'ko' => array('KR', ''),				// coréen
		'ks' => array('', ''),					// kashmiri
		'ku' => array('KU', ''),				// kurde
		'ky' => array('', ''),					// kirghiz
		'la' => array('LA', ''),				// latin
		'lb' => array('', 'FR'),				// luxembourgeois
		'ln' => array('', ''),					// lingala
		'lo' => array('', ''), 					// lao
		'lt' => array('LT', ''),				// lituanien
		'lu' => array('', ''),					// luba-katanga
		'lv' => array('LV', ''),				// letton
		'man' => array('GM', ''),				// mandingue
		'mfv' => array('', ''), 				// manjaque - iso-639-3
		'mg' => array('', ''),					// malgache
		'mi' => array('MI', ''),				// maori
		'mk' => array('MK', ''),				// macédonien
		'ml' => array('', ''),					// malayalam
		'mn' => array('MN', ''),				// mongol
		'mo' => array('', 'RO'),				// moldave ??? normalement c'est ro comme le roumain
		'mos' => array('', ''),					// moré - iso 639-2
		'mr' => array('MR', ''),				// marathe
		'ms' => array('', ''),					// malais
		'mt' => array('MT', ''),				// maltais
		'my' => array('MY', ''),				// birman
		'na' => array('', ''),					// nauruan
		'nap' => array('', 'IT'),				// napolitain - iso 639-2
		'ne' => array('', ''),					// népalais
		'nqo' => array('', ''), 				// n’ko - iso 639-3
		'nl' => array('NL', ''),				// néerlandais
		'no' => array('NO', ''),				// norvégien
		'nb' => array('', 'NO'),				// norvégien bokmål
		'nn' => array('', 'NO'),				// norvégien nynorsk
		'oc' => array('OC', ''),				// occitan
		'oc_lnc' => array('', 'OC'),			// occitan languedocien
		'oc_ni' => array('', 'OC'),				// occitan niçard
		'oc_ni_la' => array('', 'OC'),			// occitan niçard
		'oc_prv' => array('', 'OC'),			// occitan provençal
		'oc_gsc' => array('', 'OC'),			// occitan gascon
		'oc_lms' => array('', 'OC'),			// occitan limousin
		'oc_auv' => array('', 'OC'),			// occitan auvergnat
		'oc_va' => array('', 'OC'),				// occitan vivaro-alpin
		'om' => array('', ''),					// galla
		'or' => array('', ''),					// oriya
		'pa' => array('PA', ''),				// pendjabi
		'pbb' => array('', ''),					// Nasa Yuwe (páez) - iso 639-3
		'pl' => array('PL', ''),				// polonais
		'ps' => array('PS', ''),				// pachto
		'pt' => array('BR', ''),				// portugais
		'pt_br' => array('', 'BR'),				// portugais brésilien
		'qu' => array('', ''),					// quechua
		'rm' => array('', ''),					// romanche
		'rn' => array('', ''),					// rundi
		'ro' => array('RO', ''),				// roumain
		'roa' => array('chti', ''),				// langues romanes (ch'ti) - iso 639-2
		'ru' => array('RU', ''),				// russe
		'rw' => array('', ''),					// rwanda
		'sa' => array('', ''),					// sanskrit
		'sc' => array('', 'IT'),				// sarde
		'scn' => array('', 'IT'),				// sicilien - iso 639-2
		'sd' => array('', ''),					// sindhi
		'sg' => array('', ''),					// sango
		'sh' => array('', 'SR'),				// serbo-croate
		'sh_latn' => array('', 'SR'),			// serbo-croate latin
		'sh_cyrl' => array('', 'SR'),			// serbo-croate cyrillique
		'si' => array('', ''),					// singhalais
		'sk' => array('SK', ''),				// slovaque
		'sl' => array('SL', ''),				// slovène
		'sm' => array('', ''),					// samoan
		'sn' => array('', ''),					// shona
		'so' => array('', ''),					// somali
		'sq' => array('AL', ''), 				// albanais
		'sr' => array('SR', ''),				// serbe
		'src' => array('', 'IT'), 				// sarde logoudorien - iso 639-3
		'sro' => array('', 'IT'), 				// sarde campidanien - iso 639-3
		'ss' => array('', ''),					// swati
		'st' => array('', ''),					// sotho du Sud
		'su' => array('', ''),					// soundanais
		'sv' => array('SW', ''),				// suédois
		'sw' => array('SI', ''),				// swahili
		'ta' => array('', ''), 					// tamoul
		'te' => array('', ''),					// télougou
		'tg' => array('', ''),					// tadjik
		'th' => array('TH', ''),				// thaï
		'ti' => array('', ''),					// tigrigna
		'tk' => array('TK', ''),				// turkmène
		'tl' => array('TL', ''),				// tagalog
		'tn' => array('', ''),					// tswana
		'to' => array('', ''),					// tongan (Îles Tonga)
		'tr' => array('TR', ''),				// turc
		'ts' => array('', ''),					// tsonga
		'tt' => array('TT', ''),				// tatar
		'tw' => array('', ''),					// twi
		'ty' => array('', 'FR'),			 	// tahitien
		'ug' => array('', ''),					// ouïgour
		'uk' => array('UA', ''),				// ukrainien
		'ur' => array('', ''),					// ourdou
		'uz' => array('UZ', ''),				// ouszbek
		'vi' => array('VU', ''),				// vietnamien
		'vo' => array('', ''),					// volapük
		'wa' => array('', 'FR'),				// wallon
		'wo' => array('SN', ''),				// wolof
		'xh' => array('', ''),					// xhosa
		'yi' => array('YI', ''),				// yiddish
		'yo' => array('', ''),					// yoruba
		'za' => array('', 'CN'),				// zhuang
		'zh' => array('CN', ''), 				// chinois (ecriture simplifiee)
		'zh_tw' => array('TW', ''), 			// chinois taiwan (ecriture traditionnelle)
		'zu' => array('', '')					// zoulou
	);

	$code = _RAINETTE_WUNDERGROUND_LANGUE_DEFAUT;
	if (array_key_exists($langue,  $langue2wunderground)) {
		if ($c0 = $langue2wunderground[$langue][0])
			$code = strtoupper($c0);
		else
			$code = strtoupper($langue2wunderground[$langue][1]);
	}

	return $code;
}

?>