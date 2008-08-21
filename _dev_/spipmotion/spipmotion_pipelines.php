<?php

include_spip("inc/spipmotion");

function spipmotion_editer_contenu_objet($flux){
	$id_document = $flux['args']['id'];
	if($flux['args']['type']=='case_document'){
		$document = sql_fetsel("docs.id_document, docs.extension, L.vu,L.objet,L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
		$extension = $document['extension'];
		$type = $document['objet'];
		$id = $document['id_objet'];
		if(($extension == 'mov')||($extension == 'flv')||($extension == 'avi')){
			$infos_videos = charger_fonction('infos_videos', 'inc');
			$flux['data'] .= $infos_videos($id,$id_document,$type);
		}
	}
	return $flux['data'];
}

function spipmotion_taches_generales_cron($taches_generales){
	$taches_generales['spipmotion_file'] = 60 * 1; // toutes les 30 minutes
	return $taches_generales;
}

function spipmotion_post_edition($flux){
	$id_document = $flux['args']['id_objet'];
	if($flux['args']['operation'] == 'ajouter_document'){
			$document = sql_fetsel("docs.id_document, docs.extension,docs.fichier,docs.mode,docs.distant, L.vu, L.objet, L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
			$extension = $document['extension'];
			if(($extension == 'mov')||($extension == 'flv')||($extension == 'avi')){
				
				spip_log("id_document=$id_document - extension = ".$document['extension']);
				include_spip('inc/documents');
				$mode= 'vignette';
				$type= $document['objet'];
				$chemin = $document['fichier'];
				$type = $document['objet'];
				$id = $document['id_objet'];
				$movie = get_spip_doc($chemin);
				$movie = @new ffmpeg_movie($movie, 0);
			
				$height = $movie->getFrameHeight();
				$width = $movie->getFrameWidth();
		
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
				
				// verifier l'extension du fichier en fonction de son type mime
				//list($extension,$arg) = fixer_extension_document($arg);
				$x = $ajouter_documents($img_finale, $img_finale, $type, $id, $mode, $id_document, $actifs);
				
				sql_updateq('spip_documents',array('hauteur'=> $height, 'largeur'=>$width),'id_document='.sql_quote($id_document));
				
				// un invalideur a la hussarde qui doit marcher au moins pour article, breve, rubrique
				imagedestroy($img);
				unlink($img_finale);
				include_spip('inc/invalideur');
				suivre_invalideur("id='id_$type/$id'");
				return $x;
			}
			else{
				return;
			}
		}
	else{
		return;
	}
}
?>