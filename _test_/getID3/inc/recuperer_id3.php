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
		$id3['titre'] = ($ThisFileInfo['comments_html']['title']['0']) ? $ThisFileInfo['comments_html']['title']['0'] : $ThisFileInfo['id3v2']['comments']['title']['0'] ;
		$id3['artiste'] = ($ThisFileInfo['comments_html']['artist']['0']) ? $ThisFileInfo['comments_html']['artist']['0'] : $ThisFileInfo['id3v2']['comments']['artist']['0'] ;
		$id3['album']  = ($ThisFileInfo['comments_html']['album']['0']) ? $ThisFileInfo['comments_html']['album']['0'] : $ThisFileInfo['id3v2']['comments']['album']['0'] ;
		$id3['genre'] = ($ThisFileInfo['comments_html']['genre']['0']) ? $ThisFileInfo['comments_html']['genre']['0'] : $ThisFileInfo['id3v2']['comments']['genre']['0'] ;
		$id3['comment'] = ($ThisFileInfo['comments_html']['comment']) ? $ThisFileInfo['comments_html']['comment']['0'] : $ThisFileInfo['id3v2']['comment']['0'] ;
		$id3['year'] = ($ThisFileInfo['comments_html']['date']['0']) ? $ThisFileInfo['comments_html']['date']['0'] : $ThisFileInfo['id3v2']['comments']['year']['0'] ;
		$id3['sample_rate'] = $ThisFileInfo['audio']['sample_rate'] ;
		$id3['track'] = $ThisFileInfo['tags']['id3v2']['track']['0'] ;
		$id3['encoded_by'] = ($ThisFileInfo['audio']['encoder']) ? $ThisFileInfo['audio']['encoder'] : $ThisFileInfo['tags']['id3v2']['encoded_by']['0'] ;
		$id3['totaltracks'] = $ThisFileInfo['tags']['id3v2']['totaltracks']['0'] ;
		$id3['tracknum'] = $ThisFileInfo['tags']['id3v2']['totaltracks']['0'] ;
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