<?php
/**
 * Ce fichier contient la configuration et l'ensemble des fonctions implémentant le service World Weather Online (wwo).
 * Ce service est capable de fournir des données au format XML ou JSON. Néanmoins, l'API actuelle du plugin utilise
 * uniquemement le format JSON.
 *
 * @package SPIP\RAINETTE\SERVICES\WWO
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_WWO_URL_BASE')) {
	define('_RAINETTE_WWO_URL_BASE', 'http://api.worldweatheronline.com/free/v2/weather.ashx');
}


// Configuration des valeurs par défaut des éléments de la configuration dynamique.
// Ces valeurs sont applicables à tous les modes.
$GLOBALS['rainette_wwo_config']['service'] = array(
	'defauts'        => array(
		'inscription' => '',
		'unite'       => 'm',
		'condition'   => 'wwo',
		'theme'       => '',
	),
	'credits'        => array(
		'titre' => 'Free local weather content provider',
		'logo'  => null,
		'lien'  => 'http://www.worldweatheronline.com/',
	),
	'langue_service' => 'en',
);

// Configuration des données fournies par le service wwo pour le mode 'infos' en format JSON.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wwo_config']['infos'] = array(
	'periode_maj' => 86400,
	'format_flux' => 'json',
	'cle_base'    => array('data', 'nearest_area', 0),
	'donnees'     => array(
		// Lieu
		'ville'     => array('cle' => array('areaName', 0, 'value')),
		'pays'      => array('cle' => array('country', 0, 'value')),
		'pays_iso2' => array('cle' => array()),
		'region'    => array('cle' => array('region', 0, 'value')),
		// Coordonnées
		'longitude' => array('cle' => array('longitude')),
		'latitude'  => array('cle' => array('latitude')),
		// Informations complémentaires : aucune configuration car ce sont des données calculées
	),
);

// Configuration des données fournies par le service wwo pour le mode 'conditions'.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wwo_config']['conditions'] = array(
	'periode_maj' => 10800,
	'format_flux' => 'json',
	'cle_base'    => array('data', 'current_condition', 0),
	'donnees'     => array(
		// Données d'observation
		'derniere_maj'          => array('cle' => array('localObsDateTime')),
		'station'               => array('cle' => array()),
		// Températures
		'temperature_reelle'    => array('cle' => array('temp_'), 'suffixe' => array('id_cle' => 0, 'm' => 'C', 's' => 'F')),
		'temperature_ressentie' => array('cle' => array('FeelsLike'), 'suffixe' => array('id_cle' => 0, 'm' => 'C', 's' => 'F')),
		// Données anémométriques
		'vitesse_vent'          => array('cle' => array('windspeed'), 'suffixe' => array('id_cle' => 0, 'm' => 'Kmph', 's' => 'Miles')),
		'angle_vent'            => array('cle' => array('winddirDegree')),
		'direction_vent'        => array('cle' => array('winddir16Point')),
		// Données atmosphériques : risque_uv est calculé
		'precipitation'         => array('cle' => array('precipMM')),
		'humidite'              => array('cle' => array('humidity')),
		'point_rosee'           => array('cle' => array()),
		'pression'              => array('cle' => array('pressure')),
		'tendance_pression'     => array('cle' => array()),
		'visibilite'            => array('cle' => array('visibility')),
		'indice_uv'             => array('cle' => array()),
		// Etats météorologiques natifs
		'code_meteo'            => array('cle' => array('weatherCode')),
		'icon_meteo'            => array('cle' => array('weatherIconUrl', 0, 'value')),
		'desc_meteo'            => array('cle' => array('weatherDesc', 0, 'value')),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);

// Configuration des données fournies par le service wwo pour le mode 'conditions'.
// -- L'API gratuite fournit 5 jours de prévisions alors que l'API Premium fournit 15 jours
//    de prévisions. On utilise donc le max des deux.
// -- Seules les données non calculées sont configurées.
$GLOBALS['rainette_wwo_config']['previsions'] = array(
	'periodicites'       => array(
		24 => array('max_jours' => 15),
		12 => array('max_jours' => 15),
		6  => array('max_jours' => 15),
		3  => array('max_jours' => 15),
		1  => array('max_jours' => 15)
	),
	'periodicite_defaut' => 24,
	'periode_maj'        => 14400,
	'format_flux'        => 'json',
	'cle_base'           => array('data', 'weather'),
	'cle_heure'          => array('hourly'),
	'structure_heure'    => true,
	'donnees'            => array(
		// Données d'observation
		'date'                 => array('cle' => array('date')),
		'heure'                => array('cle' => array('time')),
		// Données astronomiques
		'lever_soleil'         => array('cle' => array('astronomy', 0, 'sunrise')),
		'coucher_soleil'       => array('cle' => array('astronomy', 0, 'sunset')),
		// Températures
		'temperature'          => array('cle' => array('temp'), 'suffixe' => array('id_cle' => 0, 'm' => 'C', 's' => 'F')),
		'temperature_max'      => array('cle' => array('maxtemp'), 'suffixe' => array('id_cle' => 0, 'm' => 'C', 's' => 'F')),
		'temperature_min'      => array('cle' => array('mintemp'), 'suffixe' => array('id_cle' => 0, 'm' => 'C', 's' => 'F')),
		// Données anémométriques
		'vitesse_vent'         => array('cle' => array('windspeed'), 'suffixe' => array('id_cle' => 0, 'm' => 'Kmph', 's' => 'Miles')),
		'angle_vent'           => array('cle' => array('winddirDegree')),
		'direction_vent'       => array('cle' => array('winddir16Point')),
		// Données atmosphériques : risque_uv est calculé
		'risque_precipitation' => array('cle' => array('chanceofrain')),
		'precipitation'        => array('cle' => array('precipMM')),
		'humidite'             => array('cle' => array('humidity')),
		'point_rosee'          => array('cle' => array('DewPoint'), 'suffixe' => array('id_cle' => 0, 'm' => 'C', 's' => 'F')),
		'pression'             => array('cle' => array('pressure')),
		'visibilite'           => array('cle' => array('visibility')),
		'indice_uv'            => array('cle' => array('uvIndex')),
		// Etats météorologiques natifs
		'code_meteo'           => array('cle' => array('weatherCode')),
		'icon_meteo'           => array('cle' => array('weatherIconUrl', 0, 'value')),
		'desc_meteo'           => array('cle' => array('weatherDesc', 0, 'value')),
		// Etats météorologiques calculés : icone, resume, periode sont calculés
	),
);


// ------------------------------------------------------------------------------------------------
// Les fonctions qui suivent définissent l'API standard du service et sont appelées par la fonction
// unique de chargement des données météorologiques `charger_meteo()`.
// ------------------------------------------------------------------------------------------------

/**
 * Fournit la configuration statique du service pour le type de données requis.
 *
 * @api
 *
 * @param string $mode
 *        Type de données météorologiques. Les valeurs possibles sont `infos`, `conditions` ou `previsions`.
 *        La périodicité n'est pas nécessaire car la configuration est indifférente à ce paramètre.
 *
 * @return array
 *        Le tableau des données de configuration communes au service et propres au type de données demandé.
 */
function wwo_service2configuration($mode) {
	// On merge la configuration propre au mode et la configuration du service proprement dite
	// composée des valeurs par défaut de la configuration utilisateur et de paramètres généraux.
	$config = array_merge($GLOBALS['rainette_wwo_config'][$mode], $GLOBALS['rainette_wwo_config']['service']);

	return $config;
}


/**
 * Construit le nom du cache en fonction du lieu, du type de données et de la langue utilisée par le site.
 *
 * @api
 *
 * @param string $lieu
 *        Lieu pour lequel on requiert le nom du cache.
 * @param string $mode
 *        Type de données météorologiques. Les valeurs possibles sont `infos`, `conditions` ou `previsions`.
 * @param int    $periodicite
 *        La périodicité horaire des prévisions :
 *        - `24`, `12`, `6`, `3` ou `1`, pour le mode `previsions`
 *        - `0`, pour les modes `conditions` et `infos`
 * @param array  $configuration
 *        Configuration complète du service, statique et utilisateur.
 *
 * @return string
 *        Chemin complet du fichier cache.
 */
function wwo_service2cache($lieu, $mode, $periodicite, $configuration) {

	// Identification de la langue du resume.
	$code_langue = ($configuration['condition'] == 'wwo')
		? langue2code_wwo($GLOBALS['spip_lang'])
		: $configuration['langue_service'];

	// Construction du chemin du fichier cache
	include_spip('inc/rainette_normaliser');
	$fichier_cache = normaliser_cache('wwo', $lieu, $mode, $periodicite, $code_langue);

	return $fichier_cache;
}


/**
 * Construit l'url de la requête correspondant au lieu, au type de données et à la configuration utilisateur
 * du service (par exemple, le code d'inscription, le format des résultats...).
 *
 * @api
 * @uses langue2code_wwo()
 *
 * @param string $lieu
 *        Lieu pour lequel on acquiert les données météorologiques.
 * @param string $mode
 *        Type de données météorologiques. Les valeurs possibles sont `infos`, `conditions` ou `previsions`.
 * @param int    $periodicite
 *        La périodicité horaire des prévisions :
 *        - `24`, `12`, `6`, `3` ou `1`, pour le mode `previsions`
 *        - `0`, pour les modes `conditions` et `infos`
 * @param array  $configuration
 *        Configuration complète du service, statique et utilisateur.
 *
 * @return string
 *        URL complète de la requête.
 */
function wwo_service2url($lieu, $mode, $periodicite, $configuration) {

	// Identification de la langue du resume.
	$code_langue = ($configuration['condition'] == 'wwo')
		? langue2code_wwo($GLOBALS['spip_lang'])
		: $configuration['langue_service'];

	// On normalise le lieu et on récupère son format.
	// Le service accepte la format ville,pays, le format latitude,longitude et le format adresse IP.
	// Néanmoins, la query a toujours la même forme; il n'est donc pas nécessaire de gérer le format.
	include_spip('inc/rainette_normaliser');
	list($lieu_normalise,) = normaliser_lieu($lieu);

	$url = _RAINETTE_WWO_URL_BASE
		   . '?key=' . $configuration['inscription']
		   . '&format=' . $configuration['format_flux']
		   . '&extra=localObsTime'
		   . '&lang=' . $code_langue
		   . '&q=' . $lieu_normalise;

	if ($mode == 'infos') {
		$url .= '&includeLocation=yes&cc=no&fx=no';
	} elseif ($mode == 'conditions') {
		$url .= '&cc=yes&fx=no';
	} else {
		$url .= '&cc=no&fx=yes'
				. '&num_of_days=' . $configuration['periodicites'][$periodicite]['max_jours']
				. '&tp=' . strval($periodicite);
	}

	return $url;
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
function wwo_complement2conditions($tableau, $configuration) {

	if ($tableau) {
		// Convertir les informations exprimées en système métrique dans le systeme US si la
		// configuration le demande
		if ($configuration['unite'] == 's') {
			metrique2imperial_wwo($tableau);
		}

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_wwo($tableau, $configuration);
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
function wwo_complement2previsions($tableau, $configuration, $index_periode) {

	if (($tableau) and ($index_periode > -1)) {
		// Convertir les informations exprimées en système métrique dans le systeme US si la
		// configuration le demande
		if ($configuration['unite'] == 's') {
			metrique2imperial_wwo($tableau);
		}

		// Compléter le tableau standard avec les états météorologiques calculés
		etat2resume_wwo($tableau, $configuration);
	}

	return $tableau;
}


// ---------------------------------------------------------------------------------------------
// Les fonctions qui suivent sont des utilitaires utilisés uniquement appelées par les fonctions
// de l'API.
// PACKAGE SPIP\RAINETTE\WWO\OUTILS
// ---------------------------------------------------------------------------------------------

/**
 * @param array $tableau
 *
 * @return void
 */
function metrique2imperial_wwo(&$tableau) {
	include_spip('inc/rainette_convertir');

	// Seules la température, la température ressentie et la vitesse du vent sont fournies dans
	// les deux systèmes.
	// Etant donnée que les tableaux sont normalisés, ils contiennent toujours les index de chaque
	// donnée météo, il est donc inutile de tester leur existence.
	$tableau['visibilite'] = ($tableau['visibilite'])
		? kilometre2mile($tableau['visibilite'])
		: '';
	$tableau['pression'] = ($tableau['pression'])
		? millibar2inch($tableau['pression'])
		: '';
	$tableau['precipitation'] = ($tableau['precipitation'])
		? millimetre2inch($tableau['precipitation'])
		: '';
}


function etat2resume_wwo(&$tableau, $configuration) {

	if ($tableau['code_meteo'] and $tableau['icon_meteo']) {
		// Determination de l'indicateur jour/nuit qui permet de choisir le bon icone
		// Pour ce service aucun indicateur n'est disponible
		// -> on utilise le nom de l'icone qui contient l'indication "night" pour la nuit
		$icone = basename($tableau['icon_meteo']);
		if (strpos($icone, '_night') === false) {
			// C'est le jour
			$tableau['periode'] = 0;
		} else {
			// C'est la nuit
			$tableau['periode'] = 1;
		}

		// Determination, suivant le mode choisi, du code, de l'icone et du resume qui seront affiches
		if ($configuration['condition'] == 'wwo') {
			// On affiche les conditions natives fournies par le service.
			// TODO le dessous est faux : c'est désormais disponible
			// Pour le resume, wwo ne fournit pas de traduction : on stocke donc le code meteo afin
			// de le traduire à partir des fichiers de langue SPIP.
			$tableau['icone']['code'] = $tableau['code_meteo'];
			$tableau['icone']['url'] = copie_locale($tableau['icon_meteo']);
			$tableau['resume'] = $tableau['code_meteo'];
		} else {
			// On affiche les conditions traduites dans le systeme weather.com
			$meteo = meteo_wwo2weather($tableau['code_meteo'], $tableau['periode']);
			$tableau['icone'] = $meteo;
			$tableau['resume'] = $meteo;
		}
	}
}

/**
 * @param $langue
 *
 * @return string
 */
function langue2code_wwo($langue) {
	static $langue2wwo = array(
		'aa'           => array('', ''),      // afar
		'ab'           => array('', ''),      // abkhaze
		'af'           => array('', ''),      // afrikaans
		'am'           => array('', ''),      // amharique
		'an'           => array('', 'es'),    // aragonais
		'ar'           => array('ar', ''),    // arabe
		'as'           => array('', ''),      // assamais
		'ast'          => array('', 'es'),    // asturien - iso 639-2
		'ay'           => array('', ''),      // aymara
		'az'           => array('', ''),      // azeri
		'ba'           => array('', ''),      // bashkir
		'be'           => array('', ''),      // bielorusse
		'ber_tam'      => array('', ''),      // berbère
		'ber_tam_tfng' => array('', ''),      // berbère tifinagh
		'bg'           => array('bg', ''),    // bulgare
		'bh'           => array('', ''),      // langues biharis
		'bi'           => array('', ''),      // bichlamar
		'bm'           => array('', ''),      // bambara
		'bn'           => array('bn', ''),    // bengali
		'bo'           => array('', ''),      // tibétain
		'br'           => array('', 'fr'),    // breton
		'bs'           => array('', ''),      // bosniaque
		'ca'           => array('', 'es'),    // catalan
		'co'           => array('', 'fr'),    // corse
		'cpf'          => array('', 'fr'),    // créole réunionais
		'cpf_dom'      => array('', 'fr'),    // créole ???
		'cpf_hat'      => array('', ''),      // créole haïtien
		'cs'           => array('cs', ''),    // tchèque
		'cy'           => array('', 'en'),    // gallois
		'da'           => array('', ''),      // danois
		'de'           => array('de', ''),    // allemand
		'dz'           => array('', ''),      // dzongkha
		'el'           => array('el', ''),    // grec moderne
		'en'           => array('en', ''),    // anglais
		'en_hx'        => array('', 'en'),    // anglais hacker
		'en_sm'        => array('', 'en'),    // anglais smurf
		'eo'           => array('', ''),      // esperanto
		'es'           => array('es', ''),    // espagnol
		'es_co'        => array('', 'es'),    // espagnol colombien
		'es_mx_pop'    => array('', 'es'),    // espagnol mexicain
		'et'           => array('', ''),      // estonien
		'eu'           => array('', ''),      // basque
		'fa'           => array('', ''),      // persan (farsi)
		'ff'           => array('', ''),      // peul
		'fi'           => array('fi', ''),    // finnois
		'fj'           => array('', ''),      // fidjien
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
		'gl'           => array('', 'es'),    // galicien
		'gn'           => array('', ''),      // guarani
		'grc'          => array('', 'el'),    // grec ancien
		'gu'           => array('', ''),      // goudjrati
		'ha'           => array('', ''),      // haoussa
		'hac'          => array('', ''),      // Kurdish-Horami
		'hbo'          => array('', ''),      // hebreu classique ou biblique
		'he'           => array('', ''),      // hébreu
		'hi'           => array('hi', ''),    // hindi
		'hr'           => array('', ''),      // croate
		'hu'           => array('hu', ''),    // hongrois
		'hy'           => array('', ''),      // armenien
		'ia'           => array('', ''),      // interlingua (langue auxiliaire internationale)
		'id'           => array('', ''),      // indonésien
		'ie'           => array('', ''),      // interlingue
		'ik'           => array('', ''),      // inupiaq
		'is'           => array('', ''),      // islandais
		'it'           => array('it', ''),    // italien
		'it_fem'       => array('', 'it'),    // italien féminin
		'iu'           => array('', ''),      // inuktitut
		'ja'           => array('ja', ''),    // japonais
		'jv'           => array('jv', ''),    // javanais
		'ka'           => array('', ''),      // géorgien
		'kk'           => array('', ''),      // kazakh
		'kl'           => array('', ''),      // groenlandais
		'km'           => array('', ''),      // khmer central
		'kn'           => array('', ''),      // Kannada
		'ko'           => array('ko', ''),    // coréen
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
		'mr'           => array('mr', ''),    // marathe
		'ms'           => array('', ''),      // malais
		'mt'           => array('', ''),      // maltais
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
		'pa'           => array('pa', ''),    // pendjabi
		'pbb'          => array('', ''),      // Nasa Yuwe (páez) - iso 639-3
		'pl'           => array('pl', ''),    // polonais
		'ps'           => array('', ''),      // pachto
		'pt'           => array('pt', ''),    // portugais
		'pt_br'        => array('', 'pt'),    // portugais brésilien
		'qu'           => array('', 'es'),    // quechua
		'rm'           => array('', 'fr'),    // romanche
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
		'sh'           => array('', 'sr'),    // serbo-croate
		'sh_latn'      => array('', 'sr'),    // serbo-croate latin
		'sh_cyrl'      => array('', 'sr'),    // serbo-croate cyrillique
		'si'           => array('si', ''),    // singhalais
		'sk'           => array('sk', ''),    // slovaque
		'sl'           => array('', ''),      // slovène
		'sm'           => array('', ''),      // samoan
		'sn'           => array('', ''),      // shona
		'so'           => array('', ''),      // somali
		'sq'           => array('', ''),      // albanais
		'sr'           => array('sr', ''),    // serbe
		'src'          => array('', 'it'),    // sarde logoudorien - iso 639-3
		'sro'          => array('', 'it'),    // sarde campidanien - iso 639-3
		'ss'           => array('', ''),      // swati
		'st'           => array('', ''),      // sotho du Sud
		'su'           => array('', ''),      // soundanais
		'sv'           => array('sv', ''),    // suédois
		'sw'           => array('', ''),      // swahili
		'ta'           => array('ta', ''),    // tamoul
		'te'           => array('te', ''),    // télougou
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
		'uk'           => array('uk', ''),    // ukrainien
		'ur'           => array('ur', ''),    // ourdou
		'uz'           => array('', ''),      // ouszbek
		'vi'           => array('vi', ''),    // vietnamien
		'vo'           => array('', ''),      // volapük
		'wa'           => array('', 'fr'),    // wallon
		'wo'           => array('', ''),      // wolof
		'xh'           => array('', ''),      // xhosa
		'yi'           => array('', ''),      // yiddish
		'yo'           => array('', ''),      // yoruba
		'za'           => array('', 'zh'),    // zhuang
		'zh'           => array('zh', ''),    // chinois (ecriture simplifiee)
		'zh_tw'        => array('zh_tw', ''), // chinois taiwan (ecriture traditionnelle)
		'zu'           => array('zu', '')     // zoulou
	);

	$code = $GLOBALS['rainette_wwo_config']['service']['langue_service'];
	if (array_key_exists($langue, $langue2wwo)) {
		if ($c0 = $langue2wwo[$langue][0]) {
			$code = strtolower($c0);
		} elseif ($c1 = $langue2wwo[$langue][1]) {
			$code = strtolower($c1);
		}
	}

	return $code;
}


/**
 * @internal
 *
 * @link http://plugins.trac.wordpress.org/browser/weather-and-weather-forecast-widget/trunk/gg_funx_.php
 * Transcodage issu du plugin Wordpress weather forecast.
 *
 * @param string $meteo
 * @param int    $periode
 *
 * @return string
 */
function meteo_wwo2weather($meteo, $periode = 0) {
	static $wwo2weather = array(
		395 => array(41, 46),
		392 => array(41, 46),
		389 => array(38, 47),
		386 => array(37, 47),
		377 => array(6, 6),
		374 => array(6, 6),
		371 => array(14, 14),
		368 => array(13, 13),
		365 => array(6, 6),
		362 => array(6, 6),
		359 => array(11, 11),
		356 => array(11, 11),
		353 => array(9, 9),
		350 => array(18, 18),
		338 => array(16, 16),
		335 => array(16, 16),
		332 => array(14, 14),
		329 => array(14, 14),
		326 => array(13, 13),
		323 => array(13, 13),
		320 => array(18, 18),
		317 => array(18, 18),
		314 => array(8, 8),
		311 => array(8, 8),
		308 => array(40, 40),
		305 => array(39, 45),
		302 => array(11, 11),
		299 => array(39, 45),
		296 => array(9, 9),
		293 => array(9, 9),
		284 => array(10, 10),
		281 => array(9, 9),
		266 => array(9, 9),
		263 => array(9, 9),
		260 => array(20, 20),
		248 => array(20, 20),
		230 => array(16, 16),
		227 => array(15, 15),
		200 => array(38, 47),
		185 => array(10, 10),
		182 => array(18, 18),
		179 => array(16, 16),
		176 => array(40, 49),
		143 => array(20, 20),
		122 => array(26, 26),
		119 => array(28, 27),
		116 => array(30, 29),
		113 => array(32, 31)
	);

	$icone = 'na';
	if (array_key_exists($meteo, $wwo2weather)) {
		$icone = strval($wwo2weather[$meteo][$periode]);
	}

	return $icone;
}
