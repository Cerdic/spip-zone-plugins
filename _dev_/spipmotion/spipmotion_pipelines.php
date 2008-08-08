<?php

include_spip("inc/spipmotion");

function spipmotion_affiche_droite($flux) {
	if ($flux['args']['exec'] =='articles_edit'){
		$spipmotion = charger_fonction('spipmotion', 'inc');
		$flux['data'] .= $spipmotion($flux['arg']['id_article']);
	}
	return $flux;
}

function spipmotion_editer_contenu_objet($flux){
	$type = _request('exec');
	$type = substr($type, 0, -1);
	$id = _request('id_'.$type);
	if($flux['args']['type']=='case_document'){
		$id_document = $flux['args']['id'];
		$document = sql_fetsel("docs.id_document, docs.id_vignette,docs.extension,docs.fichier,docs.mode,docs.distant, docs.date, L.vu", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
		$extension = $document['extension'];
		if(($extension == 'mov')||($extension == 'flv')||($extension == 'avi')){
			$infos_videos = charger_fonction('infos_videos', 'inc');
			$flux['data'] .= $infos_videos($id,$id_document,$type);
		}
	}
	return $flux['data'];
}

function spipmotion_post_edition($flux){
	spip_log('spipmotion post edition');
	spip_log('operation = '.$flux['args']['operation']);
	spip_log('type_image = '.$flux['args']['type_image']);
	$id_document = $flux['args']['id_objet'];
	if($flux['args']['operation'] == 'ajouter_document'){
			$document = sql_fetsel("docs.id_document, docs.extension,docs.fichier,docs.mode,docs.distant, L.vu, L.objet, L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
			$extension = $document['extension'];
			if(($extension == 'mov')||($extension == 'flv')||($extension == 'avi')){
				
				spip_log("id_document=$id_document - extension = ".$document['extension']);
				include_spip('inc/documents');
				$mode= 'vignette';
				$type= $document['objet'];
				spip_log("type=$type");
				$chemin = $document['fichier'];
				$type = $document['objet'];
				$id = $document['id_objet'];
				spip_log("chemin=$chemin");
				$movie = get_spip_doc($chemin);
				spip_log("on travail sur $movie","spipmotion");	
				$movie = @new ffmpeg_movie($movie, 0);
			
				$string = "$id-$type-$id_document";
				$query = md5($string);
				$dossier = _DIR_VAR;
				$fichier = "$dossier$query.jpg";
				
				
				$frame = $movie->getFrame(100);
				$img = $frame->toGDImage();
				imagejpeg($img, $fichier);
				$img_finale = $fichier;
				$mode = 'vignette';
				
				$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
			
					spip_log("on ajoute $img_finale","spipmotion");	
					// verifier l'extension du fichier en fonction de son type mime
					//list($extension,$arg) = fixer_extension_document($arg);
					$x = $ajouter_documents($img_finale, $img_finale, 
							    $type, $id, $mode, $id_document, $actifs);
							    
				// un invalideur a la hussarde qui doit marcher au moins pour article, breve, rubrique
				imagedestroy($img);
				unlink($img_finale);
				include_spip('inc/invalideur');
				suivre_invalideur("id='id_$type/$id'");
				return $x;
			}
			else{
				spip_log("id_document=$id_document - extension = ".$document['extension']);
				return;
			}
		}
	else{
		return;
	}
}
?>