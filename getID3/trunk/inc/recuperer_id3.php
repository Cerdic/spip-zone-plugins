<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Récupère le contenu des tags id3 et des données audio d'un fichier
 *
 * Dans le cas où le fichier contient un ou plusieurs logo,
 * ces fichiers sont écris dans le répertoire tmp/
 *
 * @param string $fichier
 * @param string $info
 * @param string $mime
 * @return array Le contenu complet des tags id3 et des données audio
 */
function inc_recuperer_id3_dist($fichier,$info=null,$mime=null){
	include_spip('getid3/getid3');
	$getID3 = new getID3;
	$getID3->setOption(array('tempdir' => _DIR_TMP));

	// Scan file - should parse correctly if file is not corrupted
	$file_info = $getID3->analyze($fichier);
	getid3_lib::CopyTagsToComments($file_info);
	if(sizeof($file_info)>0){
		// Cover art?
		if(isset($file_info['id3v2']['APIC'])){
			foreach($file_info['id3v2']['APIC'] as $cle=>$val){
				if (isset($file_info['id3v2']['APIC'][$cle]['data']) && isset($file_info['id3v2']['APIC'][$cle]['image_mime']) && isset($file_info['id3v2']['APIC'][$cle]['dataoffset'])) {
					$imageinfo = array();
		            $imagechunkcheck = getid3_lib::GetDataImageSize($file_info['id3v2']['APIC'][$cle]['data'],$imageinfo);
		            $extension = getid3_lib::ImageTypesLookup($imagechunkcheck[2]);
		            if($extension == 'jpeg')
		            	$extension = 'jpg';
		            $tmp_file = 'getid3-'.$file_info['id3v2']['APIC'][$cle]['dataoffset'].'.'.$extension;
		            $dest = sous_repertoire(_DIR_VAR, 'cache-getid3');
					$dest = $dest.$tmp_file;
					if ($ok = ecrire_fichier($dest, $file_info['id3v2']['APIC'][$cle]['data'])) {
						$id3['cover'.$cle] = $dest;
					}
				}
			}
		}
		if(isset($file_info['flac']['APIC'])){
			if (isset($file_info['flac']['APIC']['data']) && isset($file_info['flac']['APIC']['image_mime'])) {
				spip_log($file_info['flac']['APIC']['image_mime'],'getid3');
	            $extension = strtolower($file_info['flac']['APIC']['extension']);
	            if($extension == 'jpeg')
	            	$extension = 'jpg';
	            $tmp_file = 'getid3-'.md5($file_info['filename'].$file_info['filesize']).'.'.$extension;
	            $dest = sous_repertoire(_DIR_VAR, 'cache-getid3');
				$dest = $dest.$tmp_file;
				if ($ok = ecrire_fichier($dest, $file_info['flac']['APIC']['data'])) {
					$id3['cover'.$cle] = $dest;
				}
			}
		}
		if(isset($file_info['comments_html'])){
			foreach($file_info['comments_html'] as $cle=>$val){
				$id3[$cle] = array_pop($val);
			}
		}
		/**
		 * Cas des flac et ogg (certainement)
		 */
		if(isset($id3['date']) && !isset($id3['year'])){
			$id3['year'] = $id3['date']; 
		}
		$id3['format'] = $file_info['audio']['dataformat'];
		$id3['lossless'] = $file_info['audio']['lossless'];
		$id3['audiosamplerate'] = $file_info['audio']['sample_rate'] ;
		$id3['bits'] = $file_info['audio']['bits_per_sample'];
		if(is_array($file_info['tags']['id3v2']['track'])){
			$id3['track'] = array_pop($file_info['tags']['id3v2']['track']);
		}
		$id3['codec'] = ($file_info['audio']['encoder']) ? $file_info['audio']['encoder'] : $file_info['audio']['codec'];
		if(is_array($file_info['tags']['id3v2']['totaltracks'])){
			$id3['totaltracks'] = array_pop($file_info['tags']['id3v2']['totaltracks']);
		}
		$id3['bitrate'] = $file_info['audio']['bitrate'];
		$id3['bitrate_mode'] = $file_info['audio']['bitrate_mode'];
		$id3['duree_secondes'] = $file_info['playtime_seconds'];
		$id3['duree'] = $file_info['playtime_string'];
		$id3['channels'] = $file_info['audio']['channels'];
		$id3['channel_mode'] = $file_info['audio']['channelmode'];
		$id3['mime'] = $file_info['mime_type'];
	}
	if(!$info){
		return $id3;
	}
	else{
		return $id3[$info];
	}
}
?>