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

function inc_ffmpeg_infos_dist($forcer=false){
	$infos_codecs = ffmpeg_recuperer_infos_codecs($forcer);
	return $infos_codecs;
}

/**
 * Récupération des informations sur les codecs disponibles
 */
function ffmpeg_recuperer_infos_codecs($forcer){

	if($forcer){
		if(!is_dir(_DIR_CACHE.'spipmotion')){
			sous_repertoire(_DIR_CACHE,'spipmotion');
		}
		$chemin = lire_config('spipmotion/chemin','ffmpeg');
		$chemin_fichier = _DIR_CACHE.'spipmotion/ffmpeg_codecs';
		$chemin_out = _DIR_CACHE.'spipmotion/out';

		if(!$chemin){
			return false;
		}

		if($GLOBALS['meta']['spipmotion_safe_mode'] == 'oui'){
			$spipmotion_sh = $GLOBALS['meta']['spipmotion_safe_mode_exec_dir'].'/spipmotion.sh'; 
		}else{
			$spipmotion_sh = find_in_path('script_bash/spipmotion.sh');
		}
		/**
		 * On crée un fichier contenant l'ensemble de la conf de ffmpeg
		 */
		supprimer_fichier($chemin_fichier);
		
		spimotion_write($chemin_fichier,"==VERSION==");
		exec($spipmotion_sh.' --info "-version" --log '.$chemin_fichier,$retour,$bool);
		spimotion_write($chemin_fichier,"==FORMATS==");
		exec($spipmotion_sh.' --info "-formats" --log '.$chemin_fichier,$retour,$bool);
		spimotion_write($chemin_fichier,"==CODECS==");
		exec($spipmotion_sh.' --info "-codecs" --log '.$chemin_fichier,$retour,$bool);
		spimotion_write($chemin_fichier,"==BSFS==");
		exec($spipmotion_sh.' --info "-bsfs" --log '.$chemin_fichier,$retour,$bool);
		spimotion_write($chemin_fichier,"==PROTOCOLS==");
		exec($spipmotion_sh.' --info "-protocols" --log '.$chemin_fichier,$retour,$bool);
		spimotion_write($chemin_fichier,"==FILTERS==");
		exec($spipmotion_sh.' --info "-filters" --log '.$chemin_fichier,$retour,$bool);
		spimotion_write($chemin_fichier,"==PIX_FMTS==");
		exec($spipmotion_sh.' --info "-pix_fmts" --log '.$chemin_fichier,$retour,$bool);
		spimotion_write($chemin_fichier,"==fin==");

		if (lire_fichier($chemin_fichier, $contenu)){
			$data = array();
			$look_ups = array(
				'version' => 'FFmpeg version',
				'configuration'=>' configuration: ',
				'formats'=>'File formats:',
				'codecs'=>'Codecs:',
				'bitstream_filters'=>'Bitstream filters:',
				'protocols'=>'Supported file protocols:',
				'avfilters' => 'Filters',
				'pix_formats' => 'Pixel formats:',
				'abbreviations'=>'Frame size, frame rate abbreviations:',
				'fin' => '==fin==');
			$total_lookups = count($look_ups);
			$pregs = array();
			$indexs = array();
			foreach($look_ups as $key=>$reg){
				if(strpos($contenu, $reg) !== false){
					$index = array_push($pregs, $reg);
					$indexs[$key] = $index;
				}
			}

			preg_match('/'.implode('(.*)', $pregs).'/s', $contenu, $matches);

			/**
			 * Récupération des informations de version
			 */
			$data['spipmotion_compiler'] = array();
			$data['spipmotion_compiler']['versions'] = array();

			$version = trim($matches[$indexs['version']]);
			preg_match('/([a-zA-Z0-9\-]+[0-9\.]+).* on (.*) with gcc (.*)/s', $version, $versions);
			$data['spipmotion_compiler']['ffmpeg_version'] = $versions[1];
			$data['spipmotion_compiler']['gcc'] = $versions[3];
			$data['spipmotion_compiler']['build_date'] = $versions[2];
			$data['spipmotion_compiler']['build_date_timestamp'] = strtotime($versions[2]);

			/**
			 * Récupération des éléments de configuration
			 */
			$configuration = trim($matches[$indexs['configuration']]);
			preg_match_all('/--[a-zA-Z0-9\-]+/', $configuration, $config_flags);
			ksort($config_flags[0]);
			$data['spipmotion_compiler']['configuration'] = $config_flags[0];

			// Replace old vhook support
			$data['spipmotion_compiler']['avfilter-support'] = in_array('--enable-avfilter', $config_flags[0]) && !in_array('--disable-avfilter', $config_flags[0]);
			//$data['compiler']['vhook-support'] = in_array('--enable-vhook', $config_flags[0]) && !in_array('--disable-vhook', $config_flags[0]);

			if(extension_loaded('ffmpeg')){
				$data['spipmotion_compiler']['ffmpeg-php'] = true;
				$data['spipmotion_compiler']['ffmpeg-php-infos']['ffmpeg-php-version'] = FFMPEG_PHP_VERSION_STRING;
				$data['spipmotion_compiler']['ffmpeg-php-infos']['ffmpeg-php-builddate'] = FFMPEG_PHP_BUILD_DATE_STRING;
				$data['spipmotion_compiler']['ffmpeg-php-infos']['libavcodec_build_number'] = LIBAVCODEC_BUILD_NUMBER;
				$data['spipmotion_compiler']['ffmpeg-php-infos']['libavcodec_version_number'] = LIBAVCODEC_VERSION_NUMBER;
				$data['spipmotion_compiler']['ffmpeg-php-infos']['ffmpeg-php-gdenabled'] = FFMPEG_PHP_GD_ENABLED;
			}else{
				$data['spipmotion_compiler']['ffmpeg-php'] = false;
			}

			/**
			 * Récupération des formats disponibles
			 * Pour chaque format reconnu on retourne un array avec
			 */
			preg_match_all('/ (DE|D|E) (.*) {1,} (.*)/', trim($matches[$indexs['formats']]), $formats);
			$data['spipmotion_formats'] = array();
			for($i=0, $a=count($formats[0]); $i<$a; $i++){
				$data['spipmotion_formats'][strtolower(trim($formats[2][$i]))] = array(
					'encode' 	=> $formats[1][$i] == 'DE' || $formats[1][$i] == 'E',
					'decode' 	=> $formats[1][$i] == 'DE' || $formats[1][$i] == 'D',
					'fullname'	=> $formats[3][$i]
				);
			}
			ecrire_meta('spipmotion_formats',serialize($data['spipmotion_formats']));

			/**
			 * Récupération des codecs disponibles
			 */
			preg_match_all('/ (D| )(E| )(V|A|S)(S| )(D| )(T| ) (.*) {1,} (.*)/', trim($matches[$indexs['codecs']]), $codecs);
			$data['spipmotion_codecs'] = array();
			$data['spipmotion_codecs_audio_decode'] = array();
			$data['spipmotion_codecs_video_decode'] = array();
			$data['spipmotion_codecs_audio_encode'] = array();
			$data['spipmotion_codecs_video_encode'] = array();
			for($i=0, $a=count($codecs[0]); $i<$a; $i++){
				$data['spipmotion_codecs'][strtolower(trim($codecs[7][$i]))] = array(
					'decode' 	=> $codecs[1][$i] == 'D',
					'encode' 	=> $codecs[2][$i] == 'E',
					'type'	=> $codecs[3][$i],
					'draw_horiz_band'	=> $codecs[4][$i] == 'S',
					'direct_rendering'	=> $codecs[5][$i] == 'D',
					'weird_frame_truncation' => $codecs[6][$i] == 'T',
					'fullname' => $codecs[8][$i]
				);
				if(($codecs[1][$i] == 'D') && ($codecs[3][$i] == 'A'))
					$data['spipmotion_codecs_audio_decode'][] = trim($codecs[7][$i]);
				if(($codecs[1][$i] == 'D') && ($codecs[3][$i] == 'V'))
					$data['spipmotion_codecs_video_decode'][] = trim($codecs[7][$i]);
				if(($codecs[2][$i] == 'E') && ($codecs[3][$i] == 'A'))
					$data['spipmotion_codecs_audio_encode'][] = trim($codecs[7][$i]);
				if(($codecs[2][$i] == 'E') && ($codecs[3][$i] == 'V'))
					$data['spipmotion_codecs_video_encode'][] = trim($codecs[7][$i]);
			}
			ecrire_meta('spipmotion_codecs',serialize($data['spipmotion_codecs']));
			ecrire_meta('spipmotion_codecs_audio_decode',serialize($data['spipmotion_codecs_audio_decode']));
			ecrire_meta('spipmotion_codecs_video_decode',serialize($data['spipmotion_codecs_video_decode']));
			ecrire_meta('spipmotion_codecs_audio_encode',serialize($data['spipmotion_codecs_audio_encode']));
			ecrire_meta('spipmotion_codecs_video_encode',serialize($data['spipmotion_codecs_video_encode']));

			/**
			 * On récupère les filtres bitstream disponibles
			 */
			$bitstream_filters = trim($matches[$indexs['bitstream_filters']]);
			$data['spipmotion_bitstream_filters'] = empty($bitstream_filters) ? array() : preg_split('/\n/', $bitstream_filters);
			ecrire_meta('spipmotion_bitstream_filters',serialize($data['spipmotion_bitstream_filters']));

			/**
			 * On récupère les protocoles disponibles
			 */
			$protocols = trim($matches[$indexs['protocols']]);
			$data['spipmotion_protocols'] = empty($protocols) ? array() : preg_split('/\n/', str_replace(':', '', $protocols));
			ecrire_meta('spipmotion_protocols',serialize($data['spipmotion_protocols']));

			/**
			 * On récupère la liste des filtres avfilter
			 */
			preg_match_all('/(.*) {1,} (.*)/', trim($matches[$indexs['avfilters']]), $filters);
			$data['spipmotion_avfilters'] = array();
			for($i=0, $a=count($filters[0]); $i<$a; $i++){
				$data['spipmotion_avfilters'][strtolower(trim($filters[1][$i]))] = array(
					'nom' 	=> trim($filters[1][$i]),
					'description' 	=> trim($filters[2][$i]) == '(null)' ? false : trim($filters[2][$i]),
				);
			}
			if(empty($data['spipmotion_avfilters']))
				$data['spipmotion_compiler']['avfilter-support'] = false;
			ksort($data['spipmotion_avfilters']);

			ecrire_meta('spipmotion_avfilters',serialize($data['spipmotion_avfilters']));

			ecrire_meta('spipmotion_compiler',serialize($data['spipmotion_compiler']));

			/**
			 * On regarde si ffmpeg2theora est installé
			 * Si oui on ajoute sa version dans les metas aussi
			 */
			$ffmpeg2theora = exec('ffmpeg2theora',$retour_theora,$int);
			if(!empty($retour_theora)){
				$info = $retour_theora[0];
				preg_match('/ffmpeg2theora ([0-9a-z].*) - ([A-Z].*)/s',$info,$infos);
				$data['spipmotion_ffmpeg2theora']['ffmpeg2theora'] = true;
				$data['spipmotion_ffmpeg2theora']['version'] = $infos[1];
				$data['spipmotion_ffmpeg2theora']['libtheora_version'] = $infos[2];
				ecrire_meta('spipmotion_ffmpeg2theora',serialize($data['spipmotion_ffmpeg2theora']));
			}
		}
	}else{
		$data['spipmotion_compiler'] = unserialize($GLOBALS['meta']['spipmotion_compiler']);
		$data['spipmotion_formats'] = unserialize($GLOBALS['meta']['spipmotion_formats']);
		$data['spipmotion_codecs'] = unserialize($GLOBALS['meta']['spipmotion_codecs']);
		$data['spipmotion_bitstream_filters'] = unserialize($GLOBALS['meta']['spipmotion_bitstream_filters']);
		$data['spipmotion_protocols'] = unserialize($GLOBALS['meta']['spipmotion_protocols']);
		$data['spipmotion_avfilters'] = unserialize($GLOBALS['meta']['spipmotion_avfilters']);
		$data['spipmotion_ffmpeg2theora'] = unserialize($GLOBALS['meta']['spipmotion_ffmpeg2theora']);
	}
	return $data;
}

function spimotion_write($chemin_fichier,$what){
	$f = @fopen($chemin_fichier, "ab");
	if ($f) {
		fputs($f, "$what\n");
		fclose($f);
	}
}
?>