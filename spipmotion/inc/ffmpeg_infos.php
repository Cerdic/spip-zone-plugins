<?php

function inc_ffmpeg_infos_dist(){
	spip_log(_DIR_CACHE,'spipmotion');
	if(!is_dir(_DIR_CACHE.'spipmotion')){
		sous_repertoire(_DIR_CACHE,'spipmotion');
	}

	$infos_codecs = ffmpeg_recuperer_infos_codecs();
	return $infos_codecs;
}

/**
 * Récupération des informations sur les codecs disponibles
 */
function ffmpeg_recuperer_infos_codecs(){
	$chemin = lire_config('spipmotion/chemin','');
	$chemin_fichier = _DIR_CACHE.'spipmotion/ffmpeg_codecs';

	if(!$chemin){
		return false;
	}

	/**
	 * On crée un fichier contenant l'ensemble de la conf de ffmpeg
	 */
	exec($chemin.' -formats &> '.$chemin_fichier,$retour,$bool);
	exec($chemin.' -codecs >> '.$chemin_fichier,$retour,$bool);
	exec($chemin.' -bsfs >> '.$chemin_fichier,$retour,$bool);
	exec($chemin.' -protocols >> '.$chemin_fichier,$retour,$bool);
	exec($chemin.' -filters >> '.$chemin_fichier,$retour,$bool);
	exec($chemin.' -pix_fmts >> '.$chemin_fichier,$retour,$bool);

	if (lire_fichier($chemin_fichier, $contenu)){
		$data = array();
		$buffer = $contenu;
		$data['compiler'] = array();
		$look_ups = array('version' => 'FFmpeg version', 'built' => 'built', 'configuration'=>'configuration: ', 'formats'=>'File formats:', 'codecs'=>'Codecs:', 'filters'=>'Bitstream filters:', 'protocols'=>'Supported file protocols:','pix_formats' => 'Pixel formats:', 'abbreviations'=>'Frame size, frame rate abbreviations:', 'Note:');
		$total_lookups = count($look_ups);
		$pregs = array();
		$indexs = array();
		foreach($look_ups as $key=>$reg){
			if(strpos($buffer, $reg) !== false){
				$index = array_push($pregs, $reg);
				$indexs[$key] = $index;
				spip_log($reg,'spipmotion');
			}
		}
		preg_match('/'.implode('(.*)', $pregs).'/s', $buffer, $matches);

		/**
		 * Récupération des informations de versions
		 */
		$data['compiler']['versions'] = array();
		$version = trim($matches[$indexs['version']]);
		preg_match_all('/([a-zA-Z0-9\-]+[0-9\.]+)/', $version, $versions);
		$data['compiler']['ffmpeg_version'] = $versions[0][0];
		preg_match_all('/([a-zA-Z0-9\-]+) version ([0-9\.]+)/', $version, $versions);
		for($i=0, $a=count($versions[0]); $i<$a; $i++){
			$data['compiler']['versions'][strtolower(trim($versions[1][$i]))] = $versions[2][$i];
		}

		/**
		 * Récupération des éléments de configuration
		 */
		$configuration = trim($matches[$indexs['configuration']]);
		// grab the ffmpeg configuration flags
		preg_match_all('/--[a-zA-Z0-9\-]+/', $configuration, $config_flags);
		$data['compiler']['configuration'] = $config_flags[0];
		$data['compiler']['vhook-support'] = in_array('--enable-vhook', $config_flags[0]) && !in_array('--disable-vhook', $config_flags[0]);

		/**
		 * On récupère le numéro de version de gcc, la date de compilation et la version de gcc utilisée
		 */
		$build = trim($matches[$indexs['built']]);

		preg_match('/on (.*) with gcc (.*)/', $build, $conf);
		if(count($conf) > 0){
			$data['compiler']['gcc'] = $conf[2];
			$data['compiler']['build_date'] = $conf[1];
			$data['compiler']['build_date_timestamp'] = strtotime($conf[1]);
		}

		/**
		 * Récupération des formats disponibles
		 */
		preg_match_all('/ (DE|D|E) (.*) {1,} (.*)/', trim($matches[$indexs['formats']]), $formats);
		$data['formats'] = array();
		// 	loop and clean
		for($i=0, $a=count($formats[0]); $i<$a; $i++){
			$data['formats'][strtolower(trim($formats[2][$i]))] = array(
				'encode' 	=> $formats[1][$i] == 'DE' || $formats[1][$i] == 'E',
				'decode' 	=> $formats[1][$i] == 'DE' || $formats[1][$i] == 'D',
				'fullname'	=> $formats[3][$i]
			);
		}

		// grab the bitstream filters available to ffmpeg
		$filters = trim($matches[$indexs['filters']]);
		$data['filters'] = empty($filters) ? array() : explode(' ', $filters);
		// grab the file prototcols available to ffmpeg
		$protocols = trim($matches[$indexs['protocols']]);
		$data['protocols'] = empty($protocols) ? array() : explode(' ', str_replace(':', '', $protocols));
		// grab the abbreviations available to ffmpeg
		$abbreviations = trim($matches[$indexs['abbreviations']]);
		$data['abbreviations'] = empty($abbreviations) ? array() : explode(' ', $abbreviations);
	}
	spip_log($data['compiler'],'spipmotion');
	return $data;
}
?>