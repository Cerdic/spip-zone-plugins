<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_kml_infos($id_document){
	if(!intval($id_document))
		return false;
	include_spip('inc/documents');
	$document = sql_fetsel("*", "spip_documents","id_document=".intval($id_document));
	$chemin = $document['fichier'];
	$chemin = get_spip_doc($chemin);
	$extension = $document['extension'];
	
	if(in_array($extension,array('kml','kmz'))){
		$supprimer_chemin = false;
		/**
		 * Si on est dans un kmz (kml + autres fichiers compressés en zip),
		 * On dézip pour trouver le kml
		 */
		if($extension == 'kmz'){
			include_spip('inc/pclzip');
			$zip = new PclZip($chemin);
			$list = $zip->listContent();
			foreach($list as $fichier => $info_fichier){
				if(substr(basename($info_fichier['filename']),-3) == 'kml'){
					$zip->extractByIndex($info_fichier['index'],_DIR_TMP);
					$chemin = _DIR_TMP.$info_fichier['filename'];
					$supprimer_chemin = true;
					break;
				}
			}
		}
		include_spip('inc/xml');
		$ret = lire_fichier($chemin,$donnees);
		$arbre = spip_xml_parse($donnees);
		spip_xml_match_nodes(",^Document,",$arbre, $documents);
		foreach($documents as $document => $info){
			$infos['titre'] = $info[0]['name'][0];
			$infos['descriptif'] = $info[0]['description'][0];
			$infos['longitude'] = $info[0]['LookAt'][0]['longitude'][0] ? $info[0]['LookAt'][0]['longitude'][0] : false;
			$infos['latitude'] = $info[0]['LookAt'][0]['latitude'][0] ? $info[0]['LookAt'][0]['latitude'][0] : false;
		}
		
		/**
		 * Si on n'a pas de longitude ou de latitude, 
		 * on essaie de faire une moyenne des placemarks
		 */
		if(!$info['longitude'] OR !$info['latitude']){
			spip_xml_match_nodes(",^Placemark,",$arbre, $placemarks);
			$latitude = 0;
			$longitude = 0;
			$compte = 0; 
			foreach($placemarks['Placemark'] as $placemark => $lieu){
				$latitude = $latitude + $lieu['LookAt'][0]['latitude'][0];
				$longitude = $longitude + $lieu['LookAt'][0]['longitude'][0];
				if($lieu['LookAt'][0]['longitude'][0] && $latitude + $lieu['LookAt'][0]['latitude'][0])
					$compte++;
			}
			if(($latitude != 0) && ($longitude != 0)){
				$infos['latitude'] = $latitude / $compte;
				$infos['longitude'] = $longitude / $compte; 
			}
		}
	}else
		return false;
	
	if($supprimer_chemin){
		supprimer_fichier($chemin);
	}
		
	return $infos;
}
?>