<?php

function inc_recuperer_id3_dist($fichier,$info=null,$mime=null){
	include_spip('getid3/getid3');
	$getID3 = new getID3;	
	$getID3->setOption(array('tempdir' => find_in_path('tmp/')));

	// Scan file - should parse correctly if file is not corrupted
	$ThisFileInfo = $getID3->analyze($fichier);
	getid3_lib::CopyTagsToComments($ThisFileInfo);
	
	//return $ThisFileInfo;
	if(sizeof($ThisFileInfo)>0){
		// Cover art?
		if(isset($ThisFileInfo['id3v2']['APIC'])){
			foreach($ThisFileInfo['id3v2']['APIC'] as $cle=>$val){
				if (isset($ThisFileInfo['id3v2']['APIC'][$cle]['data']) && isset($ThisFileInfo['id3v2']['APIC'][$cle]['image_mime']) && isset($ThisFileInfo['id3v2']['APIC'][$cle]['dataoffset'])) {
		            $imagechunkcheck = getid3_lib::GetDataImageSize($ThisFileInfo['id3v2']['APIC'][$cle]['data']);
					spip_log($imagechunkcheck);
		            $tmp_file = 'getid3-'.$ThisFileInfo['id3v2']['APIC'][$cle]['dataoffset'].'.'.getid3_lib::ImageTypesLookup($imagechunkcheck[2]);
					if (ecrire_fichier(_NOM_TEMPORAIRES_ACCESSIBLES . $tmp_file, $ThisFileInfo['id3v2']['APIC'][$cle]['data'])) {
						$id3['cover'.$cle] = _NOM_TEMPORAIRES_ACCESSIBLES . $tmp_file;
					}
				}
			}
		}
		if(isset($ThisFileInfo['comments_html'])){
			foreach($ThisFileInfo['comments_html'] as $cle=>$val){
				$id3[$cle] = $val['0'];
			}
		}
		$id3['format'] = $ThisFileInfo['audio']['dataformat'];
		$id3['lossless'] = $ThisFileInfo['audio']['lossless'];
		$id3['sample_rate'] = $ThisFileInfo['audio']['sample_rate'] ;
		$id3['bits'] = $ThisFileInfo['audio']['bits_per_sample'];
		$id3['track'] = $ThisFileInfo['tags']['id3v2']['track']['0'] ;
		$id3['codec'] = ($ThisFileInfo['audio']['encoder']) ? $ThisFileInfo['audio']['encoder'] : $ThisFileInfo['tags']['id3v2']['encoded_by']['0'] ;
		$id3['totaltracks'] = $ThisFileInfo['tags']['id3v2']['totaltracks']['0'] ;
		$id3['bitrate'] = $ThisFileInfo['audio']['bitrate'];
		$id3['bitrate_mode'] = $ThisFileInfo['audio']['bitrate_mode'];
		$id3['duree_secondes'] = $ThisFileInfo['playtime_seconds'];
		$id3['duree'] = $ThisFileInfo['playtime_string'];
		$id3['channels'] = $ThisFileInfo['audio']['channels'];
		$id3['channel_mode'] = $ThisFileInfo['audio']['channelmode'];
		$id3['mime'] = $ThisFileInfo['mime_type'];
	}
	spip_log($ThisFileInfo);
	if(!$info){
		return $id3;
	}
	else{
		return $id3[$info];
	}
}
?>