<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion des css du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function gis_insert_head_css($flux){
	$flux .="\n".'<link rel="stylesheet" href="'. find_in_path(_DIR_LIB_GIS.'dist/leaflet.css') .'" />';
	$flux .="\n".'<!--[if lte IE 8]> <link rel="stylesheet" href="'. find_in_path(_DIR_LIB_GIS.'dist/leaflet.ie.css') .'" /> <![endif]-->';
	$flux .="\n".'<link rel="stylesheet" href="'. find_in_path(_DIR_LIB_GIS.'plugins/leaflet-plugins.css') .'" />';
	$flux .="\n".'<link rel="stylesheet" href="'. sinon(find_in_path('css/leaflet.markercluster.css'),find_in_path(_DIR_LIB_GIS.'plugins/leaflet.markercluster.css')) .'" />';
	$flux .="\n".'<!--[if lte IE 8]><link rel="stylesheet" href="'. sinon(find_in_path('css/leaflet.markercluster.ie.css'),find_in_path(_DIR_LIB_GIS.'plugins/leaflet.markercluster.ie.css')) .'" /><![endif]-->';
	return $flux;
}

/**
 * Insertion des scripts du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function gis_insert_head($flux){
	
	// initialisation des valeurs de config
	$config = @unserialize($GLOBALS['meta']['gis']);
	if (!is_array($config['layers']))
		$config['layers'] = array('openstreetmap_mapnik');
	
	include_spip('gis_fonctions');
	if (!in_array(gis_layer_defaut(),$config['layers']))
		$config['layers'][] = gis_layer_defaut();
	
	// insertion des scripts pour google si nécessaire
	if (count(array_intersect(array('google_roadmap', 'google_satellite', 'google_terrain'), $config['layers'])) > 0) {
		$flux .="\n".'<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;language='.$GLOBALS['spip_lang'].'"></script>';
	}
	
	return $flux;
}

/**
 * Insertion des scripts et css du plugin dans les pages de l'espace privé
 * @param $flux
 * @return mixed
 */
function gis_header_prive($flux){
	$flux .= gis_insert_head_css('');
	$flux .= gis_insert_head('');
	return $flux;
}

function gis_afficher_contenu_objet($flux){
	if ($objet = $flux['args']['type']
		and include_spip('inc/config')
		and in_array(table_objet_sql($objet), lire_config('gis/gis_objets', array()))
		and ($id = intval($flux['args']['id_objet']))
		
	){
		// TODO : seulement si la conf permet de geolocaliser cet objet
		// -> ajouter un element a la array suivante (qqch comme ca - voir les mots):
		//   'editable'=>autoriser('associergis',$type,$id)?'oui':'non'
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

function gis_post_edition($flux){
	
	if (($flux['args']['operation'] == 'ajouter_document') 
		AND ($document = sql_fetsel("*","spip_documents","id_document=".intval($flux['args']['id_objet'])))
	) {
		if(in_array($document['extension'],array('jpg','kml','kmz'))){
			$config = @unserialize($GLOBALS['meta']['gis']);
			if(!is_array($config))
				$config = array();
			include_spip('inc/documents');
			$fichier = get_spip_doc($document['fichier']);
			$id_document = $document['id_document'];
		}
		if ($document['extension'] == 'jpg') {
			// on recupere les coords definies dans les exif du document s'il y en a
			if ($exifs =  @exif_read_data($fichier,'GPS')) {
				if(!function_exists('dms_to_dec'))
					include_spip('gis_fonctions');
				spip_log("GIS EXIFS : Récuperation des coordonnees du fichier $fichier","gis");
				
				$LatDeg = explode("/",$exifs["GPSLatitude"][0]);
				if(is_numeric($LatDeg[1]) > 0)
					$intLatDeg = $LatDeg[0]/$LatDeg[1];

				$LatMin = explode("/",$exifs["GPSLatitude"][1]);
				if(is_numeric($LatMin[1]) > 0)
					$intLatMin = $LatMin[0]/$LatMin[1];

				$LatSec = explode("/",$exifs["GPSLatitude"][2]);
				if(is_numeric($LatSec[1]) > 0)
					$intLatSec = $LatSec[0]/$LatSec[1];

				$LongDeg = explode("/",$exifs["GPSLongitude"][0]);
				if(is_numeric($LongDeg[1]) > 0)
					$intLongDeg = $LongDeg[0]/$LongDeg[1];

				$LongMin = explode("/",$exifs["GPSLongitude"][1]);
				if(is_numeric($LongMin[1]) > 0)
					$intLongMin = $LongMin[0]/$LongMin[1];

				$LongSec = explode("/",$exifs["GPSLongitude"][2]);
				if(is_numeric($LongSec[1]) > 0)
					$intLongSec = $LongSec[0]/$LongSec[1];

				// round to 5 = approximately 1 meter accuracy
				if(is_numeric($intLatDeg) && is_numeric($intLatMin) && is_numeric($intLatSec))
					$latitude = round(dms_to_dec($exifs["GPSLatitudeRef"],
						$intLatDeg,$intLatMin,$intLatSec),5);

				if(is_numeric($intLongDeg) && is_numeric($intLongMin) && is_numeric($intLongSec))
					$longitude =  round(dms_to_dec($exifs["GPSLongitudeRef"],
						$intLongDeg,$intLongMin,$intLongSec), 5);
				if($config['geocoder'] == 'on'){
					include_spip('inc/xml');
					$url_geocoder = 'http://maps.googleapis.com/maps/api/geocode/xml?latlng='.urlencode($latitude).','.urlencode($longitude).'&sensor=true';
					$geocoder = spip_xml_load($url_geocoder);
					spip_xml_match_nodes(',result,',$geocoder,$matches_adress);
					if(is_array($matches_adress['result'])){
						foreach($matches_adress['result'] as $component){
							if(in_array('country',$component['type'])){
								$pays = $component['address_component'][0]['long_name'][0];
								$code_pays = $component['address_component'][0]['short_name'][0];
							}
							if(in_array('administrative_area_level_1',$component['type'])){
								$region = $component['address_component'][0]['long_name'][0];
							}
							if(in_array('locality',$component['type'])){
								$ville = $component['address_component'][0]['long_name'][0];
							}
							if(in_array('postal_code',$component['type'])){
								$code_postal = $component['address_component'][0]['long_name'][0];
							}
							if(in_array('route',$component['type'])){
								$adresse = $component['address_component'][0]['long_name'][0];
							}
						}
					}
				}
			}else if(file_exists($fichier)){
				include_spip("inc/iptc");

				$er = new class_IPTC($fichier);
				$iptc = $er->fct_lireIPTC();
				$codesiptc = $er->h_codesIptc;
				$string_recherche = '';
				
				if($iptc['city']){
					$string_recherche .= $iptc['city'].', ';
				}
				if($iptc['provinceState']){
					$string_recherche .= $iptc['provinceState'].', ';
				}
				if($iptc['country']){
					$string_recherche .= $iptc['country'];
				}
				if(strlen($string_recherche)){
					include_spip('inc/xml');
					$url_geocoder = 'http://maps.googleapis.com/maps/api/geocode/xml?address='.urlencode($string_recherche).'&sensor=true';
					$geocoder = spip_xml_load($url_geocoder);
					if(is_array($geocoder)){
						spip_xml_match_nodes(',location,',$geocoder,$matches);
						$latitude = $matches['location']['0']['lat']['0'];
						$longitude = $matches['location']['0']['lng']['0'];
						if($config['adresse'] == 'on'){
							spip_xml_match_nodes(',address_component,',$geocoder,$matches_adress);
							if(is_array($matches_adress['address_component'])){
								foreach($matches_adress['address_component'] as $component){
									if(in_array('country',$component['type'])){
										$pays = $component['long_name'][0];
										$code_pays = $component['short_name'][0];
									}
									if(in_array('administrative_area_level_1',$component['type'])){
										$region = $component['long_name'][0];
									}
									if(in_array('locality',$component['type'])){
										$ville = $component['long_name'][0];
									}
								}
							}
						}
					}
				}
			}
			if(is_numeric($latitude) && is_numeric($longitude)){
				$c = array(
					'titre' => basename($fichier),
					'lat'=> $latitude,
					'lon' => $longitude,
					'zoom' => $config['zoom'] ? $config['zoom'] :'4',
					'adresse' => $adresse,
					'code_postal' => $code_postal,
					'ville' => $ville,
					'region' => $region,
					'pays' => $pays,
					'code_pays' => $code_pays
				);
				
				if (defined('_DIR_PLUGIN_GISGEOM')) {
					$geojson = '{"type":"Point","coordinates":['.$longitude.','.$latitude.']}';
					set_request('geojson',$geojson);
				}
				
				include_spip('action/editer_gis');
	
				if($id_gis = sql_getfetsel("G.id_gis","spip_gis AS G LEFT  JOIN spip_gis_liens AS T ON T.id_gis=G.id_gis ","T.id_objet=" . intval($id_document) . " AND T.objet='document'")){
					// Des coordonnées sont déjà définies pour ce document => on les update
					revisions_gis($id_gis,$c);
					spip_log("GIS EXIFS : Update des coordonnées depuis EXIFS pour le document $id_document => id_gis = $id_gis","gis");
				}
				else{
					// Aucune coordonnée n'est définie pour ce document  => on les crées
					$id_gis = insert_gis();
					revisions_gis($id_gis,$c);
					lier_gis($id_gis, 'document', $id_document);
					spip_log("GIS EXIFS : Création des coordonnées depuis EXIFS pour le document $id_document => id_gis = $id_gis","gis");
				}
			}
		}elseif(in_array($document['extension'],array('kml','kmz','gpx'))){
			$recuperer_info = charger_fonction('kml_infos','inc');
			$infos = $recuperer_info($document['id_document']);
			if($infos){
				if(is_numeric($latitude = $infos['latitude']) && is_numeric($longitude = $infos['longitude'])){
					$c = array(
						'titre' => $infos['titre'] ? $infos['titre'] : basename($fichier),
						'descriptif' => $infos['descriptif'],
						'lat'=> $latitude,
						'lon' => $longitude,
						'zoom' => $config['zoom'] ? $config['zoom'] :'4'
					);
			
					include_spip('action/editer_gis');
		
					if($id_gis = sql_getfetsel("G.id_gis","spip_gis AS G LEFT  JOIN spip_gis_liens AS T ON T.id_gis=G.id_gis ","T.id_objet=" . intval($id_document) . " AND T.objet='document'")){
						// Des coordonnées sont déjà définies pour ce document => on les update
						revisions_gis($id_gis,$c);
						spip_log("GIS EXIFS : Update des coordonnées depuis EXIFS pour le document $id_document => id_gis = $id_gis","gis");
					}
					else{
						// Aucune coordonnée n'est définie pour ce document  => on les crées
						$id_gis = insert_gis();
						revisions_gis($id_gis,$c);
						lier_gis($id_gis, 'document', $id_document);
						spip_log("GIS EXIFS : Création des coordonnées depuis EXIFS pour le document $id_document => id_gis = $id_gis","gis");
					}
				}
				unset($infos['longitude']);
				unset($infos['latitude']);
				if(count($infos) > 0){
					include_spip('action/editer_document');
					document_modifier($id_document, $infos);
				}
			}
		}
	}
	if (($flux['args']['operation'] == 'supprimer_document') 
		AND ($id_document = intval($flux['args']['id_objet'])
		AND ($id_gis = sql_getfetsel("G.id_gis","spip_gis AS G LEFT  JOIN spip_gis_liens AS T ON T.id_gis=G.id_gis ","T.id_objet=" . intval($id_document) . " AND T.objet='document'")))
	) {
		include_spip('action/editer_gis');
		supprimer_gis($id_gis);
		spip_log("GIS EXIFS : Suppression des coordonnées pour le document $id_document => id_gis = $id_gis","gis");
	}
	
	return $flux;
}

function gis_taches_generales_cron($taches_generales){
	$taches_generales['gis_nettoyer_base'] = 3600*48;
	return $taches_generales;
}

function gis_saisies_autonomes($flux){
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
function gis_xmlrpc_methodes($flux){
	$flux['spip.liste_gis'] = 'spip_liste_gis';
	$flux['spip.lire_gis'] = 'spip_lire_gis';
	return $flux;
}

/**
 * Insertion dans le pipeline xmlrpc_server_class (xmlrpc)
 * Ajout de fonctions spécifiques utilisés par le serveur xml-rpc 
 */
function gis_xmlrpc_server_class($flux){
	include_spip('inc/gis_xmlrpc');
	return $flux;
}

?>
