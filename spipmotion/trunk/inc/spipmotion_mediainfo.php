<?php 
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Récupération des métadonnées via MediaInfo
 * 
 * @param string $chemin : le chemin du fichier à analyser
 * @return array $infos : un tableau des informations récupérées
 */
function inc_spipmotion_mediainfo_dist($chemin){
	$infos = array();
	if(file_exists($chemin)){
		ob_start();
		passthru("mediainfo -f --Output=XML $chemin");
		$metadatas=ob_get_contents();
		ob_end_clean();
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
				$infos['videobitrate'] = $info[0]['Bit_rate'][0] ? $info[0]['Bit_rate'][0] : ($info[0]['Nominal_bit_rate'][0] ? $info[0]['Nominal_bit_rate'][0] : '');
				$infos['hauteur'] = $info[0]['Height'][0];
				$infos['largeur'] = $info[0]['Width'][0];
				$infos['videocodec'] = $info[0]['Format'][0];
				$infos['videocodecid'] = $info[0]['Codec_ID'][0] ? $info[0]['Codec_ID'][0] : strtolower($info[0]['Format'][0]);
				if($infos['videocodecid'] == 'avc1'){
					if(isset($info[0]['Format_profile'][0])){
						if(preg_match('/^Baseline.*/',$info[0]['Format_profile'][0]))
							$infos['videocodecid'] = 'avc1.42E01E';
						if(preg_match('/^Main.*/',$info[0]['Format_profile'][0]))
							$infos['videocodecid'] = 'avc1.4D401E';
						if(preg_match('/^High.*/',$info[0]['Format_profile'][0]))
							$infos['videocodecid'] = 'avc1.64001E';
					}
				}else if($infos['videocodec'] == 'Sorenson Spark'){
					$infos['videocodecid'] = 'h263';
				}
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
				if($infos['audiocodec'] == 'AAC LC'){
					$infos['audiocodecid'] = 'mp4a.40.2';
				}else if($infos['audiocodec'] == 'MPA1L3'){
					$infos['audiocodecid'] = 'mp3a';
				}else{
					$infos['audiocodecid'] = $info[0]['Codec_ID'][0] ? $info[0]['Codec_ID'][0] : strtolower($info[0]['Codec'][0]);
				}
			}
		}
	}
	if(!$infos['hasaudio']){
		$infos['hasaudio'] = 'non';
	}
	if(!$infos['hasvideo']){
		$infos['hasvideo'] = 'non';
	}
	spip_log($infos,'spipmotion');
	$infos['metadatas'] = serialize($metas);
	return $infos;
}
?>