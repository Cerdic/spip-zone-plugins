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
			$infos['longitude'] = $info[0]['LookAt'][0]['longitude'][0];
			$infos['latitude'] = $info[0]['LookAt'][0]['latitude'][0];
		}
	}else
		return false;
	
	if($supprimer_chemin){
		supprimer_fichier($chemin);
	}
		
	return $infos;
}
?>