<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion des css du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function gis_insert_head_css($flux) {
	$flux .="\n".'<link rel="stylesheet" href="'. direction_css(sinon(find_in_path('css/leaflet.css'), find_in_path('lib/leaflet/dist/leaflet.css'))) .'" />';
	$flux .="\n".'<link rel="stylesheet" href="'. direction_css(sinon(find_in_path('css/leaflet-plugins.css'), find_in_path('lib/leaflet/plugins/leaflet-plugins.css'))) .'" />';
	$flux .="\n".'<link rel="stylesheet" href="'. direction_css(sinon(find_in_path('css/leaflet.markercluster.css'), find_in_path('lib/leaflet/plugins/leaflet.markercluster.css'))) .'" />';
	$flux .="\n".'<link rel="stylesheet" href="'. sinon(find_in_path('css/leaflet_nodirection.css'), find_in_path('lib/leaflet/dist/leaflet_nodirection.css')) .'" />';
	return $flux;
}

/**
 * Insertion des scripts du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function gis_insert_head($flux) {

	// initialisation des valeurs de config
	$config = @unserialize($GLOBALS['meta']['gis']);
	if (!isset($config['layers']) || !is_array($config['layers'])) {
		$config['layers'] = array('openstreetmap_mapnik');
	}

	include_spip('gis_fonctions');
	if (!in_array(gis_layer_defaut(), $config['layers'])) {
		$config['layers'][] = gis_layer_defaut();
	}

	// insertion des scripts pour google si nécessaire
	if (count(array_intersect(array('google_roadmap', 'google_satellite', 'google_terrain'), $config['layers'])) > 0) {
		$flux .="\n".'<script type="text/javascript" src="//maps.google.com/maps/api/js?language='.$GLOBALS['spip_lang'].'&amp;key='.(isset($config['api_key_google']) ? $config['api_key_google'] : '').'"></script>';
	}

	return $flux;
}

/**
 * Insertion des scripts et css du plugin dans les pages de l'espace privé
 *
 * @param $flux
 * @return mixed
 */
function gis_header_prive($flux) {
	$flux .= gis_insert_head_css('');
	$flux .= gis_insert_head('');
	return $flux;
}

/**
 * Insertion du bloc GIS dans les pages des objets de l'espace privé
 *
 * @param $flux
 * @return mixed
 */
function gis_afficher_contenu_objet($flux) {
	if ($objet = $flux['args']['type']
		and include_spip('inc/config')
		and in_array(table_objet_sql($objet), lire_config('gis/gis_objets', array()))
		and ($id = intval($flux['args']['id_objet']))
	) {
		$texte = recuperer_fond(
			'prive/contenu/gis_objet',
			array(
				'table_source'=>'gis',
				'objet'=>$objet,
				'id_objet'=>$id
			)
		);
		$flux['data'] .= $texte;
	}

	return $flux;
}

/**
 * Si la geolocalisation des documents est activée dans la config,
 * création/suppression d'un point à partir des métadonnées du document ajouté (jpg, kml et kmz)
 *
 * @param $flux
 * @return mixed
 */
function gis_post_edition($flux) {
	if (is_array($flux) && isset($flux['args']['operation']) && ($flux['args']['operation'] == 'ajouter_document')
		and ($document = sql_fetsel('*', 'spip_documents', 'id_document=' . intval($flux['args']['id_objet'])))
		and (in_array(table_objet_sql('document'), lire_config('gis/gis_objets', array())))
	) {
		if (in_array($document['extension'], array('jpg', 'kml', 'kmz'))) {
			$config = @unserialize($GLOBALS['meta']['gis']);
			if (!is_array($config)) {
				$config = array();
			}
			include_spip('inc/documents');
			$fichier = get_spip_doc($document['fichier']);
			$id_document = $document['id_document'];
		}
		if ($document['extension'] == 'jpg') {
			// on recupere les coords definies dans les exif du document s'il y en a
			if (function_exists('exif_read_data') and $exifs =  @exif_read_data($fichier, 'GPS')) {
				if (!function_exists('dms_to_dec')) {
					include_spip('gis_fonctions');
				}
				spip_log("GIS EXIFS : Récuperation des coordonnees du fichier $fichier", 'gis');

				$LatDeg = explode('/', $exifs['GPSLatitude'][0]);
				$intLatDeg = $LatDeg[0] / ($LatDeg[1] ? $LatDeg[1] : 1);

				$LatMin = explode('/', $exifs['GPSLatitude'][1]);
				$intLatMin = $LatMin[0] / ($LatMin[1] ? $LatMin[1] : 1);

				$LatSec = explode('/', $exifs['GPSLatitude'][2]);
				$intLatSec = $LatSec[0] / ($LatSec[1] ? $LatSec[1] : 1);

				$LongDeg = explode('/', $exifs['GPSLongitude'][0]);
				$intLongDeg = $LongDeg[0] / ($LongDeg[1] ? $LongDeg[1] : 1);

				$LongMin = explode('/', $exifs['GPSLongitude'][1]);
				$intLongMin = $LongMin[0] / ($LongMin[1] ? $LongMin[1] : 1);

				$LongSec = explode('/', $exifs['GPSLongitude'][2]);
				$intLongSec = $LongSec[0] / ($LongSec[1] ? $LongSec[1] : 1);

				// round to 5 = approximately 1 meter accuracy
				if (is_numeric($intLatDeg) && is_numeric($intLatMin) && is_numeric($intLatSec)) {
					$latitude = round(dms_to_dec($exifs['GPSLatitudeRef'], $intLatDeg, $intLatMin, $intLatSec), 5);
				}

				if (is_numeric($intLongDeg) && is_numeric($intLongMin) && is_numeric($intLongSec)) {
					$longitude =  round(dms_to_dec($exifs['GPSLongitudeRef'], $intLongDeg, $intLongMin, $intLongSec), 5);
				}
				if ($config['geocoder'] == 'on') {
					include_spip('inc/distant');
					$url_geocoder = 'http://nominatim.openstreetmap.org/reverse/?format=xml&addressdetails=1&accept-language='.urlencode($GLOBALS['meta']['langue_site']).'&lat='.urlencode($latitude).'&lon='.urlencode($longitude);
					$json = recuperer_page($url_geocoder);
					$geocoder = json_decode($json, true);
					if (is_array($geocoder)) {
						$pays = $geocoder['address']['country'];
						$code_pays = $geocoder['address']['country_code'];
						$region = $geocoder['address']['state'];
						if ($geocoder['address']['city']) {
							$ville = $geocoder['address']['city'];
						} elseif ($geocoder['address']['town']) {
							$ville = $geocoder['address']['town'];
						} elseif ($geocoder['address']['village']) {
							$ville = $geocoder['address']['village'];
						}
						$code_postal = $geocoder['address']['postcode'];
						$adresse = $geocoder['address']['road'];
					}
				}
			} elseif (file_exists($fichier)) {
				include_spip('inc/iptc');

				$er = new class_IPTC($fichier);
				$iptc = $er->fct_lireIPTC();
				$codesiptc = $er->h_codesIptc;
				$string_recherche = '';

				if ($iptc['city']) {
					$string_recherche .= $iptc['city'].', ';
				}
				if ($iptc['provinceState']) {
					$string_recherche .= $iptc['provinceState'].', ';
				}
				if ($iptc['country']) {
					$string_recherche .= $iptc['country'];
				}
				if (strlen($string_recherche)) {
					include_spip('inc/distant');
					$url_geocoder = 'http://nominatim.openstreetmap.org/search/?format=json&addressdetails=1&limit=1&accept-language='.urlencode($GLOBALS['meta']['langue_site']).'&q='.urlencode($string_recherche);
					$json = recuperer_page($url_geocoder);
					$geocoder = json_decode($json, true);
					if (is_array($geocoder[0])) {
						$latitude = $geocoder[0]['lat'];
						$longitude = $geocoder[0]['lon'];
						if ($config['adresse'] == 'on') {
							$pays = $geocoder[0]['address']['country'];
							$code_pays = $geocoder[0]['address']['country_code'];
							$region = $geocoder[0]['address']['state'];
							if ($geocoder[0]['address']['city']) {
								$ville = $geocoder[0]['address']['city'];
							} elseif ($geocoder[0]['address']['town']) {
								$ville = $geocoder[0]['address']['town'];
							} elseif ($geocoder[0]['address']['village']) {
								$ville = $geocoder[0]['address']['village'];
							}
						}
					}
				}
			}
			if (is_numeric($latitude) && is_numeric($longitude)) {
				$c = array(
					'titre' => basename($fichier),
					'lat'=> $latitude,
					'lon' => $longitude,
					'zoom' => $config['zoom'] ? $config['zoom'] :'4',
					'adresse' => $adresse,
					'code_postal' => $code_postal,
					'ville' => $ville,
					'region' => $region,
					'departement' => $departement,
					'pays' => $pays,
					'code_pays' => $code_pays
				);

				if (defined('_DIR_PLUGIN_GISGEOM')) {
					$geojson = '{"type":"Point","coordinates":['.$longitude.','.$latitude.']}';
					set_request('geojson', $geojson);
				}

				include_spip('action/editer_gis');

				if ($id_gis = sql_getfetsel('G.id_gis', 'spip_gis AS G LEFT  JOIN spip_gis_liens AS T ON T.id_gis=G.id_gis', 'T.id_objet=' . intval($id_document) . " AND T.objet='document'")) {
					// Des coordonnées sont déjà définies pour ce document => on les update
					revisions_gis($id_gis, $c);
					spip_log("GIS EXIFS : Update des coordonnées depuis EXIFS pour le document $id_document => id_gis = $id_gis", 'gis');
				} else {
					// Aucune coordonnée n'est définie pour ce document  => on les crées
					$id_gis = insert_gis();
					revisions_gis($id_gis, $c);
					lier_gis($id_gis, 'document', $id_document);
					spip_log("GIS EXIFS : Création des coordonnées depuis EXIFS pour le document $id_document => id_gis = $id_gis", 'gis');
				}
			}
		} elseif (in_array($document['extension'], array('kml','kmz','gpx'))) {
			$recuperer_info = charger_fonction('kml_infos', 'inc');
			$infos = $recuperer_info($document['id_document']);
			if ($infos) {
				if (is_numeric($latitude = $infos['latitude']) && is_numeric($longitude = $infos['longitude'])) {
					$c = array(
						'titre' => $infos['titre'] ? $infos['titre'] : basename($fichier),
						'descriptif' => $infos['descriptif'],
						'lat'=> $latitude,
						'lon' => $longitude,
						'zoom' => $config['zoom'] ? $config['zoom'] :'4'
					);

					include_spip('action/editer_gis');

					if ($id_gis = sql_getfetsel('G.id_gis', 'spip_gis AS G LEFT  JOIN spip_gis_liens AS T ON T.id_gis=G.id_gis', 'T.id_objet=' . intval($id_document) . " AND T.objet='document'")) {
						// Des coordonnées sont déjà définies pour ce document => on les update
						revisions_gis($id_gis, $c);
						spip_log("GIS EXIFS : Update des coordonnées depuis EXIFS pour le document $id_document => id_gis = $id_gis", 'gis');
					} else {
						// Aucune coordonnée n'est définie pour ce document  => on les crées
						$id_gis = insert_gis();
						revisions_gis($id_gis, $c);
						lier_gis($id_gis, 'document', $id_document);
						spip_log("GIS EXIFS : Création des coordonnées depuis EXIFS pour le document $id_document => id_gis = $id_gis", 'gis');
					}
				}
				unset($infos['longitude']);
				unset($infos['latitude']);
				if (count($infos) > 0) {
					include_spip('action/editer_document');
					document_modifier($id_document, $infos);
				}
			}
		}
	}
	if (is_array($flux) && isset($flux['args']['operation']) && ($flux['args']['operation'] == 'supprimer_document')
		and ($id_document = intval($flux['args']['id_objet'])
		and ($id_gis = sql_getfetsel('G.id_gis', 'spip_gis AS G LEFT JOIN spip_gis_liens AS T ON T.id_gis=G.id_gis', 'T.id_objet=' . intval($id_document) . " AND T.objet='document'")))
	) {
		include_spip('action/editer_gis');
		supprimer_gis($id_gis);
		spip_log("GIS EXIFS : Suppression des coordonnées pour le document $id_document => id_gis = $id_gis", 'gis');
	}

	return $flux;
}


/**
 * Optimiser la base de données en supprimant les liens orphelins
 *
 * @param array $flux
 * @return array
 */
function gis_optimiser_base_disparus($flux) {

	include_spip('action/editer_liens');
	// optimiser les liens morts :
	// entre gis vers des objets effaces
	// depuis des gis effaces
	$flux['data'] += objet_optimiser_liens(array('gis' => '*'), '*');

	return $flux;
}

function gis_saisies_autonomes($flux) {
	$flux[] = 'carte';
	return $flux;
}

/**
 * Insertion dans le pipeline xmlrpc_methodes (xmlrpc)
 * Ajout de méthodes xml-rpc spécifiques à GIS
 *
 * @param array $flux : un array des methodes déjà présentes, fonctionnant sous la forme :
 * -* clé = nom de la méthode;
 * -* valeur = le nom de la fonction à appeler;
 * @return array $flux : l'array complété avec nos nouvelles méthodes
 */
function gis_xmlrpc_methodes($flux) {
	$flux['spip.liste_gis'] = 'spip_liste_gis';
	$flux['spip.lire_gis'] = 'spip_lire_gis';
	return $flux;
}

/**
 * Insertion dans le pipeline xmlrpc_server_class (xmlrpc)
 * Ajout de fonctions spécifiques utilisés par le serveur xml-rpc
 */
function gis_xmlrpc_server_class($flux) {
	include_spip('inc/gis_xmlrpc');
	return $flux;
}

/**
 * Insertion dans le traitement du formulaire de configuration
 *
 * Purger le répertoire js si on a une carte google dans les layers pour recalculer le js statique
 * Peut être à améliorer
 * Invalider le cache lors de l'ajout ou dissociation d'un point à un objet, "Voir en ligne" ne suffit pas
 * car le json est sur un autre hit
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 */
function gis_formulaire_traiter($flux) {
	if ($flux['args']['form'] == 'configurer_gis'
		and count(array_intersect(array('google_roadmap', 'google_satellite', 'google_terrain'), _request('layers'))) > 0) {
		include_spip('inc/invalideur');
		purger_repertoire(_DIR_VAR . 'cache-js');
		suivre_invalideur(1);
	} else if ($flux['args']['form'] == 'editer_liens'
		and isset($flux['args']['args'][0])
		and $flux['args']['args'][0] == 'gis') {
		include_spip('inc/invalideur');
		suivre_invalideur(1);
	}
	return $flux;
}

/**
 * Definir le libelle pour les logos GIS dans l'espace prive
 * @param array $logo_libelles
 * @return mixed
 */
function gis_libeller_logo($logo_libelles) {
	$logo_libelles['id_gis'] = _T('gis:libelle_logo_gis');
	return $logo_libelles;
}