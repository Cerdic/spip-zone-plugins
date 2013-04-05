<?php 
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2013 - Distribué sous licence GNU/GPL
 * 
 * Fonction de récupération de métadonnées via ffprobe
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Récupération des métadonnées via FFprobe
 * 
 * @param string $chemin
 * 		Le chemin du fichier à analyser
 * @return array $infos
 * 		Un tableau des informations récupérées
 */
function inc_spipmotion_ffprobe_dist($chemin){
	$infos = $metas = array();
	if(file_exists($chemin)){
		include_spip('inc/filtres');
		ob_start();
		passthru("ffprobe -i '$chemin' -show_format -show_streams 2> /dev/null");
		$metadatas=ob_get_contents();
		ob_end_clean();
		preg_match('/\[FORMAT\](.*)\[\/FORMAT\]/s', $metadatas, $formats);
		if(isset($formats[1])){
			$formats =  explode("\n",trim($formats[1]));
			foreach ($formats as $infos){
				$info = explode('=',$infos);
				if($info[0] == 'duration')
					$metas['duree'] = $info[1];
				if($info[0] == 'bit_rate')
					$metas['bitrate'] = $info[1];
				if(preg_match('/^TAG:.*/',$infos)){
					$info = explode('=',str_replace('TAG:','',$infos));
					$metas[$info[0]] = trim($info[1]);
				}
			}
		}
		preg_match_all('/\[STREAM\](.*)\[\/STREAM\]/sU', $metadatas, $streams);
		if(count($streams) > 1){
			foreach($streams[1] as $stream){
				$stream_final = array();
				$lignes_stream = explode("\n",trim($stream));
				foreach($lignes_stream as $ligne){
					$ligne = explode('=',$ligne);
					$stream_final[$ligne[0]] = $ligne[1];
				}
				if(isset($stream_final['codec_type']) && $stream_final['codec_type'] == 'video'){
					if(isset($stream_final['width']))
						$metas['largeur'] = $stream_final['width'];
					if(isset($stream_final['height']))
						$metas['hauteur'] = $stream_final['height'];
					if(isset($stream_final['nb_frames']) && $stream_final['nb_frames'] != 'N/A')
						$metas['framecount'] = $stream_final['nb_frames'];
					if(isset($stream_final['r_frame_rate'])){
						 $framerate = explode('/',$stream_final['r_frame_rate']);
						 $metas['framerate'] = $framerate[0];
					}
					$metas['hasvideo'] = 'oui';
				}
				if(isset($stream_final['codec_type']) && $stream_final['codec_type'] == 'audio'){
					if(isset($stream_final['channels']))
						$metas['audiochannels'] = $stream_final['channels'];
					if(isset($stream_final['sample_rate']))
						$metas['audiosamplerate'] = intval($stream_final['sample_rate']);
					$metas['hasaudio'] = 'oui';
				}
			}
		}
		if(isset($metas['location'])){
			$coords = preg_match('/((\+|-)\d+\.\d+)((\+|-)\d+\.\d+)((\+|-)\d+\.\d+)\//',$metas['location'],$matches);
			if(isset($matches[1]) && isset($matches[3])){
				$metas['lat'] = $matches[1];
				$metas['lon'] = $matches[3];
			}
		}
	}
	return $metas;
}
?>