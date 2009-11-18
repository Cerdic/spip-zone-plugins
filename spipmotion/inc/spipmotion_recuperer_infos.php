<?php
	function inc_spipmotion_recuperer_infos($id_document){
		spip_log("SPIPMOTION : recuperation des infos du document $id_document","spipmotion");
		if(!intval($id_document)){
			return;
		}
		
		include_spip('inc/documents');
		$document = sql_fetsel("docs.extension,docs.fichier,docs.taille,docs.mode", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
		$chemin = $document['fichier'];
		$movie_chemin = get_spip_doc($chemin);
		
		/**
		 * Si c'est un flv on lui applique les metadatas pour éviter les problèmes
		 */
		if($document['extension'] == 'flv'){
			$metadatas = @shell_exec("flvtool2 -xUP $movie_chemin");	
		}
		
		spip_log($metadatas);
		spip_log("on travail sur $movie_chemin","spipmotion");
	
		$movie = new ffmpeg_movie($movie_chemin, 0);
		spip_log($movie);
		
		$bitrate = $movie->getBitRate();
		$duree = $movie->getDuration();
		$framecount = $movie->getFrameCount();
		$height = $movie->getFrameHeight();
		$width = $movie->getFrameWidth();
		if($movie->hasVideo()){
			$videocodec = @$movie->getVideoCodec();
			spip_log($videocodec);
				$pixelformat = $movie->getPixelFormat();
				$videobitrate = $movie->getVideoBitRate();
				$framerate = $movie->getFrameRate();
		}
		if($movie->hasAudio()){
			$audiocodec = @$movie->getAudioCodec();
			$audiobitrate = $movie->getAudioBitRate();
			$audiosamplerate = $movie->getAudioSampleRate();
			$audiochannels = $movie->getAudioChannels();
		}
		
		if((($videobitrate == 0)||($audiobitrate == 0)) && ($document['extension'] == 'flv')){
			include_spip('inc/xml');
			spip_log($metadatas);
			$arbre = spip_xml_parse($metadatas);
			if($videobitrate && ($videobitrate == 0)){
				spip_xml_match_nodes(",^videodatarate,",$arbre, $videobitrate_array);
				$videobitrate = $videobitrate_array['videodatarate'][0];
				spip_log("video_bitrate = $videobitrate");
			}
			if($movie->hasAudio() && ($audiobitrate == 0)){
				spip_xml_match_nodes(",^audiodatarate,",$arbre, $audiobitrate_array);
				$audiobitrate = $audiobitrate_array['audiodatarate'][0];
				spip_log("audio_bitrate = $audiobitrate");
			}
		}
	
		sql_updateq('spip_documents',array('hauteur'=> $height, 'largeur'=>$width, 'duree'=> $duree, 'framecount'=>$framecount,'framerate'=>$framerate,'pixelformat'=>$pixelformat,'bitrate' => $bitrate, 'videobitrate'=>$videobitrate,'audiobitrate' =>$audiobitrate, 'audiosamplerate'=>$audiosamplerate,'videocodec'=>$videocodec, 'audiocodec'=>$audiocodec, 'audiochannels' =>$audiochannels),'id_document='.sql_quote($id_document));
		return;
	}
?>
