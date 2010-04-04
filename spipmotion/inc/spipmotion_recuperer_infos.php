<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

/**
 * Récupération des informations techniques du document audio ou video
 * @param int $id_document
 */
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

	$movie = new ffmpeg_movie($movie_chemin, 0);

	$infos['bitrate'] = $movie->getBitRate();
	$infos['duree'] = $movie->getDuration();
	$infos['framecount'] = $movie->getFrameCount();
	$infos['hauteur'] = $movie->getFrameHeight();
	$infos['largeur'] = $movie->getFrameWidth();
	$infos['hasvideo'] = '';
	$infos['hasaudio'] = '';

	if($movie->hasVideo()){
		$infos['videocodec'] = @$movie->getVideoCodec();
		$infos['pixelformat'] = $movie->getPixelFormat();
		$infos['videobitrate'] = $movie->getVideoBitRate();
		$infos['framerate'] = $movie->getFrameRate();
		$infos['hasvideo'] = 'oui';
	}
	if($movie->hasAudio()){
		$infos['hasaudio'] = 'oui';
		$infos['audiocodec'] = @$movie->getAudioCodec();
		$infos['audiobitrate'] = $movie->getAudioBitRate();
		$infos['audiosamplerate'] = $movie->getAudioSampleRate();
		$infos['audiochannels'] = $movie->getAudioChannels();
	}

	if((($infos['videobitrate'] == 0)||($infos['audiobitrate'] == 0)||($infos['videocodec'] == 'flv')) && ($document['extension'] == 'flv')){
		include_spip('inc/xml');
		$arbre = spip_xml_parse($metadatas);
		if(($infos['videobitrate'] == 0)||($infos['videocodec'] == 'flv')){
			/**
			 * videocodecid (Number): Video codec ID used in the FLV
			 * (Captionate uses the first video tag for this value).
			 * Possible values are
			 * 2: Sorenson H.263
			 * 3: Screen Video
			 * 4: On2 VP6
			 * 5: On2 VP6 with Transparency.
			 */
			$videocodecids = array(
								'2'=>'Sorenson H.263',
								'3'=>'Screen Video',
								'4'=>'On2 VP6',
								'5'=>'On2 VP6 Transparency');

			spip_xml_match_nodes(",^videocodecid,",$arbre, $videocodec_array);
			spip_log($videocodec_array['videocodecid'][0],'spipmotion');
			if(array_key_exists($videocodec_array['videocodecid'][0],$videocodecids)){
				$infos['videocodec'] = $videocodecids[$videocodec_array['videocodecid'][0]];
			}
			spip_xml_match_nodes(",^videodatarate,",$arbre, $videobitrate_array);
			$infos['videobitrate'] = $videobitrate_array['videodatarate'][0];
		}
		if($movie->hasAudio()){
			/**
			 * audiocodecid (Number): Audio codec ID used in the FLV.
			 * (Captionate uses the first audio tag with non-zero data size for this value).
			 * Possible values are :
			 * 0: Uncompressed
			 * 1: ADPCM
			 * 2: MP3
			 * 5: Nellymoser 8kHz Mono
			 * 6: Nellymoser.
			 */
			$audiocodecids = array(
								'0'=>'Uncompressed',
								'1'=>'ADPCM',
								'2'=>'mp3',
								'5'=>'Nellymoser 8kHz Mono',
								'6'=>'Nellymoser');
			spip_xml_match_nodes(",^audiocodecid,",$arbre, $audiocodec_array);
			spip_log($audiocodec_array[0],'spipmotion');
			if(array_key_exists($audiocodec_array['audiocodecid'][0],$audiocodecids)){
				$infos['audiocodec'] = $audiocodecids[$audiocodec_array['audiocodecid'][0]];
			}
			spip_xml_match_nodes(",^audiodatarate,",$arbre, $audiobitrate_array);
			$infos['audiobitrate'] = $audiobitrate_array['audiodatarate'][0];
		}
	}
	spip_log($infos,'spipmotion');
	include_spip('inc/modifier');
	return revision_document($id_document, $infos);
}
?>