<?php

function recuperer_id3($fichier,$info=null,$mime=null){
	// Copy remote file locally to scan with getID3()
	include_spip('getid3/getid3');
	$getID3 = new getID3;	
	
	// Scan file - should parse correctly if file is not corrupted
	$ThisFileInfo = $getID3->analyze($fichier);
	getid3_lib::CopyTagsToComments($ThisFileInfo);
	
	if(sizeof($ThisFileInfo)>0){
		$id3['titre'] = ($ThisFileInfo['tags']['id3v2']['title']['0']) ? $ThisFileInfo['tags']['id3v2']['title']['0'] : $ThisFileInfo['id3v2']['comments']['title']['0'] ;
		$id3['artiste'] = ($ThisFileInfo['tags']['id3v2']['artist']['0']) ? $ThisFileInfo['tags']['id3v2']['artist']['0'] : $ThisFileInfo['id3v2']['comments']['artist']['0'] ;
		$id3['album']  = ($ThisFileInfo['tags']['id3v2']['album']['0']) ? $ThisFileInfo['tags']['id3v2']['album']['0'] : $ThisFileInfo['id3v2']['comments']['album']['0'] ;
		$id3['genre'] = ($ThisFileInfo['tags']['id3v2']['genre']['0']) ? $ThisFileInfo['tags']['id3v2']['genre']['0'] : $ThisFileInfo['id3v2']['comments']['genre']['0'] ;
		$id3['comment'] = ($ThisFileInfo['tags']['id3v2']['comment']['0']) ? $ThisFileInfo['tags']['id3v2']['comment']['0'] : $ThisFileInfo['id3v2']['comments']['comment']['0'] ;
		$id3['sample_rate'] = $ThisFileInfo['audio']['sample_rate'] ;
		$id3['track'] = $ThisFileInfo['tags']['id3v2']['track']['0'] ;
		$id3['encoded_by'] = $ThisFileInfo['tags']['id3v2']['encoded_by']['0'] ;
		$id3['totaltracks'] = $ThisFileInfo['tags']['id3v2']['totaltracks']['0'] ;
		$id3['tracknum'] = $ThisFileInfo['tags']['id3v2']['totaltracks']['0'] ;
		$id3['bitrate'] = $ThisFileInfo['audio']['bitrate'];
		$id3['bitrate_mode'] = $ThisFileInfo['audio']['bitrate_mode'];
		$id3['duree_secondes'] = $ThisFileInfo['playtime_seconds'];
		$id3['duree'] = $ThisFileInfo['playtime_string'];
	}
	if(!$info){
		return $id3;
	}
	else{
		return $id3[$info];
	}
}

?>