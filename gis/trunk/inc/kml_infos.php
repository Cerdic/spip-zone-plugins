<?php
/**
 * Plugin GIS
 * Récupération de données dans les fichiers kml permettant de :
 * -* récupérer latitude et longitude d'un point correspondant centré sur la moyenne des points ou polygones du kml
 * -* récupérer un titre
 * -* récupérer un descriptif
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function inc_kml_infos($id_document) {
	if (!intval($id_document)) {
		return false;
	}
	include_spip('inc/documents');
	$document = sql_fetsel('*', 'spip_documents', 'id_document=' . intval($id_document));
	$chemin = $document['fichier'];
	$chemin = get_spip_doc($chemin);
	$extension = $document['extension'];

	if (in_array($extension, array('kml','kmz'))) {
		$supprimer_chemin = false;
		/**
		 * Si on est dans un kmz (kml + autres fichiers compressés en zip),
		 * On dézip pour trouver le kml
		 */
		if ($extension == 'kmz') {
			include_spip('inc/pclzip');
			$zip = new PclZip($chemin);
			$list = $zip->listContent();
			foreach ($list as $fichier => $info_fichier) {
				if (substr(basename($info_fichier['filename']), -3) == 'kml') {
					$zip->extractByIndex($info_fichier['index'], _DIR_TMP);
					$chemin = _DIR_TMP.$info_fichier['filename'];
					$supprimer_chemin = true;
					break;
				}
			}
		}
		include_spip('inc/xml');
		$donnees = '';
		lire_fichier($chemin, $donnees);
		$arbre = spip_xml_parse($donnees);
		$documents = $infos = array();
		spip_xml_match_nodes(',^Document,', $arbre, $documents);
		foreach ($documents as $document => $info) {
			$infos['titre'] = $info[0]['name'][0];
			$infos['descriptif'] = $info[0]['description'][0];
			$infos['longitude'] = $info[0]['LookAt'][0]['longitude'][0] ? $info[0]['LookAt'][0]['longitude'][0] : false;
			$infos['latitude'] = $info[0]['LookAt'][0]['latitude'][0] ? $info[0]['LookAt'][0]['latitude'][0] : false;
		}

		/**
		 * Si on n'a pas de longitude ou de latitude,
		 * on essaie de faire une moyenne des placemarks
		 */
		if (!$infos['longitude'] or !$infos['latitude']) {
			$placemarks = array();
			spip_xml_match_nodes(',^Placemark,', $arbre, $placemarks);
			$latitude = 0;
			$longitude = 0;
			$compte = 0;
			foreach ($placemarks as $places) {
				foreach ($places as $lieu) {
					if ($compte > 500) {
						break;
					}
					if ($lieu['LookAt'][0]['longitude'][0] && $latitude + $lieu['LookAt'][0]['latitude'][0]) {
						if ($compte > 500) {
							break;
						}
						$latitude = $latitude + $lieu['LookAt'][0]['latitude'][0];
						$longitude = $longitude + $lieu['LookAt'][0]['longitude'][0];
						$compte++;
					} elseif ($lieu['Point'][0]['coordinates'][0]) {
						if ($compte > 500) {
							break;
						}
						$coordinates = explode(',', $lieu['Point'][0]['coordinates'][0]);
						$latitude = $latitude + trim($coordinates[1]);
						$longitude = $longitude + trim($coordinates[0]);
						$compte++;
					} elseif ($lieu['Polygon'][0]['outerBoundaryIs'][0]['LinearRing'][0]['coordinates'][0]) {
						if ($compte > 500) {
							break;
						}
						$coordinates = explode(' ', trim($lieu['Polygon'][0]['outerBoundaryIs'][0]['LinearRing'][0]['coordinates'][0]));
						foreach ($coordinates as $coordinate) {
							if ($compte > 500) {
								break;
							}
							$coordinate = explode(',', $coordinate);
							$latitude = $latitude + trim($coordinate[1]);
							$longitude = $longitude + trim($coordinate[0]);
							$compte++;
						}
					}
				}
			}
			if (($latitude != 0) and ($longitude != 0)) {
				$infos['latitude'] = $latitude / $compte;
				$infos['longitude'] = $longitude / $compte;
			}
		}

		/**
		 * Si pas de titre ou si le titre est égal au nom de fichier ou contient kml ou kmz :
		 * -* on regarde s'il n'y a qu'un seul Folder et on récupère son nom;
		 * -* on regarde s'il n'y a qu'un seul Placemark et on récupère son nom;
		 */
		if (!$infos['titre'] or ($infos['titre'] == basename($chemin)) or (preg_match(',\.km.,', $infos['titre']) > 0)) {
			$folders = array();
			spip_xml_match_nodes(',^Folder,', $arbre, $folders);
			if (count($folders['Folder']) == 1) {
				foreach ($folders['Folder'] as $dossier) {
					if ($dossier['name'][0]) {
						$infos['titre'] = $dossier['name'][0];
					}
					if (!$infos['descriptif'] && $dossier['description'][0]) {
						$infos['descriptif'] = $dossier['description'][0];
					}
				}
			} else {
				if (!is_array($placemarks)) {
					spip_xml_match_nodes(',^Placemark,', $arbre, $placemarks);
				}
				if (count($placemarks) == 1) {
					foreach ($placemarks as $places) {
						if (count($places) == 1) {
							foreach ($places as $lieu) {
								if ($lieu['name'][0]) {
									$infos['titre'] = $lieu['name'][0];
								}
								if (!$infos['descriptif'] && $lieu['description'][0]) {
									$infos['descriptif'] = $lieu['description'][0];
								}
							}
						}
					}
				}
			}
		}
	} elseif (in_array($extension, array('gpx'))) {
		$supprimer_chemin = false;
		include_spip('inc/xml');
		$donnees = '';
		lire_fichier($chemin, $donnees);
		$arbre = spip_xml_parse($donnees);
		$metadatas = array();
		spip_xml_match_nodes(',^metadata,', $arbre, $metadatas);
		foreach ($metadatas as $info) {
			$infos['titre'] = $info[0]['name'][0];
			//$infos['date'] =  $info[0]['time'][0];
			$infos['descriptif'] = $info[0]['description'][0];
			foreach ($info[0] as $meta => $data) {
				if (preg_match(',^bounds ,', $meta)) {
					$meta = '<'.$meta.'>';
					$maxlat = extraire_attribut($meta, 'maxlat');
					$minlat = extraire_attribut($meta, 'minlat');
					$maxlon = extraire_attribut($meta, 'maxlon');
					$minlon = extraire_attribut($meta, 'minlon');
					if ($maxlat && $minlat) {
						$infos['latitude'] = (($maxlat+$minlat)/2);
					}
					if ($maxlon && $minlon) {
						$infos['longitude'] = (($maxlon+$minlon)/2);
					}
				}
			}
		}
		/**
		 * Si on n'a pas de longitude ou de latitude,
		 * on essaie de faire une moyenne des placemarks
		 */
		if (!$infos['longitude'] or !$infos['latitude']) {
			$trackpoints = array();
			spip_xml_match_nodes(',^trkpt,', $arbre, $trackpoints);
			$latitude = 0;
			$longitude = 0;
			$compte = 0;
			foreach ($trackpoints as $places => $place) {
				foreach ($place as $lieu) {
					if ($compte > 10) {
						break;
					}
				}
			}
			if (($latitude != 0) && ($longitude != 0)) {
				$infos['latitude'] = $latitude / $compte;
				$infos['longitude'] = $longitude / $compte;
			}
		}
	} else {
		return false;
	}

	if (isset($infos['titre'])) {
		$infos['titre'] = preg_replace('/<!\[cdata\[(.*?)\]\]>/is', '$1', $infos['titre']);
	}
	if (isset($infos['descriptif'])) {
		$infos['descriptif'] = preg_replace('/<!\[cdata\[(.*?)\]\]>/is', '$1', $infos['descriptif']);
	}
	if ($supprimer_chemin) {
		supprimer_fichier($chemin);
	}

	return $infos;
}
