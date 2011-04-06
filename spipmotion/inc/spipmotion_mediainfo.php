<?php 
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

/**
 * Récupération des métadonnées via MediaInfo
 * @param string $chemin
 */
function inc_spipmotion_mediainfo_dist($chemin){
	$infos = array();
	if(file_exists($chemin)){
		$metadatas = shell_exec("mediainfo -f --Output=XML $chemin");
		include_spip('inc/xml');
		$arbre = spip_xml_parse($metadatas);
		spip_xml_match_nodes(",^track type,",$arbre, $tracks);
		foreach($tracks as $track => $info){
			if($track == 'track type="General"'){
				$infos['duree'] = $info[0]['Duration'][0] / 1000;
				$infos['bitrate'] = $info[0]['Overall_bit_rate'][0];
			}
			if($track == 'track type="Video"'){
				$infos['videobitrate'] = $info[0]['Bit_rate'][0];
				$infos['hauteur'] = $info[0]['Height'][0];
				$infos['largeur'] = $info[0]['Width'][0];
				$infos['videocodec'] = $info[0]['Format'][0];
				$infos['framerate'] = $info[0]['Frame_rate'][0];
				$infos['framecount'] = $info[0]['Frame_count'][0];
				$infos['hasvideo'] = 'oui';
			}
			if($track == 'track type="Audio"'){
				$infos['hasaudio'] = 'oui';
				$infos['audiobitrate'] = $info[0]['Bit_rate'][0];
				$infos['audiochannels'] = $info[0]['Channel_s_'][0];
				$infos['canaux'] = $info[0]['Channel_s_'][0];
				$infos['audiosamplerate'] = $info[0]['Sampling_rate'][0];
				$infos['audiocodec'] = $info[0]['Codec'][0];
			}
		}
	}
	return $infos;
}
?>