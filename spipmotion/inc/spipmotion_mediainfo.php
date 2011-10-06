<?php 
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2011 - Distribué sous licence GNU/GPL
 *
 */

/**
 * Récupération des métadonnées via MediaInfo
 * @param string $chemin
 */
function inc_spipmotion_mediainfo_dist($chemin,$id_document,$only_cover=false){
	$infos = array();
	if(file_exists($chemin)){
		$metadatas = shell_exec("mediainfo -f --Output=XML $chemin");
		include_spip('inc/xml');
		$arbre = spip_xml_parse($metadatas);
		spip_xml_match_nodes(",^track type,",$arbre, $tracks);
		foreach($tracks as $track => $info){
			$metas[$track] = $info;
			if($track == 'track type="General"'){
				$infos['titre'] = $info[0]['Title'][0] ? $info[0]['Title'][0] : ($info[0]['Movie_name'][0] ? $info[0]['Movie_name'][0] : $info[0]['Track_name '][0]);
				$infos['descriptif'] = $info[0]['Description'][0] ? $info[0]['Description'][0] : $info[0]['desc'][0];
				$infos['credits'] .= $info[0]['Performer'][0]? $info[0]['Performer'][0].($info[0]['Copyright'][0] ? ' - '.$info[0]['Copyright'][0] : '') : $info[0]['Copyright'][0] ;
				$infos['duree'] = $info[0]['Duration'][0] / 1000;
				$infos['bitrate'] = $info[0]['Overall_bit_rate'][0];
			}
			if($track == 'track type="Video"'){
				if(!$infos['titre'])
					$infos['titre'] = $info[0]['Title'][0] ? $info[0]['Title'][0] : '';
				$infos['videobitrate'] = $info[0]['Bit_rate'][0] ? $info[0]['Bit_rate'][0] : ($info[0]['Nominal_bit_rate'][0] ? $info[0]['Nominal bit rate'][0] : '');
				$infos['hauteur'] = $info[0]['Height'][0];
				$infos['largeur'] = $info[0]['Width'][0];
				$infos['videocodec'] = $info[0]['Format'][0];
				$infos['framerate'] = $info[0]['Frame_rate'][0];
				$infos['framecount'] = $info[0]['Frame_count'][0];
				$infos['rotation'] = intval($info[0]['Rotation'][0]);
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
	if(!$infos['hasaudio']){
		$infos['hasaudio'] = 'non';
	}
	if(!$infos['hasvideo']){
		$infos['hasvideo'] = 'non';
	}
	$infos['metadatas'] = serialize($metas);
	return $infos;
}
?>