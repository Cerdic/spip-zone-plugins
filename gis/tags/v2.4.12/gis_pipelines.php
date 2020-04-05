<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function gis_inserer_javascript($flux){
	
	// initialisation des valeurs de config
	$config = @unserialize($GLOBALS['meta']['gis']);
	if (!is_array($config))
		$config = array();
	$config = array_merge(array(
		'api' => 'openlayers'
	), $config);
	
	include_spip('gis_fonctions');
	$config['api'] = gis_api_utilisee();
	if(defined('_GIS_APIS') && !array_key_exists($config['api'],unserialize(_GIS_APIS))){
		return $flux;
	}
	
	// insertion du script de l'api a utiliser
	if ($config['api'] == 'cloudmade')
		$flux .="\n".'<script type="text/javascript" src="http://tile.cloudmade.com/wml/latest/web-maps-lite.js"></script>'."\n";
	if ($config['api'] == 'google')
		$flux .="\n".'<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key='.$config['api_key_google'].'&hl='.$GLOBALS['spip_lang'].'"></script>'."\n";
	if ($config['api'] == 'googlev3')
		$flux .="\n".'<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;language='.$GLOBALS['spip_lang'].'"></script>'."\n";
	if ($config['api'] == 'microsoft')
		$flux .="\n".'<script type="text/javascript" src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6"></script>'."\n";
	if ($config['api'] == 'openlayers')
		$flux .="\n".'<script type="text/javascript" src="http://openlayers.org/api/2.11/OpenLayers.js"></script>'."\n";
	if ($config['api'] == 'ovi')
		$flux .="\n".'<script type="text/javascript" src="http://api.maps.ovi.com/jsl.js"></script>'."\n";
	if ($config['api'] == 'yandex')
		$flux .="\n".'<script type="text/javascript" src="http://api-maps.yandex.ru/1.1/index.xml?key='.$config['api_key_yandex'].'"></script>'."\n";
		
	// insertion de la lib mapstraction
	if(in_array($config['api'],array('cartociudad','google','googlev3','yandex','openlayers'))){
		$geocoder = ($config['geocoder']) ? ',[geocoder]' : '';
	}else{
		$geocoder = '';
	}
	$flux .="\n".'<script id="mxn_script" type="text/javascript" src="'. url_absolue(find_in_path(_DIR_LIB_GIS.'mxn.js')) .'?('. $config['api'] . $geocoder .')"></script>'."\n";
	
	// insertion des scripts de gis
	$flux .="\n".'<script type="text/javascript" src="'. url_absolue(find_in_path('javascript/gis.js')) .'"></script>'."\n";

	return $flux;
}

function gis_affiche_milieu($flux){
	if ($exec = $flux['args']['exec']){
		switch ($exec){
			case 'articles':
				$objet = 'article';
				$id_objet = $flux['args']['id_article'];
				break;
			case 'auteur_infos':
				$objet = 'auteur';
				$id_objet = $flux['args']['id_auteur'];
				break;
			case 'breves_voir':
				$objet = 'breve';
				$id_objet = $flux['args']['id_breve'];
				break;
			case 'naviguer':
				$objet = 'rubrique';
				$id_objet = $flux['args']['id_rubrique'];
				break;
			case 'mots_edit':
				$objet = 'mot';
				$id_objet = $flux['args']['id_mot'];
				break;
			case 'sites':
				$objet = 'syndic';
				$id_objet = $flux['args']['id_syndic'];
				break;
			case 'documents_edit':
				$objet = 'document';
				$id_objet = $flux['args']['id_document'];
				break;
			case 'evenements_edit':
				$objet = 'evenement';
				$id_objet = $flux['args']['id_evenement'];
				break;
			default:
				$objet = $id_objet = '';
				break;
		}
		if ($objet && $id_objet) {
			// TODO : seulement si la conf permet de geolocaliser cet objet
			if (1) {
				$contexte['objet'] = $objet;
				$contexte['id_objet'] = $id_objet;
				$flux['data'] .= "<div id='pave_gis'>";
				$bouton = bouton_block_depliable(_T('gis:cfg_titre_gis'), false, "pave_gis_depliable");
				$flux['data'] .= debut_cadre_enfonce(find_in_path('images/gis-24.png'), true, "", $bouton);
				$flux['data'] .= recuperer_fond('prive/contenu/gis_objet', $contexte);
				$flux['data'] .= fin_cadre_enfonce(true);
				$flux['data'] .= "</div>";
			}
		}
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
			if (function_exists('exif_read_data') AND $exifs =  @exif_read_data($fichier,'GPS')) {
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
		}elseif(in_array($document['extension'],array('kml','kmz'))){
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
					include_spip('inc/modifier');
					revision_document($id_document, $infos);
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

// permettre la recherche dans une boucle gis
function gis_rechercher_liste_des_champs($tables){
	$tables['gis'] = array(
		'titre' => 8,
		'descriptif' => 5,
		'pays' => 3,
		'region' => 3,
		'ville' => 3,
		'code_postal' => 3
	);
	return $tables;
}

function gis_declarer_type_surnoms($surnoms){
	$surnoms['gi'] = 'gis';
	return $surnoms;
}

function gis_declarer_tables_objets_surnoms($surnoms){
	$surnoms['gis'] = 'gis';
	return $surnoms;
}

function gis_taches_generales_cron($taches_generales){
	$taches_generales['gis_nettoyer_base'] = 3600*48;
	return $taches_generales;
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

function gis_saisies_autonomes($flux){
	$flux[] = 'carte';
	return $flux;
}

?>
