<?php
	function inc_getid3_recuperer_infos($id_document){
		if(!intval($id_document)){
			return;
		}
		include_spip('inc/documents');
		$document = sql_fetsel("docs.id_document,docs.titre,docs.extension,docs.fichier,docs.taille,docs.mode", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
		$chemin = $document['fichier'];
		$movie_chemin = get_spip_doc($chemin);
		spip_log("on travail sur $movie","getid3");	

		include_spip('getid3/getid3');
		$getID3 = new getID3;
	
		// Scan file - should parse correctly if file is not corrupted
		$ThisFileInfo = $getID3->analyze($movie_chemin);
		getid3_lib::CopyTagsToComments($ThisFileInfo);
	
		if(sizeof($ThisFileInfo)>0){
			$id3['titre'] = ($ThisFileInfo['tags']['id3v2']['title']['0']) ? $ThisFileInfo['tags']['id3v2']['title']['0'] : $ThisFileInfo['id3v2']['comments']['title']['0'] ;
			$id3['artiste'] = ($ThisFileInfo['tags']['id3v2']['artist']['0']) ? $ThisFileInfo['tags']['id3v2']['artist']['0'] : $ThisFileInfo['id3v2']['comments']['artist']['0'] ;
			$id3['album']  = ($ThisFileInfo['tags']['id3v2']['album']['0']) ? $ThisFileInfo['tags']['id3v2']['album']['0'] : $ThisFileInfo['id3v2']['comments']['album']['0'] ;
			$id3['genre'] = ($ThisFileInfo['tags']['id3v2']['genre']['0']) ? $ThisFileInfo['tags']['id3v2']['genre']['0'] : $ThisFileInfo['id3v2']['comments']['genre']['0'] ;
			$id3['comment'] = ($ThisFileInfo['tags']['id3v2']['comment']['0']) ? $ThisFileInfo['tags']['id3v2']['comment']['0'] : $ThisFileInfo['id3v2']['comments']['comment']['0'] ;
			$id3['audiosamplerate'] = $ThisFileInfo['audio']['sample_rate'] ;
			$id3['track'] = $ThisFileInfo['tags']['id3v2']['track']['0'] ;
			$id3['encoded_by'] = $ThisFileInfo['tags']['id3v2']['encoded_by']['0'] ;
			$id3['totaltracks'] = $ThisFileInfo['tags']['id3v2']['totaltracks']['0'] ;
			$id3['tracknum'] = $ThisFileInfo['tags']['id3v2']['totaltracks']['0'] ;
			$id3['bitrate'] = $ThisFileInfo['audio']['bitrate'];
			$id3['bitrate_mode'] = $ThisFileInfo['audio']['bitrate_mode'];
			$id3['duree'] = $ThisFileInfo['playtime_string'];
		}
		if($document['titre']==''){
			$document['titre'] = ereg_replace('_',' ',$id3['titre']);
		}
		if($document['descriptif']==''){
			$document['descriptif'] = $id3['artiste'].' - '.$id3['album'];
		}
		sql_updateq('spip_documents',array('titre'=>$document['titre'],'descriptif'=>$document['descriptif'],'duree'=> $id3['duree'], 'bitrate' => $id3['bitrate'], 'bitrate_mode'=>$id3['bitrate_mode'],'audiosamplerate'=>$id3['audiosamplerate'], 'encodeur'=>$id3['encoded_by']),'id_document='.sql_quote($id_document));
		return;
	}
?>
