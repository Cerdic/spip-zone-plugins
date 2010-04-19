<?php

/**
 * Enregistre en base le contenu des données des tags et des données audio
 *
 * @param int $id_document
 */

function inc_getid3_recuperer_infos($id_document){
	if(!intval($id_document)){
		return;
	}
	include_spip('inc/documents');
	$document = sql_fetsel("docs.id_document,docs.titre,docs.extension,docs.fichier,docs.taille,docs.mode", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".intval($id_document));
	$son_chemin = get_spip_doc($document['fichier']);
	$recuperer_id3 = charger_fonction('recuperer_id3','inc');
	$id3 = $recuperer_id3($son_chemin);

	if($document['titre'] == ''){
		$document['titre'] = ereg_replace('_',' ',utf8_encode($id3['titre']));
	}
	if($document['titre'] == ''){
		$titre = strtolower(array_shift(explode('.',basename($document['fichier']))));
		$titre = utf8_encode($titre);
		$document['titre'] = ereg_replace('_',' ',$titre);
	}

	if($document['descriptif'] == ''){
		if($id3['comments']){
			$document['descriptif'] = utf8_encode($id3['comments']);
		}
		else{
			$document['descriptif'] = utf8_encode($id3['genre']);
		}
	}

	sql_updateq('spip_documents',
		array(
			'titre'=>$document['titre'],
			'descriptif'=>$document['descriptif'],
			'duree'=> $id3['duree'],
			'bitrate' => $id3['bitrate'],
			'bitrate_mode'=>$id3['bitrate_mode'],
			'audiosamplerate'=>$id3['audiosamplerate'],
			'encodeur'=>$id3['codec'],
			'bits'=>$id3['bits'],
			'canaux' => $id3['channels']
		),
		'id_document='.intval($id_document));

	return $id3;
}
?>
