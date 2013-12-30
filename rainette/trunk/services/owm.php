<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_RAINETTE_OWM_URL_BASE_REQUETE'))
	define('_RAINETTE_OWM_URL_BASE_REQUETE', 'http://api.openweathermap.org/data/2.5/');
if (!defined('_RAINETTE_OWM_URL_BASE_ICONE'))
	define('_RAINETTE_OWM_URL_BASE_ICONE', 'http://openweathermap.org/img/w');
if (!defined('_RAINETTE_OWM_JOURS_PREVISIONS'))
	define('_RAINETTE_OWM_JOURS_PREVISIONS', 14);


function owm_service2cache($lieu, $mode) {

	include_spip('inc/config');
	$condition = lire_config('rainette/owm/condition');
	$langue = $GLOBALS['spip_lang'];

	$dir = sous_repertoire(_DIR_CACHE, 'rainette');
	$dir = sous_repertoire($dir, 'owm');
	$fichier_cache = $dir . str_replace(array(',', '+', '.', '/'), '-', $lieu) 
				   . "_" . $mode 
				   . ((($condition == 'owm') AND ($mode != 'infos')) ? '-' . $langue : '')
				   . ".txt";

	return $fichier_cache;
}

function owm_service2url($lieu, $mode) {

	include_spip('inc/config');

	// Determination de la demande
	$demande = ($mode == 'previsions') ? 'forecast/daily' : 'weather';

	// Identification du système d'unité
	$unite = lire_config('rainette/owm/unite', 'm');

	// Clé d'inscription facultative
	$cle = lire_config('rainette/owm/inscription');

	// Identification de la langue du resume.
	// Le choix de la langue n'a d'interet que si on utilise le resume natif du service. Si ce n'est pas le cas
	// on ne la precise pas et on laisse l'API renvoyer la langue par defaut
	$condition = lire_config('rainette/owm/condition', 'owm');
	$code_langue = '';
	if ($condition == 'owm')
		$code_langue = langue2code_owm($GLOBALS['spip_lang']);

	$url = _RAINETTE_OWM_URL_BASE_REQUETE
		.  $demande. '?'
		.  'q=' . trim($lieu)
		.  '&mode=xml'
		.  '&units=' . ('m' ? 'metric' : 'imperial')
		.  ($mode == 'previsions' ? '&cnt=' . _RAINETTE_OWM_JOURS_PREVISIONS : '')
		.  ($code_langue ? '&lang=' . $code_langue : '')
		.  ($cle ? '&APPID=' . $cle : '');

	return $url;
}


function owm_service2reload_time($mode) {

	static $reload = array('conditions' => 7200, 'previsions' => 10800);

	return $reload[$mode];
}

function owm_url2flux($url) {

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
function owm_flux2previsions($flux, $lieu) {
	$tableau = array();
	$index = 0;

	// Traitement des erreurs de flux
	$tableau[$index]['erreur'] = (!$tableau) ? 'chargement' : '';

	// Ajout des informations communes dans l'index adéquat
	$tableau[$index]['max_jours'] = _RAINETTE_OWM_JOURS_PREVISIONS;

	return $tableau;
}

function owm_flux2conditions($flux, $lieu) {
	$tableau = array();

	// On stocke les informations disponibles dans un tableau standard
	if (isset($flux['children'])) {
		$conditions = $flux['children'];

		// Date d'observation
		$date_maj = (isset($conditions['lastupdate'])) ? strtotime($conditions['lastupdate'][0]['attributes']['value']) : 0;
		$tableau['derniere_maj'] = date('Y-m-d H:i:s', $date_maj);
		// Station d'observation
		$tableau['station'] = NULL;

		// Liste des conditions meteo
		if ($conditions['wind'][0]['children']) {
			$conditions_vent = $conditions['wind'][0]['children'];

			$tableau['vitesse_vent'] = (isset($conditions_vent['speed'])) ? floatval($conditions_vent['speed'][0]['attributes']['value']) : '';
			$tableau['angle_vent'] = (isset($conditions_vent['direction'])) ? intval($conditions_vent['direction'][0]['attributes']['value']) : '';
			$tableau['direction_vent'] = (isset($conditions_vent['direction']))	? $conditions_vent['direction'][0]['attributes']['code'] : '';
		}

		$tableau['temperature_reelle'] = (isset($conditions['temperature'])) ? intval($conditions['temperature'][0]['attributes']['value']) : '';
		$tableau['temperature_ressentie'] = (isset($conditions['temperature'])) ? temperature2ressenti($tableau['temperature_reelle'], $tableau['vitesse_vent']) : '';

		$tableau['humidite'] = (isset($conditions['humidity'])) ? intval($conditions['humidity'][0]['attributes']['value']) : '';
		$tableau['point_rosee'] = NULL;

		$tableau['pression'] = (isset($conditions['pressure'])) ? floatval($conditions['pressure'][0]['attributes']['value']) : '';
		$tableau['tendance_pression'] = NULL;

		$tableau['visibilite'] = NULL;

		// Code meteo, resume et icone natifs au service
		$tableau['code_meteo'] = (isset($conditions['weather'])) ? $conditions['weather'][0]['attributes']['number'] : '';
		$tableau['icon_meteo'] = (isset($conditions['weather'])) ? $conditions['weather'][0]['attributes']['icon'] : '';
		$tableau['desc_meteo'] = (isset($conditions['weather'])) ? $conditions['weather'][0]['attributes']['value'] : '';

		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service le nom du fichier icone finit par "d" pour le jour et
		// par "n" pour la nuit.
		$icone = basename($tableau['icon_meteo']);
		if (strpos($icone, 'n') === false)
			$tableau['periode'] = 0; // jour
		else
			$tableau['periode'] = 1; // nuit

		// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
		$condition = lire_config('rainette/owm/condition', 'owm');
		if ($condition == 'owm') {
			// On affiche les conditions natives fournies par le service.
			// Celles-ci etant deja traduites dans la bonne langue on stocke le texte exact retourne par l'API
			$tableau['icone']['code'] = $tableau['code_meteo'];
			$url = _RAINETTE_OWM_URL_BASE_ICONE . '/' . $tableau['icon_meteo'] . '.png';
			$tableau['icone']['url'] = copie_locale($url);
			$tableau['resume'] = ucfirst($tableau['desc_meteo']);
		}
		else {
			// On affiche les conditions traduites dans le systeme weather.com
			// Pour le resume on stocke le code et non la traduction pour eviter de generer 
			// un cache par langue comme pour le mode natif. La traduction est faite via les fichiers de langue
			$meteo = meteo_owm2weather($tableau['code_meteo'], $tableau['periode']);
			$tableau['icone'] = $meteo;
			$tableau['resume'] = $meteo;
		}
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? 'chargement' : '';

	return $tableau;
}

function owm_flux2infos($flux, $lieu) {
	$tableau = array();

	// On stocke les informations disponibles dans un tableau standard
	if (isset($flux['children']['city'][0]['attributes']['name'])) {
		$tableau['ville'] = $flux['children']['city'][0]['attributes']['name'];
	}

	if (isset($flux['children']['city'][0]['children']['coord'][0]['attributes'])) {
		$infos = $flux['children']['city'][0]['children']['coord'][0]['attributes'];

		$tableau['region'] = NULL;

		$tableau['longitude'] = (isset($infos['lon'])) ? floatval($infos['lon']) : '';
		$tableau['latitude'] = (isset($infos['lat'])) ? floatval($infos['lat']) : '';

		$tableau['population'] = NULL;
	}

	// Traitement des erreurs de flux
	$tableau['erreur'] = (!$tableau) ? 'chargement' : '';

	return $tableau;
}

function owm_service2credits() {

	$credits = array('titre' => '', 'logo' => '');
	$credits['lien'] = 'http://openweathermap.org/';

	return $credits;
}


function meteo_owm2weather($meteo, $periode=0) {
	static $owm2weather = array(
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
	if (array_key_exists($meteo,  $owm2weather))
		$icone = strval($owm2weather[$meteo][$periode]);
	return $icone;
}

function langue2code_owm($langue) {
	static $langue2owm = array(
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
	if (array_key_exists($langue,  $langue2owm)) {
		if ($c0 = $langue2owm[$langue][0])
			$code = strtoupper($c0);
		else
			$code = strtoupper($langue2owm[$langue][1]);
	}

	return $code;
}

?>