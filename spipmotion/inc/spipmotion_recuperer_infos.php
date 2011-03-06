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
	if(!intval($id_document) OR ($GLOBALS['meta']['spipmotion_casse'] == 'oui'))
		return false;

	include_spip('inc/documents');
	$document = sql_fetsel("docs.extension,docs.fichier,docs.taille,docs.mode", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
	$chemin = $document['fichier'];
	$movie_chemin = get_spip_doc($chemin);

	/**
	 * Si c'est un flv on lui applique les metadatas pour éviter les problèmes
	 * Si c'est un mov ou MP4 on applique qt-faststart
	 */
	if(($document['extension'] == 'flv') && !$GLOBALS['meta']['spipmotion_flvtool_casse']){
		$metadatas = @shell_exec("flvtool2 -xUP $movie_chemin");
	}
	if(in_array($document['extension'],array('mov','mp4','m4v')) && !$GLOBALS['meta']['spipmotion_qt-faststart_casse']){
		exec("qt-faststart $movie_chemin $movie_chemin._temp",$retour,$retour_int);
		if(file_exists($movie_chemin.'._temp')){
			rename($movie_chemin.'._temp',$movie_chemin);
		}
	}

	if(!$GLOBALS['meta']['spipmotion_mediainfo_casse']){
		$mediainfo = charger_fonction('spipmotion_mediainfo','inc');
		$infos = $mediainfo($movie_chemin);
	}else{
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
		}else{
			$infos['hasvideo'] = 'non';
		}
		if($movie->hasAudio()){
			$infos['hasaudio'] = 'oui';
			$infos['audiocodec'] = @$movie->getAudioCodec();
			$infos['audiobitrate'] = $movie->getAudioBitRate();
			$infos['audiosamplerate'] = $movie->getAudioSampleRate();
			$infos['audiochannels'] = $movie->getAudioChannels();
		}else{
			$infos['hasaudio'] = 'non';
		}
		if((($infos['videobitrate'] == 0)||($infos['audiobitrate'] == 0)|| ($infos['videocodec'] == 'flv')) && ($document['extension'] == 'flv')){
			include_spip('inc/xml');
			$arbre = spip_xml_parse($metadatas);
			if(($infos['videobitrate'] == 0)||($infos['videocodec'] == 'flv')){
				spip_xml_match_nodes(",^videocodecid,",$arbre, $videocodec_array);
				$infos['videocodec'] = $videocodec_array['videocodecid'][0];
	
				spip_xml_match_nodes(",^videodatarate,",$arbre, $videobitrate_array);
				$infos['videobitrate'] = floor($videobitrate_array['videodatarate'][0]*1000);
			}
			if($movie->hasAudio()){
				spip_xml_match_nodes(",^audiocodecid,",$arbre, $audiocodec_array);
				$infos['audiocodec'] = $audiocodec_array['audiocodecid'][0];
	
				spip_xml_match_nodes(",^audiodatarate,",$arbre, $audiobitrate_array);
				$infos['audiobitrate'] = floor($audiobitrate_array['audiodatarate'][0]*1000);
			}
		}
	}
	
	foreach($infos as $key => $val){
		if(!$val){
			unset($infos[$key]);
		}	
	}
	if(count($infos) > 0){
		include_spip('inc/modifier');
		revision_document($id_document, $infos);
	}
	return true;
}
?>