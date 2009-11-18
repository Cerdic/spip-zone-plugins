<?php
	function inc_spipmotion_recuperer_logo($id_document){
		if(!intval($id_document)){
			return;
		}
		include_spip('inc/documents');
		$mode= 'vignette';
		
		$document = sql_fetsel("docs.id_document,docs.fichier", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
		$chemin_court = $document['fichier'];
		$chemin = get_spip_doc($chemin_court););
		
		$movie = new ffmpeg_movie($chemin,0);
		if($movie->hasVideo()){
			$frame1 = $movie->getFrame(100);
			if($frame1){
				$string_temp = "$id-$type-$id_document";
				$query = md5($string_temp);
				$dossier_temp = _DIR_VAR;
				$fichier_temp = "$dossier_temp$query.jpg";
				spip_log("fichier temporaire = $fichier_temp","spipmotion");
				
				spip_log('frame1 existe',"spipmotion");
				$img_temp = $frame1->toGDImage();
				imagejpeg($img_temp, $fichier_temp);
				$img_finale = $fichier_temp;
				$mode = 'vignette';
				
				$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
				spip_log("on ajoute $img","spipmotion");	
				// verifier l'extension du fichier en fonction de son type mime
				list($extension,$arg) = fixer_extension_document($arg);
				$x = $ajouter_documents($img_finale, $img_finale, 
						    $type, $id, $mode, $id_document, $actifs);
				
				imagedestroy($img_temp);
				unlink($img_finale);
			}
		}
		return;
	}
?>