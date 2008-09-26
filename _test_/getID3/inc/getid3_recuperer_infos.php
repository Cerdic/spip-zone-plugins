<?php
	function inc_getid3_recuperer_infos($id_document){
		if(!intval($id_document)){
			return;
		}
		include_spip('inc/documents');
		$document = sql_fetsel("docs.id_document,docs.titre,docs.extension,docs.fichier,docs.taille,docs.mode", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
		$son_chemin = get_spip_doc($document['fichier']);
		spip_log("on travail sur $son_chemin","getid3");
		$recuperer_id3 = charger_fonction('recuperer_id3','inc');
		$id3 = $recuperer_id3($son_chemin);
		if($document['titre']==''){
			$document['titre'] = ereg_replace('_',' ',utf8_encode($id3['titre']));
		}
		if($document['titre'] == ''){
			$titre = substr(basename($document['fichier']), 0, -4);
			$document['titre'] = ereg_replace('_',' ',$titre);
		}
		if($document['descriptif']==''){
			
			if($id3['comment']){
				$document['descriptif'] = utf8_encode($id3['comment']);
			}
			else{
				$document['descriptif'] = utf8_encode($id3['genre']);
			}
		}
		sql_updateq('spip_documents',array('titre'=>$document['titre'],'descriptif'=>$document['descriptif'],'duree'=> $id3['duree'], 'bitrate' => $id3['bitrate'], 'bitrate_mode'=>$id3['bitrate_mode'],'audiosamplerate'=>$id3['audiosamplerate'], 'encodeur'=>$id3['encoded_by']),'id_document='.sql_quote($id_document));
		return;
	}
?>
