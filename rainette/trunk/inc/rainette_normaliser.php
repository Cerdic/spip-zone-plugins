<?php
/**
 * Ce fichier contient les fonctions internes destinées à standardiser les données météorologiques.
 *
 * @package SPIP\RAINETTE\MASHUP
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_REGEXP_LIEU_IP')) {
	/**
	 * Regexp permettant de reconnaître un lieu au format adresse IP
	 */
	define('_RAINETTE_REGEXP_LIEU_IP', '#(?:\d{1,3}\.){3}\d{1,3}#');
}
if (!defined('_RAINETTE_REGEXP_LIEU_COORDONNEES')) {
	/**
	 * Regexp permettant de reconnaître un lieu au format coordonnées géographiques latitude,longitude
	 */
	define('_RAINETTE_REGEXP_LIEU_COORDONNEES', '#([\-\+]?\d+(?:\.\d+)?)\s*,\s*([\-\+]?\d+(?:\.\d+)?)#');
}
if (!defined('_RAINETTE_REGEXP_LIEU_WEATHER_ID')) {
	/**
	 * Regexp permettant de reconnaître un lieu au format Weather ID
	 */
	define('_RAINETTE_REGEXP_LIEU_WEATHER_ID', '#[a-zA-Z]{4}\d{4}#i');
}

$GLOBALS['rainette_config']['erreurs'] = array(
	'code'    => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_erreur'),
	'message' => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_erreur'),
);

$GLOBALS['rainette_config']['infos'] = array(
	// Lieu
	'ville'     => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_lieu'),
	'pays'      => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_lieu'),
	'pays_iso2' => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_lieu'),
	'region'    => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_lieu'),
	// Coordonnées géographiques
	'longitude' => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'angle', 'groupe' => 'donnees_coordonnees'),
	'latitude'  => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'angle', 'groupe' => 'donnees_coordonnees'),
);

$GLOBALS['rainette_config']['conditions'] = array(
	// Données d'observation
	'derniere_maj'          => array('origine' => 'service', 'type_php' => 'date', 'type_unite' => '', 'groupe' => 'donnees_observation'),
	'station'               => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_observation'),
	// Températures
	'temperature_reelle'    => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'temperature', 'groupe' => 'donnees_temperatures'),
	'temperature_ressentie' => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'temperature', 'groupe' => 'donnees_temperatures'),
	// Données anémométriques
	'vitesse_vent'          => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'vitesse', 'groupe' => 'donnees_anemometriques'),
	'angle_vent'            => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'angle', 'groupe' => 'donnees_anemometriques'),
	'direction_vent'        => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_anemometriques'),
	// Données atmosphériques
	'precipitation'         => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'precipitation', 'groupe' => 'donnees_atmospheriques'),
	'humidite'              => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'pourcentage', 'groupe' => 'donnees_atmospheriques'),
	'point_rosee'           => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'temperature', 'groupe' => 'donnees_atmospheriques'),
	'pression'              => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'pression', 'groupe' => 'donnees_atmospheriques'),
	'tendance_pression'     => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_atmospheriques'),
	'visibilite'            => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'distance', 'groupe' => 'donnees_atmospheriques'),
	'indice_uv'             => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'indice', 'groupe' => 'donnees_atmospheriques'),
	'risque_uv'             => array('origine' => 'calcul', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_atmospheriques'),
	// Etats météorologiques natifs
	'code_meteo'            => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_etats_natifs'),
	'icon_meteo'            => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_etats_natifs'),
	'desc_meteo'            => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_etats_natifs'),
	'trad_meteo'            => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'groupe' => 'donnees_etats_natifs'),
	// Etats météorologiques calculés
	'icone'                 => array('origine' => 'calcul', 'type_php' => 'mixed', 'type_unite' => '', 'groupe' => 'donnees_etats_calcules'),
	'resume'                => array('origine' => 'calcul', 'type_php' => 'mixed', 'type_unite' => '', 'groupe' => 'donnees_etats_calcules'),
	'periode'               => array('origine' => 'calcul', 'type_php' => 'int', 'type_unite' => '', 'groupe' => 'donnees_etats_calcules'),
);

$GLOBALS['rainette_config']['previsions'] = array(
	// Données d'observation
	'date'                 => array('origine' => 'service', 'type_php' => 'date', 'type_unite' => '', 'rangement' => 'jour', 'groupe' => 'donnees_observation'),
	'heure'                => array('origine' => 'service', 'type_php' => 'heure', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_observation'),
	// Données astronomiques
	'lever_soleil'         => array('origine' => 'service', 'type_php' => 'date', 'type_unite' => '', 'rangement' => 'jour', 'groupe' => 'donnees_astronomiques'),
	'coucher_soleil'       => array('origine' => 'service', 'type_php' => 'date', 'type_unite' => '', 'rangement' => 'jour', 'groupe' => 'donnees_astronomiques'),
	// Températures
	'temperature'          => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'temperature', 'rangement' => 'heure', 'groupe' => 'donnees_temperatures'),
	'temperature_max'      => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'temperature', 'rangement' => 'jour', 'groupe' => 'donnees_temperatures'),
	'temperature_min'      => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'temperature', 'rangement' => 'jour', 'groupe' => 'donnees_temperatures'),
	// Données anémométriques
	'vitesse_vent'         => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'vitesse', 'rangement' => 'heure', 'groupe' => 'donnees_anemometriques'),
	'angle_vent'           => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'angle', 'rangement' => 'heure', 'groupe' => 'donnees_anemometriques'),
	'direction_vent'       => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_anemometriques'),
	// Données atmosphériques
	'risque_precipitation' => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'pourcentage', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'precipitation'        => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'precipitation', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'humidite'             => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'pourcentage', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'point_rosee'          => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'temperature', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'pression'             => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'pression', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'visibilite'           => array('origine' => 'service', 'type_php' => 'float', 'type_unite' => 'distance', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'indice_uv'            => array('origine' => 'service', 'type_php' => 'int', 'type_unite' => 'indice', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	'risque_uv'            => array('origine' => 'calcul', 'type_php' => 'string', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_atmospheriques'),
	// Etats météorologiques natifs
	'code_meteo'           => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_natifs'),
	'icon_meteo'           => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_natifs'),
	'desc_meteo'           => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_natifs'),
	'trad_meteo'           => array('origine' => 'service', 'type_php' => 'string', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_natifs'),
	// Etats météorologiques calculés
	'icone'                => array('origine' => 'calcul', 'type_php' => 'mixed', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_calcules'),
	'resume'               => array('origine' => 'calcul', 'type_php' => 'mixed', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_calcules'),
	'periode'              => array('origine' => 'calcul', 'type_php' => 'int', 'type_unite' => '', 'rangement' => 'heure', 'groupe' => 'donnees_etats_calcules'),
);

$GLOBALS['rainette_config']['periodicite'] = array(
	24 => array(24, 12),
	12 => array(12),
	1  => array(1, 3, 6)
);

$GLOBALS['rainette_config']['langues_alternatives'] = array(
	'aa'           => array(),           // afar
	'ab'           => array(),           // abkhaze
	'af'           => array('en'),       // afrikaans
	'am'           => array(),           // amharique
	'an'           => array('es'),       // aragonais
	'ar'           => array(),           // arabe
	'as'           => array(),           // assamais
	'ast'          => array('es'),       // asturien - iso 639-2
	'ay'           => array(),           // aymara
	'az'           => array('ru'),       // azeri
	'ba'           => array(),           // bashkir
	'be'           => array('ru'),       // bielorusse
	'ber_tam'      => array('ar'),       // berbère
	'ber_tam_tfng' => array('ar'),       // berbère tifinagh
	'bg'           => array(),           // bulgare
	'bh'           => array(),           // langues biharis
	'bi'           => array(),           // bichlamar
	'bm'           => array(),           // bambara
	'bn'           => array(),           // bengali
	'bo'           => array(),           // tibétain
	'br'           => array('fr'),       // breton
	'bs'           => array(),           // bosniaque
	'ca'           => array('es'),       // catalan
	'co'           => array('fr'),       // corse
	'cpf'          => array('fr'),       // créole réunionais
	'cpf_dom'      => array('es'),       // créole ???
	'cpf_hat'      => array('fr'),       // créole haïtien
	'cs'           => array(),           // tchèque
	'cy'           => array('en'),       // gallois
	'da'           => array(),           // danois
	'de'           => array(),           // allemand
	'dz'           => array(),           // dzongkha
	'el'           => array(),           // grec moderne
	'en'           => array(),           // anglais
	'en_hx'        => array('en'),       // anglais hacker
	'en_sm'        => array('en'),       // anglais smurf
	'eo'           => array(),           // esperanto
	'es'           => array(),           // espagnol
	'es_co'        => array('es'),       // espagnol colombien
	'es_mx_pop'    => array('es'),       // espagnol mexicain
	'et'           => array(),           // estonien
	'eu'           => array('fr'),       // basque
	'fa'           => array(),           // persan (farsi)
	'ff'           => array(),           // peul
	'fi'           => array('sv'),       // finnois
	'fj'           => array('en'),       // fidjien
	'fo'           => array('da'),       // féroïen
	'fon'          => array(),           // fon
	'fr'           => array(),           // français
	'fr_fem'       => array('fr'),       // français féminin
	'fr_sc'        => array('fr'),       // français schtroumpf
	'fr_lpc'       => array('fr'),       // français langue parlée
	'fr_lsf'       => array('fr'),       // français langue des signes
	'fr_spl'       => array('fr'),       // français simplifié
	'fr_tu'        => array('fr'),       // français copain
	'fy'           => array('de'),       // frison occidental
	'ga'           => array('en'),       // irlandais
	'gd'           => array('en'),       // gaélique écossais
	'gl'           => array('es'),       // galicien
	'gn'           => array(),           // guarani
	'grc'          => array('el'),       // grec ancien
	'gu'           => array(),           // goudjrati
	'ha'           => array(),           // haoussa
	'hac'          => array('ku'),       // Kurdish-Horami
	'hbo'          => array('il'),       // hebreu classique ou biblique
	'he'           => array(),           // hébreu
	'hi'           => array(),           // hindi
	'hr'           => array(),           // croate
	'hu'           => array(),           // hongrois
	'hy'           => array(),           // armenien
	'ia'           => array(),           // interlingua (langue auxiliaire internationale)
	'id'           => array(),           // indonésien
	'ie'           => array(),           // interlingue
	'ik'           => array(),           // inupiaq
	'is'           => array(),           // islandais
	'it'           => array(),           // italien
	'it_fem'       => array('it'),       // italien féminin
	'iu'           => array(),           // inuktitut
	'ja'           => array(),           // japonais
	'jv'           => array(),           // javanais
	'ka'           => array(),           // géorgien
	'kk'           => array(),           // kazakh
	'kl'           => array('da'),       // groenlandais
	'km'           => array(),           // khmer central
	'kn'           => array(),           // Kannada
	'ko'           => array(),           // coréen
	'kok'          => array(),           // konkani (macrolangage)
	'ks'           => array(),           // kashmiri
	'ku'           => array(),           // kurde
	'ky'           => array(),           // kirghiz
	'la'           => array('fr'),       // latin
	'lb'           => array('fr'),       // luxembourgeois
	'ln'           => array(),           // lingala
	'lo'           => array(),           // lao
	'lt'           => array(),           // lituanien
	'lu'           => array(),           // luba-katanga
	'lv'           => array(),           // letton
	'man'          => array(),           // mandingue
	'mfv'          => array(),           // manjaque - iso-639-3
	'mg'           => array('fr'),       // malgache
	'mi'           => array(),           // maori
	'mk'           => array(),           // macédonien
	'ml'           => array(),           // malayalam
	'mn'           => array('zh'),       // mongol
	'mo'           => array('ro'),       // moldave ??? normalement c'est ro comme le roumain
	'mos'          => array(),           // moré - iso 639-2
	'mr'           => array(),           // marathe
	'ms'           => array(),           // malais
	'mt'           => array('en'),       // maltais
	'my'           => array(),           // birman
	'na'           => array(),           // nauruan
	'nap'          => array('it'),       // napolitain - iso 639-2
	'ne'           => array(),           // népalais
	'nqo'          => array(),           // n’ko - iso 639-3
	'nl'           => array(),           // néerlandais
	'no'           => array(),           // norvégien
	'nb'           => array('no'),       // norvégien bokmål
	'nn'           => array('no'),       // norvégien nynorsk
	'oc'           => array('fr'),       // occitan
	'oc_lnc'       => array('oc', 'fr'), // occitan languedocien
	'oc_ni'        => array('oc', 'fr'), // occitan niçard
	'oc_ni_la'     => array('oc', 'fr'), // occitan niçard larg
	'oc_ni_mis'    => array('oc', 'fr'), // occitan niçard mistralenc
	'oc_prv'       => array('oc', 'fr'), // occitan provençal
	'oc_gsc'       => array('oc', 'fr'), // occitan gascon
	'oc_lms'       => array('oc', 'fr'), // occitan limousin
	'oc_auv'       => array('oc', 'fr'), // occitan auvergnat
	'oc_va'        => array('oc', 'fr'), // occitan vivaro-alpin
	'om'           => array(),           // galla
	'or'           => array(),           // oriya
	'pa'           => array(),           // pendjabi
	'pbb'          => array(),           // Nasa Yuwe (páez) - iso 639-3
	'pl'           => array(),           // polonais
	'prs'          => array(),           // Dari (Afghanistan) - iso 639-3
	'ps'           => array(),           // pachto
	'pt'           => array(),           // portugais
	'pt_br'        => array('pt'),       // portugais brésilien
	'qu'           => array('es'),       // quechua
	'rm'           => array('fr'),       // romanche
	'rn'           => array(),           // rundi
	'ro'           => array(),           // roumain
	'roa'          => array('fr'),       // langues romanes (ch'ti) - iso 639-2
	'ru'           => array(),           // russe
	'rw'           => array(),           // rwanda
	'sa'           => array(),           // sanskrit
	'sc'           => array('it'),       // sarde
	'scn'          => array('it'),       // sicilien - iso 639-2
	'sd'           => array(),           // sindhi
	'sg'           => array(),           // sango
	'sh'           => array('sh'),       // serbo-croate
	'sh_latn'      => array('sh'),       // serbo-croate latin
	'sh_cyrl'      => array('sh'),       // serbo-croate cyrillique
	'si'           => array(),           // singhalais
	'sk'           => array(),           // slovaque
	'sl'           => array(),           // slovène
	'sm'           => array('en'),       // samoan
	'sn'           => array(),           // shona
	'so'           => array(),           // somali
	'sq'           => array(),           // albanais
	'sr'           => array(),           // serbe
	'src'          => array('it'),       // sarde logoudorien - iso 639-3
	'sro'          => array('it'),       // sarde campidanien - iso 639-3
	'ss'           => array(),           // swati
	'st'           => array(),           // sotho du Sud
	'su'           => array(),           // soundanais
	'sv'           => array(),           // suédois
	'sw'           => array(),           // swahili
	'ta'           => array(),           // tamoul
	'te'           => array(),           // télougou
	'tg'           => array(),           // tadjik
	'th'           => array(),           // thaï
	'ti'           => array(),           // tigrigna
	'tk'           => array(),           // turkmène
	'tl'           => array(),           // tagalog
	'tn'           => array(),           // tswana
	'to'           => array('en'),       // tongan (Îles Tonga)
	'tr'           => array(),           // turc
	'ts'           => array(),           // tsonga
	'tt'           => array(),           // tatar
	'tw'           => array(),           // twi
	'ty'           => array('fr'),       // tahitien
	'ug'           => array(),           // ouïgour
	'uk'           => array('ru'),       // ukrainien
	'ur'           => array(),           // ourdou
	'uz'           => array(),           // ouszbek
	'vi'           => array(),           // vietnamien
	'vo'           => array(),           // volapük
	'wa'           => array('fr'),       // wallon
	'wo'           => array(),           // wolof
	'xh'           => array(),           // xhosa
	'yi'           => array('he'),       // yiddish
	'yo'           => array(),           // yoruba
	'za'           => array('zh'),       // zhuang
	'zh'           => array(),           // chinois (ecriture simplifiee)
	'zh_tw'        => array('zh'),       // chinois taiwan (ecriture traditionnelle)
	'zu'           => array()            // zoulou
);

if (!defined('_RAINETTE_CACHE_NOMDIR')) {
	/**
	 * Nom du dossier contenant les fichiers caches
	 */
	define('_RAINETTE_CACHE_NOMDIR', 'cache-rainette/');
}
if (!defined('_RAINETTE_CACHE_DIR')) {
	/**
	 * Chemin du dossier contenant les fichiers caches
	 */
	define('_RAINETTE_CACHE_DIR', _DIR_VAR . _RAINETTE_CACHE_NOMDIR);
}


/**
 * Normalise les données issues du service dans un tableau standard aux index prédéfinis pour chaque mode.
 *
 * @param array  $configuration_service
 *        Configuration statique et utilisateur du service ayant retourné le flux de données.
 * @param string $mode
 *        Le type de données météorologiques demandé :
 *        - `conditions`, la valeur par défaut
 *        - `previsions`
 *        - `infos`
 * @param array  $flux
 *        Le tableau brut des données météorologiques issu de l'appel au service.
 * @param int    $periode
 *        Valeur de 0 à n pour indiquer qu'on traite les données de prévisions d'une période horaire donnée
 *        ou -1 pour indiquer que l'on traite les données jour. La valeur maximale n dépend de la périodicité
 *        des prévisions, par exemple, elle vaut 0 pour une périodicité de 24h, 1 pour 12h...
 *
 * @return array
 *        Le tableau standardisé des données météorologiques du service pour la période spécifiée.
 */
function meteo_normaliser($configuration_service, $mode, $flux, $periode) {
	$tableau = array();

	include_spip('inc/filtres');
	if ($flux !== null) {
		// Le service a renvoyé des données, on boucle sur les clés du tableau normalisé
		// Néanmoins, en fonction de la période fournie en argument on filtre les données uniquement
		// utiles à cette période:
		// - si période = -1 on traite les données jour
		// - si période > -1 on traite les données heure
		foreach (array_keys($GLOBALS['rainette_config'][$mode]) as $_donnee) {
			if ((($periode == -1)
				 and (empty($GLOBALS['rainette_config'][$mode][$_donnee]['rangement'])
					  or ($GLOBALS['rainette_config'][$mode][$_donnee]['rangement'] == 'jour')))
				or (($periode > -1) and ($GLOBALS['rainette_config'][$mode][$_donnee]['rangement'] == 'heure'))
			) {
				if ($GLOBALS['rainette_config'][$mode][$_donnee]['origine'] == 'service') {
					// La donnée est fournie par le service. Elle n'est jamais calculée par le plugin
					// Néanmoins, elle peut-être indisponible temporairement
					if ($cle_service = $configuration_service['donnees'][$_donnee]['cle']) {
						// La donnée est normalement fournie par le service car elle possède une configuration de clé
						// On traite le cas où le nom de la clé varie suivant le système d'unité choisi ou la langue.
						// La clé de base peut être vide, le suffixe contenant dès lors toute la clé.
						if (!empty($configuration_service['donnees'][$_donnee]['suffixe_unite'])) {
							$systeme_unite = $configuration_service['unite'];
							$id_suffixee = $configuration_service['donnees'][$_donnee]['suffixe_unite']['id_cle'];
							$cle_service[$id_suffixee] .= $configuration_service['donnees'][$_donnee]['suffixe_unite'][$systeme_unite];
						} elseif (!empty($configuration_service['donnees'][$_donnee]['suffixe_langue'])) {
							$langue = langue_determiner($configuration_service);
							$id_suffixee = $configuration_service['donnees'][$_donnee]['suffixe_langue']['id_cle'];
							$cle_service[$id_suffixee] .= $langue;
						}

						// On utilise donc la clé pour calculer la valeur du service.
						// Si la valeur est disponible on la stocke sinon on met la donnée à chaine vide pour
						// montrer l'indisponibilité temporaire.
						$donnee = '';
						$valeur_service = empty($cle_service)
							? $flux
							: table_valeur($flux, implode('/', $cle_service), '');
						if ($valeur_service !== '') {
							$typer = donnee_typer($mode, $_donnee);
							$valeur_typee = $typer($valeur_service);

							// Vérification de la donnée en cours de traitement si une fonction idoine existe
							$verifier = "donnee_verifier_${_donnee}";
							if (!function_exists($verifier) or (function_exists($verifier) and $verifier($valeur_typee))) {
								$donnee = $valeur_typee;
							}
						}
					} else {
						// La donnée météo n'est jamais fournie par le service. On la positionne à null pour
						// la distinguer avec une donnée vide qui indique une indisponibilité temporaire.
						$donnee = null;
					}
				} else {
					// La données météo est toujours calculée à posteriori par le plugin indépendamment
					// du service. On l'initialise temporairement à la chaine vide.
					$donnee = '';
				}

				$tableau[$_donnee] = $donnee;
			}
		}
	}

	return $tableau;
}


/**
 * Détermine, en fonction du type PHP configuré, la fonction à appliquer à la valeur d'une donnée.
 *
 * @param string $mode
 *        Le type de données météorologiques demandé :
 *        - `conditions`, la valeur par défaut
 *        - `previsions`
 *        - `infos`
 * @param string $donnee
 *        Correspond à l'index du tableau associatif standardisé comme `temperature`, `humidite`, `precipitation`...
 *
 * @return string
 *        La fonction PHP (floatval, intval...) ou spécifique à appliquer à la valeur de la donnée.
 */
function donnee_typer($mode, $donnee) {
	$fonction = '';

	$type_php = isset($GLOBALS['rainette_config'][$mode][$donnee]['type_php'])
		? $GLOBALS['rainette_config'][$mode][$donnee]['type_php']
		: '';
	if ($type_php) {
		switch ($type_php) {
			case 'float':
				$fonction = 'floatval';
				break;
			case 'int':
				$fonction = 'intval';
				break;
			case 'string':
				$fonction = 'strval';
				break;
			case 'date':
				$fonction = 'donnee_formater_date';
				break;
			case 'heure':
				$fonction = 'donnee_formater_heure';
				break;
			default:
				$fonction = '';
		}
	}

	return $fonction;
}


/**
 * Formate une date numérique ou sous une autre forme en une date au format `Y-m-d H:i:s`.
 *
 * @param string $donnee
 *        Correspond à un index du tableau associatif standardisé à formater en date standard.
 *
 * @return string
 *        Date au format `Y-m-d H:i:s`.
 */
function donnee_formater_date($donnee) {
	if (is_numeric($donnee)) {
		$date = date('Y-m-d H:i:s', $donnee);
	} else {
		$date = date_create($donnee);
		if (!$date) {
			$elements_date = explode(' ', $donnee);
			array_pop($elements_date);
			$donnee = implode(' ', $elements_date);
			$date = date_create($donnee);
		}
		$date = date_format($date, 'Y-m-d H:i:s');
	}

	return $date;
}

/**
 * Formate une heure numérique ou sous une autre forme en une heure au format `H:i`.
 *
 * @param string $donnee
 *        Correspond à un index du tableau associatif standardisé à formater en heure standard.
 *
 * @return string
 *        Heure au format `H:i`.
 */
function donnee_formater_heure($donnee) {
	if (is_numeric($donnee)) {
		$taille = strlen($donnee);
		if ($taille < 3) {
			$m = '00';
			$h = $donnee;
		} else {
			$m = substr($donnee, -2);
			$h = strlen($donnee) == 3
				? substr($donnee, 0, 1)
				: substr($donnee, 0, 2);
		}
		$heure = "${h}:${m}";
	} else {
		$heure = $donnee;
	}

	return $heure;
}

/**
 * Vérifie que la valeur de l'indice UV est acceptable.
 *
 * @param int $valeur
 *        Valeur de l'indice UV à vérifier. Un indice UV est toujours compris entre 0 et 16, bornes comprises.
 *
 * @return bool
 *        `true` si la valeur est acceptable, `false` sinon.
 */
function donnee_verifier_indice_uv($valeur) {

	$est_valide = true;
	if (($valeur < 0) or ($valeur > 16)) {
		$est_valide = false;
	}

	return $est_valide;
}

/**
 * @param $erreur
 * @param $lieu
 * @param $mode
 * @param $modele
 * @param $service
 *
 * @return array
 */
function erreur_formater_texte($erreur, $lieu, $mode, $modele, $service, $nom_service) {

	$texte = array('principal' => '', 'conseil' => '', 'service' => '');

	$type_erreur = $erreur['type'];
	switch ($type_erreur) {
		// Cas d'erreur lors du traitement de la requête par le plugin
		case 'url_indisponible':
		case 'analyse_xml':
		case 'analyse_json':
			// Cas d'erreur où le service renvoie aucune donnée sans pour autant monter une erreur.
		case 'aucune_donnee':
			// Cas d'erreur où le nombre de requêtes maximal a été atteint.
			$texte['principal'] .= _T("rainette:erreur_${type_erreur}", array('service' => $nom_service));
			$texte['conseil'] .= _T('rainette:erreur_conseil_equipe');
			break;
		// Cas d'erreur renvoyé par le service lui-même
		case 'reponse_service':
			if (!empty($erreur['service']['code'])) {
				$texte['service'] .= $erreur['service']['code'];
			}
			if (!empty($erreur['service']['message'])) {
				$texte['service'] .= ($texte['service'] ? ' - ' : '') . $erreur['service']['message'];
			}
			$texte['principal'] .= _T("rainette:erreur_${type_erreur}_${mode}", array('service' => $nom_service, 'lieu' => $lieu));
			$texte['conseil'] .= _T('rainette:erreur_conseil_service');
			break;
		// Cas d'erreur où le nombre de requêtes maximal a été atteint.
		case 'limite_service':
			$texte['principal'] .= _T("rainette:erreur_${type_erreur}", array('service' => $nom_service));
			$texte['conseil'] .= _T('rainette:erreur_conseil_limite');
			break;
		// Cas d'erreur du à une mauvaise utilisation des modèles
		case 'modele_periodicite':
			$texte['principal'] .= _T("rainette:erreur_${type_erreur}", array('modele' => $modele));
			$texte['conseil'] .= _T('rainette:erreur_conseil_periodicite');
			break;
		case 'modele_service':
			$texte['principal'] .= _T("rainette:erreur_${type_erreur}", array('modele' => $modele, 'service' => $nom_service));
			$texte['conseil'] .= _T('rainette:erreur_conseil_modele_changer');
			break;
		case 'modele_inutilisable':
			$texte['principal'] .= _T("rainette:erreur_${type_erreur}", array('modele' => $modele));
			$texte['conseil'] .= _T('rainette:erreur_conseil_modele_expliciter');
			break;
	}


	return $texte;
}

/**
 * @param $type_modele
 * @param $service
 *
 * @return int
 */
function periodicite_determiner($type_modele, $service) {

	// Périodicité initialisée à "non trouvée"
	$periodicite = 0;

	if (isset($GLOBALS['rainette_config']['periodicite'][$type_modele])) {
		// Acquérir la configuration statique du service pour connaître les périodicités horaires supportées
		// pour le mode prévisions.
		include_spip("services/${service}");
		$configurer = "${service}_service2configuration";
		$configuration = $configurer('previsions');
		$periodicites_service = array_keys($configuration['periodicites']);

		$periodicites_modele = $GLOBALS['rainette_config']['periodicite'][$type_modele];
		foreach ($periodicites_modele as $_periodicite_modele) {
			if (in_array($_periodicite_modele, $periodicites_service)) {
				$periodicite = $_periodicite_modele;
				break;
			}
		}
	}

	return $periodicite;
}


/**
 * @param $type_modele
 * @param $periodicite
 *
 * @return bool
 */
function periodicite_est_compatible($type_modele, $periodicite) {

	// Initialisation de la compatibilité à "non compatible".
	$compatible = false;

	if (isset($GLOBALS['rainette_config']['periodicite'][$type_modele])
		and in_array($periodicite, $GLOBALS['rainette_config']['periodicite'][$type_modele])
	) {
		$compatible = true;
	}

	return $compatible;
}


/**
 * Construit le tableau du cache en fonction du service, du lieu, du type de données, de la langue utilisée par le site
 * et de l'unité des données.
 *
 * @param string $lieu
 *        Lieu pour lequel on requiert le nom du cache.
 * @param string $mode
 *        Type de données météorologiques. Les valeurs possibles sont `infos`, `conditions` ou `previsions`.
 * @param int    $periodicite
 *        La périodicité horaire des prévisions :
 *        - `24`, `12`, `6`, `3` ou `1`, pour le mode `previsions`
 *        - `0`, pour les modes `conditions` et `infos`
 * @param array  $configuration_service
 *        Configuration complète du service, statique et utilisateur. Contient l'unité choisie pour les données.
 *
 * @return array
 *        Chemin complet du fichier cache.
 */
function cache_normaliser($lieu, $mode, $periodicite, $configuration_service) {

	// Identification de la langue du resume.
	$cache = array();

	// Le cache est stocké dans un sous-dossier au nom du service
	$cache['sous_dossier'] = $configuration_service['alias'];

	// Composants obligatoires
	// -- Le nom du lieu normalisé (sans espace et dont tous les caractères non alphanumériques sont remplacés par un tiret)
	$lieu_normalise = lieu_normaliser($lieu);
	$cache['lieu'] = str_replace(array(' ', ',', '+', '.', '/'), '-', $lieu_normalise);

	// -- Le nom du mode (infos, conditions ou previsions) accolé à la périodicité du cache pour les prévisions uniquement
	$cache['donnees'] = $mode . ($periodicite ? strval($periodicite) : '');

	// -- Identification de la langue du resume.
	$code_langue = langue_determiner($configuration_service);
	$cache['langage'] = strtolower($code_langue);

	// Composants facultatifs
	// -- Unité des données si le mode n'est pas infos.
	if ($mode != 'infos') {
		$cache['unite'] = $configuration_service['unite'];
	}

	// La durée de conservation par défaut est positionné pour le mode infos. Pour les autres modes il faut
	// la positionner spécifiquement car chaque service a sa propre récurrence.
	if ($mode != 'infos') {
		$cache['conservation'] = $configuration_service['periode_maj'];
	}

	return $cache;
}


/**
 * @param string $lieu
 * @param string $format_lieu
 *
 * @return string
 */
function lieu_normaliser($lieu, &$format_lieu = '') {

	$lieu_normalise = trim($lieu);

	if (preg_match(_RAINETTE_REGEXP_LIEU_WEATHER_ID, $lieu_normalise, $match)) {
		$format_lieu = 'weather_id';
		$lieu_normalise = $match[0];
	} elseif (preg_match(_RAINETTE_REGEXP_LIEU_COORDONNEES, $lieu_normalise, $match)) {
		$format_lieu = 'latitude_longitude';
		$lieu_normalise = "{$match[1]},{$match[2]}";
	} elseif (preg_match(_RAINETTE_REGEXP_LIEU_IP, $lieu_normalise, $match)) {
		$format_lieu = 'adresse_ip';
		$lieu_normalise = $match[0];
	} else {
		$format_lieu = 'ville_pays';
		// On détermine la ville et éventuellement le pays (ville[,pays])
		// et on élimine les espaces par un seul "+".
		$elements = explode(',', $lieu_normalise);
		$lieu_normalise = trim($elements[0]) . (!empty($elements[1]) ? ',' . trim($elements[1]) : '');
		$lieu_normalise = preg_replace('#\s{1,}#', '+', $lieu_normalise);
	}

	return $lieu_normalise;
}


/**
 * @param $configuration_service
 *
 * @return mixed
 */
function langue_determiner($configuration_service) {

	// Les services de Rainette sauf weather.com peuvent renvoyer la traduction du résumé dans plusieurs langues.
	// il est donc nécessaire de demander ce résumé dans la bonne langue si elle existe.

	// On détermine la "bonne langue" : on choisit soit celle de la page en cours
	// soit celle en cours pour l'affichage.
	$langue_spip = !empty($GLOBALS['lang']) ? $GLOBALS['lang'] : $GLOBALS['spip_lang'];

	// On cherche d'abord si le service fournit la langue utilisée par le site.
	// -- Pour cela on utilise la configuration du service qui fournit un tableau des langues disponibles
	//    sous le format [code de langue du service] = code de langue spip.
	$langue_service = array_search($langue_spip, $configuration_service['langues']['disponibles']);

	if ($langue_service === false) {
		// La langue utilisée par SPIP n'est pas supportée par le service.
		// -- On cherche si il existe une langue SPIP utilisable meilleure que la langue par défaut du service.
		// -- Pour ce faire on a défini pour chaque code de langue spip, un ou deux codes de langue SPIP à utiliser
		//    en cas d'absence de la langue concernée dans un ordre de priorité (index 0, puis index 1).
		$langue_service = $configuration_service['langues']['defaut'];
		if ($GLOBALS['rainette_config']['langues_alternatives'][$langue_spip]) {
			foreach ($GLOBALS['rainette_config']['langues_alternatives'][$langue_spip] as $_langue_alternative) {
				$langue_service = array_search($_langue_alternative, $configuration_service['langues']['disponibles']);
				if ($langue_service !== false) {
					break;
				}
			}
		}
	}

	// Aucune langue ne correspond véritablement, on choisit donc la langue configurée par défaut.
	if ($langue_service === false) {
		$langue_service = $configuration_service['langues']['defaut'];
	}

	return $langue_service;
}

/**
 * @param $mode
 * @param $configuration
 *
 * @return array
 */
function configuration_donnees_normaliser($mode, $configuration) {

	$configuration_normalisee = array();

	foreach ($GLOBALS['rainette_config'][$mode] as $_donnee => $_configuration) {
		if ($_configuration['origine'] == 'service') {
			$configuration_normalisee[$_donnee] = !empty($configuration[$_donnee]['cle']) ? true : false;
		}
	}

	return $configuration_normalisee;
}

/**
 * @param $service
 * @param $configuration_defaut
 *
 * @return mixed
 */
function parametrage_normaliser($service, $configuration_defaut) {

	// On récupère la configuration utilisateur
	include_spip('inc/config');
	$configuration_utilisateur = lire_config("rainette/${service}", array());

	// On complète la configuration avec des valeurs par défaut si nécessaire.
	foreach ($configuration_defaut as $_cle => $_valeur) {
		if (!isset($configuration_utilisateur[$_cle])) {
			$configuration_utilisateur[$_cle] = $_valeur;
		}
	}

	return $configuration_utilisateur;
}

/**
 * @param int|string $code_meteo
 * @param string     $theme
 * @param array      $transcodage
 * @param int        $periode
 *
 * @return string
 */
function icone_weather_normaliser($code_meteo, $theme, $transcodage = array(), $periode = 0) {

	// Si le transcodage échoue ou que le code weather est erroné on renvoie toujours N/A.
	$icone = 'na';

	// Transcodage en code weather.com.
	$code = is_string($code_meteo) ? strtolower($code_meteo) : intval($code_meteo);
	if ($transcodage) {
		// Service différent de weather.com
		if (array_key_exists($code, $transcodage) and isset($transcodage[$code][$periode])) {
			$icone = strval($transcodage[$code][$periode]);
		}
	} else {
		// Service weather.com
		if (($code >= 0) and ($code < 48)) {
			$icone = strval($code);
		}
	}

	// Construction du chemin complet de l'icone.
	$chemin = icone_local_normaliser("${icone}.png",'weather', $theme);

	return $chemin;
}

/**
 * @param string $icone
 * @param string $service
 * @param string $theme
 * @param string $periode
 *
 * @return string
 */
function icone_local_normaliser($icone, $service, $theme = '', $periode = '') {

	// On initialise le dossier de l'icone pour le service demandé.
	$chemin = "themes/${service}";
	// Si on demande un thème il faut créer le sous-dossier.
	if ($theme) {
		$chemin .= "/${theme}";
	}
	// Si le service gère des icones suivant le jour ou la nuit il faut ajouter le sous-dossier concerné.
	if ($periode) {
		$chemin .= "/${periode}";
	}
	// On finalise le chemin complet avec le nom de l'icone sauf si on ne veut que le dossier.
	if ($icone) {
		$chemin .= "/${icone}";
	}

	return $chemin;
}
