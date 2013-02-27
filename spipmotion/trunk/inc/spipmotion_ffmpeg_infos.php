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

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_spipmotion_ffmpeg_infos_dist($forcer=false){
	$infos_codecs = ffmpeg_recuperer_infos_codecs($forcer);
	return $infos_codecs;
}

/**
 * Récupération des informations sur les codecs disponibles
 */
function ffmpeg_recuperer_infos_codecs($forcer){
	
	if($forcer){
		include_spip('inc/config');
		if(!is_dir(_DIR_CACHE.'spipmotion')){
			sous_repertoire(_DIR_CACHE,'spipmotion');
		}
		$chemin = lire_config('spipmotion/chemin','ffmpeg');
		$chemin_fichier = _DIR_CACHE.'spipmotion/ffmpeg_codecs';
		$chemin_out = _DIR_CACHE.'spipmotion/out';
		
		/**
		 * On recharge les logiciels
		 */
		$verifier_binaires = charger_fonction('spipmotion_verifier_binaires','inc');
		$verifier_binaires();
		if($GLOBALS['spipmotion_metas']['spipmotion_safe_mode'] == 'oui'){
			$spipmotion_sh = $GLOBALS['spipmotion_metas']['spipmotion_safe_mode_exec_dir'].'/spipmotion.sh'; 
		}else{
			$spipmotion_sh = find_in_path('script_bash/spipmotion.sh');
		}
		/**
		 * On crée un fichier contenant l'ensemble de la conf de ffmpeg
		 */
		supprimer_fichier($chemin_fichier);
		spimotion_write($chemin_fichier,"==VERSION==\n");
		exec($spipmotion_sh.' --info "-version" --log '.$chemin_fichier,$retour,$bool);
		spimotion_write($chemin_fichier.'_formats',"\n==FORMATS==\n");
		exec($spipmotion_sh.' --info "-formats" --log '.$chemin_fichier.'_formats',$retour,$bool);
		spimotion_write($chemin_fichier.'_codecs',"\n==CODECS==\n");
		exec($spipmotion_sh.' --info "-codecs" --log '.$chemin_fichier.'_codecs',$retour,$bool);
		spimotion_write($chemin_fichier,"\n==BSFS==\n");
		exec($spipmotion_sh.' --info "-bsfs" --log '.$chemin_fichier,$retour,$bool);
		spimotion_write($chemin_fichier,"\n==FILTERS==\n");
		exec($spipmotion_sh.' --info "-filters" --log '.$chemin_fichier,$retour,$bool);
		spimotion_write($chemin_fichier,"\n==PIX_FMTS==\n");
		exec($spipmotion_sh.' --info "-pix_fmts" --log '.$chemin_fichier,$retour,$bool);
		spimotion_write($chemin_fichier,"\n==PROTOCOLS==\n");
		exec($spipmotion_sh.' --info "-protocols" --log '.$chemin_fichier,$retour,$bool);
		spimotion_write($chemin_fichier,"\n==FIN==");
		
		if (lire_fichier($chemin_fichier, $contenu)){
			$contenu=trim($contenu);
			$data = array();
			$look_ups = array(
				'version' => 'ffmpeg version',
				'configuration'=>'configuration:',
				'bitstream_filters'=>'==BSFS==',
				'avfilters' => 'Filters:',
				'pix_formats' => '==PIX_FMTS==',
				'abbreviations'=>'Frame size, frame rate abbreviations:',
				'protocols'=>'==PROTOCOLS==',
				'fin' => '==FIN=='
			);
			$total_lookups = count($look_ups);
			$pregs = array();
			$indexs = array();
			foreach($look_ups as $key=>$reg){
				if(strpos($contenu, $reg) !== false){
					$index = array_push($pregs, $reg);
					$indexs[$key] = $index;
				}
			}
			$result = preg_match('/'.implode('(.*)', $pregs).'/s', $contenu, $matches);

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
			$data['spipmotion_compiler']['avfilter-support'] = (in_array('--enable-avfilter', $config_flags[0]) && !in_array('--disable-avfilter', $config_flags[0]) ? '1' : '0');

			/**
			 * Récupération des formats disponibles
			 * Pour chaque format reconnu on retourne un array avec
			 */
			if (lire_fichier($chemin_fichier.'_formats', $contenu_formats)){
				preg_match_all('/ (DE|D|E) (.*) {1,} (.*)/', $contenu_formats, $formats);
				$data['spipmotion_formats'] = array();
				for($i=0, $a=count($formats[0]); $i<$a; $i++){
					$data['spipmotion_formats'][strtolower(trim($formats[2][$i]))] = array(
						'encode' 	=> $formats[1][$i] == 'DE' || $formats[1][$i] == 'E',
						'decode' 	=> $formats[1][$i] == 'DE' || $formats[1][$i] == 'D',
						'fullname'	=> $formats[3][$i]
					);
				}
				ecrire_meta('spipmotion_formats',serialize($data['spipmotion_formats']),'','spipmotion_metas');
			}

			/**
			 * Récupération des codecs disponibles
			 */
			if (lire_fichier($chemin_fichier.'_codecs', $contenu_codecs)){
				preg_match_all('/ (D| |\.)(E| |\.)(V|A|S|\.)(S| |\.|I)(D|L| |\.)(T|S| ) (.*) {1,} (.*)/', $contenu_codecs, $codecs);
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
				ecrire_meta('spipmotion_codecs',serialize($data['spipmotion_codecs']),'','spipmotion_metas');
				ecrire_meta('spipmotion_codecs_audio_decode',serialize($data['spipmotion_codecs_audio_decode']),'','spipmotion_metas');
				ecrire_meta('spipmotion_codecs_video_decode',serialize($data['spipmotion_codecs_video_decode']),'','spipmotion_metas');
				ecrire_meta('spipmotion_codecs_audio_encode',serialize($data['spipmotion_codecs_audio_encode']),'','spipmotion_metas');
				ecrire_meta('spipmotion_codecs_video_encode',serialize($data['spipmotion_codecs_video_encode']),'','spipmotion_metas');
			}

			/**
			 * On récupère les filtres bitstream disponibles
			 */
			$bitstream_filters = trim($matches[$indexs['bitstream_filters']]);
			$data['spipmotion_bitstream_filters'] = empty($bitstream_filters) ? array() : preg_split('/\n/', $bitstream_filters);
			ecrire_meta('spipmotion_bitstream_filters',serialize($data['spipmotion_bitstream_filters']),'','spipmotion_metas');

			/**
			 * On récupère les protocoles disponibles
			 */
			$protocols = trim($matches[$indexs['protocols']]);
			$data['spipmotion_protocols'] = empty($protocols) ? array() : preg_split('/\n/', str_replace(':', '', $protocols));
			ecrire_meta('spipmotion_protocols',serialize($data['spipmotion_protocols']),'','spipmotion_metas');

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

			ecrire_meta('spipmotion_avfilters',serialize($data['spipmotion_avfilters']),'','spipmotion_metas');

			ecrire_meta('spipmotion_compiler',serialize($data['spipmotion_compiler']),'','spipmotion_metas');
			
			/**
			 * On regarde si spipmotion.sh est utilisable
			 */
			$spipmotion_infos_sh = exec($spipmotion_sh.' --help',$retour_spipmotion_sh,$int_spipmotion_sh);
			if(!empty($retour_spipmotion_sh)){
				$info = $retour_spipmotion_sh[1];
				preg_match('/SPIPmotion v([0-9a-z].*)/s',$info,$infos);
				$data['spipmotion_spipmotion_sh']['spipmotion_sh'] = true;
				$data['spipmotion_spipmotion_sh']['chemin'] = $spipmotion_sh;
				$data['spipmotion_spipmotion_sh']['version'] = $infos[1];
				ecrire_meta('spipmotion_spipmotion_sh',serialize($data['spipmotion_spipmotion_sh']),'','spipmotion_metas');
			}

			/**
			 * On regarde si spipmotion_vignettes.sh est utilisable
			 */
			if($GLOBALS['spipmotion_metas']['spipmotion_safe_mode'] == 'oui'){
				$spipmotion_sh_vignettes = $GLOBALS['spipmotion_metas']['spipmotion_safe_mode_exec_dir'].'/spipmotion_vignette.sh'; 
			}else{
				$spipmotion_sh_vignettes = find_in_path('script_bash/spipmotion_vignette.sh');
			}
			$spipmotion_sh_vignettes_infos = exec($spipmotion_sh_vignettes.' --help',$retour_spipmotion_sh_vignettes,$int_spipmotion_sh_vignettes);
			if(!empty($retour_spipmotion_sh_vignettes)){
				$info = $retour_spipmotion_sh_vignettes[2];
				preg_match('/SPIPmotion vignette v([0-9a-z].*)/s',$info,$infos);
				$data['spipmotion_spipmotion_sh_vignettes']['spipmotion_sh_vignettes'] = true;
				$data['spipmotion_spipmotion_sh_vignettes']['chemin'] = $spipmotion_sh_vignettes;
				$data['spipmotion_spipmotion_sh_vignettes']['version'] = $infos[1];
				ecrire_meta('spipmotion_spipmotion_sh_vignettes',serialize($data['spipmotion_spipmotion_sh_vignettes']),'','spipmotion_metas');
			}else{
				$data['spipmotion_spipmotion_sh_vignettes']['spipmotion_sh_vignettes'] = false;
				if(strlen($spipmotion_sh_vignettes))
					$data['spipmotion_spipmotion_sh_vignettes']['chemin'] = $spipmotion_sh_vignettes;
				ecrire_meta('spipmotion_spipmotion_sh_vignettes',serialize($data['spipmotion_spipmotion_sh_vignettes']),'','spipmotion_metas');
			}
			/**
			 * On regarde si ffmpeg2theora est installé
			 * http://v2v.cc/~j/ffmpeg2theora/
			 * Si oui on ajoute sa version dans les metas aussi
			 */
			$ffmpeg2theora = exec('ffmpeg2theora',$retour_theora,$int);
			if(!empty($retour_theora)){
				$info = $retour_theora[0];
				preg_match('/ffmpeg2theora ([0-9a-z].*) - ([A-Z].*)/s',$info,$infos);
				$data['spipmotion_ffmpeg2theora']['ffmpeg2theora'] = true;
				$data['spipmotion_ffmpeg2theora']['version'] = $infos[1];
				$data['spipmotion_ffmpeg2theora']['libtheora_version'] = $infos[2];
				ecrire_meta('spipmotion_ffmpeg2theora',serialize($data['spipmotion_ffmpeg2theora']),'','spipmotion_metas');
			}
			
			/**
			 * On regarde si flvtool2 est installé
			 * http://www.inlet-media.de/flvtool2/
			 * Si oui on ajoute sa version dans les metas aussi
			 */
			$flvtool2 = exec('flvtool2',$retour_flvtool2,$int_flvtool2);
			if(!empty($retour_flvtool2)){
				$info = $retour_flvtool2[0];
				preg_match('/FLVTool2 ([0-9a-z].*)/s',$info,$infos);
				$data['spipmotion_flvtool2']['flvtool2'] = true;
				$data['spipmotion_flvtool2']['version'] = $infos[1];
				ecrire_meta('spipmotion_flvtool2',serialize($data['spipmotion_flvtool2']),'','spipmotion_metas');
			}
			
			/**
			 * On regarde si flvtool++ est installé
			 * http://mirror.facebook.net/facebook/flvtool++/
			 * Si oui on ajoute sa version dans les metas aussi
			 */
			$flvtoolplus = exec('flvtool++',$retour_flvtoolplus,$int_flvtoolplus);
			if(!empty($retour_flvtoolplus)){
				$info = $retour_flvtoolplus[0];
				preg_match('/flvtool\+\+ ([0-9a-z].*)/s',$info,$infos);
				$data['spipmotion_flvtoolplus']['flvtoolplus'] = true;
				$data['spipmotion_flvtoolplus']['version'] = $infos[1];
				ecrire_meta('spipmotion_flvtoolplus',serialize($data['spipmotion_flvtoolplus']),'','spipmotion_metas');
			}
			
			/**
			 * On regarde si ffprobe est installé
			 * Si oui on dit juste qu'il est présent
			 */
			$ffprobe = exec('ffprobe --version',$retour_ffprobe,$int_ffprobe);
			if($int_mediainfo == 0){
				$data['spipmotion_ffprobe']['ffprobe'] = true;
				$data['spipmotion_ffprobe']['version'] = "present";
				ecrire_meta('spipmotion_ffprobe',serialize($data['spipmotion_ffprobe']),'','spipmotion_metas');
			}

			/**
			 * On regarde si mediainfo est installé
			 * http://mediainfo.sourceforge.net/fr
			 * Si oui on ajoute sa version dans les metas aussi
			 */
			$mediainfo = exec('mediainfo --version',$retour_mediainfo,$int_mediainfo);
			if(!empty($retour_mediainfo)){
				$info = $retour_mediainfo[1];
				preg_match('/MediaInfoLib - ([0-9a-z].*)/s',$info,$infos);
				$data['spipmotion_mediainfo']['mediainfo'] = true;
				$data['spipmotion_mediainfo']['version'] = $infos[1];
				ecrire_meta('spipmotion_mediainfo',serialize($data['spipmotion_mediainfo']),'','spipmotion_metas');
			}
			$inc_meta = charger_fonction('meta', 'inc');
			$inc_meta('spipmotion_metas');
		}
	}else{
		$data = array();
		$data['spipmotion_spipmotion_sh'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_spipmotion_sh']);
		$data['spipmotion_spipmotion_sh_vignettes'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_spipmotion_sh_vignettes']);
		$data['spipmotion_compiler'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_compiler']);
		$data['spipmotion_formats'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_formats']);
		$data['spipmotion_codecs'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_codecs']);
		$data['spipmotion_bitstream_filters'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_bitstream_filters']);
		$data['spipmotion_protocols'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_protocols']);
		$data['spipmotion_avfilters'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_avfilters']);
		$data['spipmotion_ffmpeg2theora'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_ffmpeg2theora']);
		$data['spipmotion_flvtool2'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_flvtool2']);
		$data['spipmotion_flvtoolplus'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_flvtoolplus']);
		$data['spipmotion_mediainfo'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_mediainfo']);
		$data['spipmotion_ffprobe'] = unserialize($GLOBALS['spipmotion_metas']['spipmotion_ffprobe']);
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