<?php
	function inc_spipmotion_recuperer_infos($id_document){
		if(!intval($id_document)){
			return;
		}
		include_spip('inc/documents');
		$document = sql_fetsel("docs.id_document,docs.extension,docs.fichier,docs.taille,docs.mode", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
		$chemin = $document['fichier'];
		$movie_chemin = get_spip_doc($chemin);
		spip_log("on travail sur $movie","spipmotion");	
	
		$movie = @new ffmpeg_movie($movie_chemin, 0);
		
		$height = $movie->getFrameHeight();
		$width = $movie->getFrameWidth();
		$duree = $movie->getDuration();
		$framecount = $movie->getFrameCount();
		$framerate = $movie->getFrameRate();
		$pixelformat = $movie->getPixelFormat();
		$bitrate = $movie->getBitRate();
		$videobitrate = $movie->getVideoBitRate();
		$audiobitrate = $movie->getAudioBitRate();
		if((($videobitrate == 0)||($audiobitrate == 0)) && ($document['extension']== 'flv')){
			include_spip('inc/xml');
			$metadatas = @shell_exec("flvtool2 -xP $movie_chemin");
			$arbre = spip_xml_parse($metadatas);
			if($videobitrate == 0){
				spip_xml_match_nodes(",^videodatarate,",$arbre, $videobitrate_array);
				$videobitrate = $videobitrate_array['videodatarate'][0];
				spip_log("video_bitrate = $videobitrate");
			}
			if($audiobitrate == 0){
				spip_xml_match_nodes(",^audiodatarate,",$arbre, $audiobitrate_array);
				$audiobitrate = $audiobitrate_array['audiodatarate'][0];
				spip_log("audio_bitrate = $audiobitrate");
			}
		}
		$audiosamplerate = $movie->getAudioSampleRate();
		$videocodec = $movie->getVideoCodec();
		$audiocodec = $movie->getAudioCodec();
		$audiochannels = $movie->getAudioChannels();
	
		sql_updateq('spip_documents',array('hauteur'=> $height, 'largeur'=>$width, 'duree'=> $duree, 'framecount'=>$framecount,'framerate'=>$framerate,'pixelformat'=>$pixelformat,'bitrate' => $bitrate, 'videobitrate'=>$videobitrate,'audiobitrate' =>$audiobitrate, 'audiosamplerate'=>$audiosamplerate,'videocodec'=>$videocodec, 'audiocodec'=>$audiocodec, 'audiochannels' =>$audiochannels),'id_document='.sql_quote($id_document));
		return;
	}
?>
