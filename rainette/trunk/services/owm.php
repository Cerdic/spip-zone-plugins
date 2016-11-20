<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service Open Weather Map (owm).
 * Ce service fournit des données au format XML ou JSON.
 *
 * @package SPIP\RAINETTE\SERVICES\OWM
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_OWM_URL_BASE_REQUETE')) {
	define('_RAINETTE_OWM_URL_BASE_REQUETE', 'http://api.openweathermap.org/data/2.5/');
}
if (!defined('_RAINETTE_OWM_URL_BASE_ICONE')) {
	define('_RAINETTE_OWM_URL_BASE_ICONE', 'http://openweathermap.org/img/w');
}


// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_owm_config']['service'] = array(
	'defauts'        => array(
		'inscription' => '',
		'unite'       => 'm',
		'condition'   => 'owm',
		'theme'       => '',
	),
	'credits'        => array(
		'titre' => null,
		'logo'  => null,
		'lien'  => 'http://openweathermap.org/',
	),
	'langue_service' => 'EN'
);

// Configuration des données fournies par le service wunderground pour le mode 'infos'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_owm_config']['infos'] = array(
	'periode_maj' => 86400,
	'format_flux' => 'json',
	'cle_base'    => array(),
	'donnees'     => array(
		// Lieu
		'ville'     => array('cle' => array('name')),
		'pays'      => array('cle' => array()),
		'pays_iso2' => array('cle' => array('sys', 'country')),
		'region'    => array('cle' => array()),
		// Coordonnées
		'longitude' => array('cle' => array('coord', 'lon')),
		'latitude'  => array('cle' => array('coord', 'lat')),
		// Informations complémentaires : aucune configuration car ce sont des données calculées
	),
);

// Configuration des données fournies par le service owm pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
// -- On utilise le mode XML et non JSON car la date de dernière mise à jour n'est pas disponible en JSON
$GLOBALS['rainette_owm_config']['conditions'] = array(
	'periode_maj' => 7200,
	'format_flux' => 'xml',
	'cle_base'    => array('children'),
	'donnees'     => array(
		// Données d'observation
		'derniere_maj'          => array('cle' => array('lastupdate', 0, 'attributes', 'value')),
		'station'               => array('cle' => array()),
		// Températures
		'temperature_reelle'    => array('cle' => array('temperature', 0, 'attributes', 'value')),
		'temperature_ressentie' => array('cle' => array()),
		// Données anémométriques
		'vitesse_vent'          => array('cle' => array('wind', 0, 'children', 'speed', 0, 'attributes', 'value')),
		'angle_vent'            => array('cle' => array('wind', 0, 'children', 'direction', 0, 'attributes', 'value')),
		'direction_vent'        => array('cle' => array('wind', 0, 'children', 'direction', 0, 'attributes', 'code')),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array()),
		'humidite'              => array('cle' => array('humidity', 0, 'attributes', 'value')),
		'point_rosee'           => array('cle' => array()),
		'pression'              => array('cle' => array('pressure', 0, 'attributes', 'value')),
		'tendance_pression'     => array('cle' => array()),
		'visibilite'            => array('cle' => array('visibility', 0, 'attributes', 'value')),
		'indice_uv'             => array('cle' => array()),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('weather', 0, 'attributes', 'number')),
		'icon_meteo'            => array('cle' => array('weather', 0, 'attributes', 'icon')),
		'desc_meteo'            => array('cle' => array('weather', 0, 'attributes', 'value')),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service owm pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
// -- On utilise le mode XML et non JSON car la date de dernière mise à jour et la précipitation ne sont
//    pas disponibles en JSON
$GLOBALS['rainette_owm_config']['previsions'] = array(
	'periodicites'       => array(
		24 => array('max_jours' => 16),
		//		3  => array('max_jours' => 5)
	),
	'periodicite_defaut' => 24,
	'periode_maj'        => 7200,
	'format_flux'        => 'xml',
	'cle_base'           => array('children', 'forecast', 0, 'children', 'time'),
	'cle_heure'          => array(),
	'structure_heure'    => false,
	'donnees'            => array(
		// Données d'observation
		'date'                 => array('cle' => array('attributes', 'day')),
		'heure'                => array('cle' => array()),
		// Données astronomiques
		'lever_soleil'         => array('cle' => array()),
		'coucher_soleil'       => array('cle' => array()),
		// Températures
		'temperature'          => array('cle' => array()),
		'temperature_max'      => array('cle' => array('children', 'temperature', 0, 'attributes', 'max')),
		'temperature_min'      => array('cle' => array('children', 'temperature', 0, 'attributes', 'min')),
		// Données anémométriques
		'vitesse_vent'         => array('cle' => array('children', 'windspeed', 0, 'attributes', 'mps')),
		'angle_vent'           => array('cle' => array('children', 'winddirection', 0, 'attributes', 'deg')),
		'direction_vent'       => array('cle' => array('children', 'winddirection', 0, 'attributes', 'code')),
		// Données atmosphériques : risque_uv est calculé
		'risque_precipitation' => array('cle' => array()),
		'precipitation'        => array('cle' => array('children', 'precipitation', 0, 'attributes', 'value')),
		'humidite'             => array('cle' => array('children', 'humidity', 0, 'attributes', 'value')),
		'point_rosee'          => array('cle' => array()),
		'pression'             => array('cle' => array('children', 'pressure', 0, 'attributes', 'value')),
		'visibilite'           => array('cle' => array()),
		'indice_uv'            => array('cle' => array()),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('children', 'symbol', 0, 'attributes', 'number')),
		'icon_meteo'           => array('cle' => array('children', 'symbol', 0, 'attributes', 'var')),
		'desc_meteo'           => array('cle' => array('children', 'symbol', 0, 'attributes', 'name')),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

/**
 * ------------------------------------------------------------------------------------------------
 * Les fonctions qui suivent définissent l'API standard du service et sont appelées par la fonction
 * unique de chargement des données météorologiques `charger_meteo()`.
 * PACKAGE SPIP\RAINETTE\OWM\API
 * ------------------------------------------------------------------------------------------------
 */

/**
 * @param string $mode
 *
 * @return string
 */
function owm_service2configuration($mode) {
	// On merge la configuration propre au mode et la configuration du service proprement dit
	// composée des valeurs par défaut de la configuration utilisateur et de paramètres généraux.
	$config = array_merge($GLOBALS['rainette_owm_config'][$mode], $GLOBALS['rainette_owm_config']['service']);

	return $config;
}

/**
 * @param string $lieu
 * @param string $mode
 * @param        $periodicite
 * @param        $configuration
 *
 * @return string
 */
function owm_service2cache($lieu, $mode, $periodicite, $configuration) {

	// Identification de la langue du resume.
	$code_langue = ($configuration['condition'] == 'owm')
		? langue2code_owm($GLOBALS['spip_lang'])
		: $configuration['langue_service'];

	// Construction du chemin du fichier cache
	include_spip('inc/rainette_normaliser');
	$fichier_cache = normaliser_cache('owm', $lieu, $mode, $periodicite, $code_langue);

	return $fichier_cache;
}

/**
 * @param $lieu
 * @param $mode
 * @param $periodicite
 * @param $configuration
 *
 * @return string
 */
function owm_service2url($lieu, $mode, $periodicite, $configuration) {

	// Determination de la demande
	$demande = ($mode == 'previsions') ? 'forecast' : 'weather';
	if ($periodicite == 24) {
		$demande .= '/daily';
	}

	// Identification de la langue du resume.
	// Le choix de la langue n'a d'interet que si on utilise le resume natif du service. Si ce n'est pas le cas
	// on ne la precise pas et on laisse l'API renvoyer la langue par defaut
	$code_langue = ($configuration['condition'] == 'owm')
		? langue2code_owm($GLOBALS['spip_lang'])
		: $configuration['langue_service'];

	// On normalise le lieu et on récupère son format.
	// Le service accepte la format ville,pays et le format latitude,longitude
	include_spip('inc/rainette_normaliser');
	list($lieu_normalise, $format_lieu) = normaliser_lieu($lieu);
	if ($format_lieu == 'latitude_longitude') {
		list($latitude, $longitude) = explode(',', $lieu_normalise);
		$query = "lat=${latitude}&lon=${longitude}";
	} else {
		// Format ville,pays
		$query = "q=${lieu_normalise}";
	}

	$url = _RAINETTE_OWM_URL_BASE_REQUETE
		   . $demande . '?'
		   . $query
		   . '&mode=' . $configuration['format_flux']
		   . '&units=' . ($configuration['unite'] == 'm' ? 'metric' : 'imperial')
		   . ((($mode == 'previsions') and ($periodicite == 24))
			? '&cnt=' . $configuration['periodicites'][$periodicite]['max_jours']
			: '')
		   . '&lang=' . $code_langue
		   . ($configuration['inscription'] ? '&APPID=' . $configuration['inscription'] : '');

	return $url;
}

/**
 * @param array $tableau
 * @param       $configuration
 *
 * @return array
 */
function owm_complement2infos($tableau, $configuration) {
	// Aucune donnée à rajouter en complément au tableau initialisé
	// TODO : remplir le nom du pays à partir du code ISO 3166-1 alpha 2.
	return $tableau;
}

/**
 * Complète par des données spécifiques au service le tableau des conditions issu
 * uniquement de la lecture du flux.
 *
 * @api
 *
 * @param array $tableau
 *        Tableau standardisé des conditions contenant uniquement les données fournies sans traitement
 *        par le service.
 * @param array $configuration
 *        Configuration complète du service, statique et utilisateur.
 *
 * @return array
 *        Tableau standardisé des conditions météorologiques complété par les données spécifiques
 *        du service.
 */
function owm_complement2conditions($tableau, $configuration) {

	if ($tableau) {
		// Calcul de la température ressentie et de la direction du vent (16 points), celles-ci
		// n'étant pas fournie nativement par owm
		include_spip('inc/rainette_convertir');
		$tableau['temperature_ressentie'] = temperature2ressenti($tableau['temperature_reelle'], $tableau['vitesse_vent']);
		$tableau['direction_vent'] = angle2direction($tableau['angle_vent']);

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_owm($tableau, $configuration);
	}

	return $tableau;
}


/**
 * Complète par des données spécifiques au service le tableau des conditions issu
 * uniquement de la lecture du flux.
 *
 * @api
 *
 * @param array $tableau
 *        Tableau standardisé des conditions contenant uniquement les données fournies sans traitement
 *        par le service.
 * @param array $configuration
 *        Configuration complète du service, statique et utilisateur.
 * @param int   $index_periode
 *        Index où trouver et ranger les données. Cet index n'est pas utilisé pour les conditions
 *
 * @return array
 *        Tableau standardisé des conditions météorologiques complété par les données spécifiques
 *        du service.
 */
function owm_complement2previsions($tableau, $configuration, $index_periode) {

	if (($tableau) and ($index_periode > -1)) {

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_owm($tableau, $configuration);
	}

	return $tableau;
}


/**
 * ---------------------------------------------------------------------------------------------
 * Les fonctions qui suivent sont des utilitaires utilisés uniquement appelées par les fonctions
 * de l'API.
 * PACKAGE SPIP\RAINETTE\OWM\OUTILS
 * ---------------------------------------------------------------------------------------------
 */

/**
 * Calcule les états en fonction des états météorologiques natifs fournis par le service.
 *
 * @internal
 *
 * @param array $tableau
 *        Tableau standardisé des conditions contenant uniquement les données fournies sans traitement
 *        par le service. Le tableau est mis à jour et renvoyé à l'appelant.
 * @param array $configuration
 *        Configuration complète du service, statique et utilisateur.
 *
 * @return void
 */
function etat2resume_owm(&$tableau, $configuration) {

	if ($tableau['code_meteo'] and $tableau['icon_meteo']) {
		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service le nom du fichier icone finit par "d" pour le jour et
		// par "n" pour la nuit.
		$icone = $tableau['icon_meteo'];
		if (strpos($icone, 'n') === false) {
			// C'est le jour
			$tableau['periode'] = 0;
		} else {
			// C'est la nuit
			$tableau['periode'] = 1;
		}

		// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
		if ($configuration['condition'] == 'owm') {
			// On affiche les conditions natives fournies par le service.
			// Celles-ci etant deja traduites dans la bonne langue on stocke le texte exact retourne par l'API
			$tableau['icone']['code'] = $tableau['code_meteo'];
			$url = _RAINETTE_OWM_URL_BASE_ICONE . '/' . $tableau['icon_meteo'] . '.png';
			$tableau['icone']['url'] = copie_locale($url);
			$tableau['resume'] = ucfirst($tableau['desc_meteo']);
		} else {
			// On affiche les conditions traduites dans le systeme weather.com
			// Pour le resume on stocke le code et non la traduction pour eviter de generer
			// un cache par langue comme pour le mode natif. La traduction est faite via les fichiers de langue
			$meteo = meteo_owm2weather($tableau['code_meteo'], $tableau['periode']);
			$tableau['icone'] = $meteo;
			$tableau['resume'] = $meteo;
		}
	}
}


// TODO : mettre au point le transcodage omw vers weather
function meteo_owm2weather($meteo, $periode = 0) {
	static $owm2weather = array(
		'chanceflurries'  => array(41, 46),
		'chancerain'      => array(39, 45),
		'chancesleet'     => array(39, 45),
		'chancesnow'      => array(41, 46),
		'chancetstorms'   => array(38, 47),
		'clear'           => array(32, 31),
		'cloudy'          => array(26, 26),
		'flurries'        => array(15, 15),
		'fog'             => array(20, 20),
		'hazy'            => array(21, 21),
		'mostlycloudy'    => array(28, 27),
		'mostlysunny'     => array(34, 33),
		'partlycloudy'    => array(30, 29),
		'partlysunny'     => array(28, 27),
		'sleet'           => array(5, 5),
		'rain'            => array(11, 11),
		'snow'            => array(16, 16),
		'sunny'           => array(32, 31),
		'tstorms'         => array(4, 4),
		'thunderstorms'   => array(4, 4),
		'unknown'         => array(4, 4),
		'scatteredclouds' => array(30, 29),
		'overcast'        => array(26, 26)
	);

	$icone = 'na';
	if (array_key_exists($meteo, $owm2weather)) {
		$icone = strval($owm2weather[$meteo][$periode]);
	}

	return $icone;
}

/**
 * @param $langue
 *
 * @return string
 */
function langue2code_owm($langue) {
	static $langue2owm = array(
		'aa'           => array('', ''),      // afar
		'ab'           => array('', ''),      // abkhaze
		'af'           => array('', ''),      // afrikaans
		'am'           => array('', ''),      // amharique
		'an'           => array('', 'sp'),    // aragonais
		'ar'           => array('', ''),      // arabe
		'as'           => array('', ''),      // assamais
		'ast'          => array('', 'sp'),    // asturien - iso 639-2
		'ay'           => array('', ''),      // aymara
		'az'           => array('', 'ru'),    // azeri
		'ba'           => array('', ''),      // bashkir
		'be'           => array('', 'ru'),    // bielorusse
		'ber_tam'      => array('', ''),      // berbère
		'ber_tam_tfng' => array('', ''),      // berbère tifinagh
		'bg'           => array('bg', ''),    // bulgare
		'bh'           => array('', ''),      // langues biharis
		'bi'           => array('', ''),      // bichlamar
		'bm'           => array('', ''),      // bambara
		'bn'           => array('', ''),      // bengali
		'bo'           => array('', ''),      // tibétain
		'br'           => array('', 'fr'),    // breton
		'bs'           => array('', ''),      // bosniaque
		'ca'           => array('', 'sp'),    // catalan
		'co'           => array('', 'fr'),    // corse
		'cpf'          => array('', 'fr'),    // créole réunionais
		'cpf_dom'      => array('', 'sp'),    // créole ???
		'cpf_hat'      => array('', 'fr'),    // créole haïtien
		'cs'           => array('cz', ''),    // tchèque
		'cy'           => array('', 'en'),    // gallois
		'da'           => array('', ''),      // danois
		'de'           => array('de', ''),    // allemand
		'dz'           => array('', ''),      // dzongkha
		'el'           => array('', ''),      // grec moderne
		'en'           => array('en', ''),    // anglais
		'en_hx'        => array('', 'en'),    // anglais hacker
		'en_sm'        => array('', 'en'),    // anglais smurf
		'eo'           => array('', ''),      // esperanto
		'es'           => array('sp', ''),    // espagnol
		'es_co'        => array('', 'sp'),    // espagnol colombien
		'es_mx_pop'    => array('', 'sp'),    // espagnol mexicain
		'et'           => array('', ''),      // estonien
		'eu'           => array('', 'fr'),    // basque
		'fa'           => array('', ''),      // persan (farsi)
		'ff'           => array('', ''),      // peul
		'fi'           => array('fi', ''),    // finnois
		'fj'           => array('', 'en'),    // fidjien
		'fo'           => array('', ''),      // féroïen
		'fon'          => array('', ''),      // fon
		'fr'           => array('fr', ''),    // français
		'fr_sc'        => array('', 'fr'),    // français schtroumpf
		'fr_lpc'       => array('', 'fr'),    // français langue parlée
		'fr_lsf'       => array('', 'fr'),    // français langue des signes
		'fr_spl'       => array('', 'fr'),    // français simplifié
		'fr_tu'        => array('', 'fr'),    // français copain
		'fy'           => array('', 'de'),    // frison occidental
		'ga'           => array('', 'en'),    // irlandais
		'gd'           => array('', 'en'),    // gaélique écossais
		'gl'           => array('', 'sp'),    // galicien
		'gn'           => array('', ''),      // guarani
		'grc'          => array('', ''),      // grec ancien
		'gu'           => array('', ''),      // goudjrati
		'ha'           => array('', ''),      // haoussa
		'hac'          => array('', ''),      // Kurdish-Horami
		'hbo'          => array('', ''),      // hebreu classique ou biblique
		'he'           => array('', ''),      // hébreu
		'hi'           => array('', ''),      // hindi
		'hr'           => array('', ''),      // croate
		'hu'           => array('', ''),      // hongrois
		'hy'           => array('', ''),      // armenien
		'ia'           => array('', ''),      // interlingua (langue auxiliaire internationale)
		'id'           => array('', ''),      // indonésien
		'ie'           => array('', ''),      // interlingue
		'ik'           => array('', ''),      // inupiaq
		'is'           => array('', ''),      // islandais
		'it'           => array('it', ''),    // italien
		'it_fem'       => array('', 'it'),    // italien féminin
		'iu'           => array('', ''),      // inuktitut
		'ja'           => array('', ''),      // japonais
		'jv'           => array('', ''),      // javanais
		'ka'           => array('', ''),      // géorgien
		'kk'           => array('', ''),      // kazakh
		'kl'           => array('', ''),      // groenlandais
		'km'           => array('', ''),      // khmer central
		'kn'           => array('', ''),      // Kannada
		'ko'           => array('', ''),      // coréen
		'ks'           => array('', ''),      // kashmiri
		'ku'           => array('', ''),      // kurde
		'ky'           => array('', ''),      // kirghiz
		'la'           => array('', ''),      // latin
		'lb'           => array('', 'fr'),    // luxembourgeois
		'ln'           => array('', ''),      // lingala
		'lo'           => array('', ''),      // lao
		'lt'           => array('', ''),      // lituanien
		'lu'           => array('', ''),      // luba-katanga
		'lv'           => array('', ''),      // letton
		'man'          => array('', ''),      // mandingue
		'mfv'          => array('', ''),      // manjaque - iso-639-3
		'mg'           => array('', ''),      // malgache
		'mi'           => array('', ''),      // maori
		'mk'           => array('', ''),      // macédonien
		'ml'           => array('', ''),      // malayalam
		'mn'           => array('', ''),      // mongol
		'mo'           => array('', 'ro'),    // moldave ??? normalement c'est ro comme le roumain
		'mos'          => array('', ''),      // moré - iso 639-2
		'mr'           => array('', ''),      // marathe
		'ms'           => array('', ''),      // malais
		'mt'           => array('', 'en'),    // maltais
		'my'           => array('', ''),      // birman
		'na'           => array('', ''),      // nauruan
		'nap'          => array('', 'it'),    // napolitain - iso 639-2
		'ne'           => array('', ''),      // népalais
		'nqo'          => array('', ''),      // n’ko - iso 639-3
		'nl'           => array('nl', ''),    // néerlandais
		'no'           => array('', ''),      // norvégien
		'nb'           => array('', ''),      // norvégien bokmål
		'nn'           => array('', ''),      // norvégien nynorsk
		'oc'           => array('', 'fr'),    // occitan
		'oc_lnc'       => array('', 'fr'),    // occitan languedocien
		'oc_ni'        => array('', 'fr'),    // occitan niçard
		'oc_ni_la'     => array('', 'fr'),    // occitan niçard
		'oc_prv'       => array('', 'fr'),    // occitan provençal
		'oc_gsc'       => array('', 'fr'),    // occitan gascon
		'oc_lms'       => array('', 'fr'),    // occitan limousin
		'oc_auv'       => array('', 'fr'),    // occitan auvergnat
		'oc_va'        => array('', 'fr'),    // occitan vivaro-alpin
		'om'           => array('', ''),      // galla
		'or'           => array('', ''),      // oriya
		'pa'           => array('', ''),      // pendjabi
		'pbb'          => array('', ''),      // Nasa Yuwe (páez) - iso 639-3
		'pl'           => array('pl', ''),    // polonais
		'ps'           => array('', ''),      // pachto
		'pt'           => array('pt', ''),    // portugais
		'pt_br'        => array('', 'pt'),    // portugais brésilien
		'qu'           => array('', ''),      // quechua
		'rm'           => array('', ''),      // romanche
		'rn'           => array('', ''),      // rundi
		'ro'           => array('ro', ''),    // roumain
		'roa'          => array('', 'fr'),    // langues romanes (ch'ti) - iso 639-2
		'ru'           => array('ru', ''),    // russe
		'rw'           => array('', ''),      // rwanda
		'sa'           => array('', ''),      // sanskrit
		'sc'           => array('', 'it'),    // sarde
		'scn'          => array('', 'it'),    // sicilien - iso 639-2
		'sd'           => array('', ''),      // sindhi
		'sg'           => array('', ''),      // sango
		'sh'           => array('', ''),      // serbo-croate
		'sh_latn'      => array('', ''),      // serbo-croate latin
		'sh_cyrl'      => array('', ''),      // serbo-croate cyrillique
		'si'           => array('', ''),      // singhalais
		'sk'           => array('', ''),      // slovaque
		'sl'           => array('', ''),      // slovène
		'sm'           => array('', 'en'),    // samoan
		'sn'           => array('', ''),      // shona
		'so'           => array('', ''),      // somali
		'sq'           => array('', ''),      // albanais
		'sr'           => array('', ''),      // serbe
		'src'          => array('', 'it'),    // sarde logoudorien - iso 639-3
		'sro'          => array('', 'it'),    // sarde campidanien - iso 639-3
		'ss'           => array('', ''),      // swati
		'st'           => array('', ''),      // sotho du Sud
		'su'           => array('', ''),      // soundanais
		'sv'           => array('se', ''),    // suédois
		'sw'           => array('', ''),      // swahili
		'ta'           => array('', ''),      // tamoul
		'te'           => array('', ''),      // télougou
		'tg'           => array('', ''),      // tadjik
		'th'           => array('', ''),      // thaï
		'ti'           => array('', ''),      // tigrigna
		'tk'           => array('', ''),      // turkmène
		'tl'           => array('', ''),      // tagalog
		'tn'           => array('', ''),      // tswana
		'to'           => array('', ''),      // tongan (Îles Tonga)
		'tr'           => array('tr', ''),    // turc
		'ts'           => array('', ''),      // tsonga
		'tt'           => array('', ''),      // tatar
		'tw'           => array('', ''),      // twi
		'ty'           => array('', 'fr'),    // tahitien
		'ug'           => array('', ''),      // ouïgour
		'uk'           => array('ua', ''),    // ukrainien
		'ur'           => array('', ''),      // ourdou
		'uz'           => array('', ''),      // ouszbek
		'vi'           => array('', ''),      // vietnamien
		'vo'           => array('', ''),      // volapük
		'wa'           => array('', 'fr'),    // wallon
		'wo'           => array('', ''),      // wolof
		'xh'           => array('', ''),      // xhosa
		'yi'           => array('', ''),      // yiddish
		'yo'           => array('', ''),      // yoruba
		'za'           => array('', 'zh_cn'), // zhuang
		'zh'           => array('zh_cn', ''), // chinois (ecriture simplifiee)
		'zh_tw'        => array('zh_tw', ''), // chinois taiwan (ecriture traditionnelle)
		'zu'           => array('', '')       // zoulou
	);

	$code = $GLOBALS['rainette_owm_config']['service']['langue_service'];
	if (array_key_exists($langue, $langue2owm)) {
		if ($c0 = $langue2owm[$langue][0]) {
			$code = strtolower($c0);
		} elseif ($c1 = $langue2owm[$langue][1]) {
			$code = strtolower($c1);
		}
	}

	return $code;
}
