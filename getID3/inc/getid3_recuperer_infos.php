<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Enregistrement en base le contenu des données des tags et des données audio
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

	/**
	 * On remplit les champs de base de SPIP avec ce dont on dispose
	 *
	 * -* titre
	 * -* descriptif
	 */
	if($document['titre'] == ''){
		$document['titre'] = preg_replace('/_/',' ',utf8_encode($id3['title']));
	}
	if($document['titre'] == ''){
		$titre = strtolower(array_shift(explode('.',basename($document['fichier']))));
		$titre = utf8_encode($titre);
		$document['titre'] = preg_replace('/_/',' ',$titre);
	}

	if(($document['descriptif'] == '') && $id3['comments']){
		/**
		 * Ne pas prendre les comments foireux d'itunes
		 */
		if(!preg_match('/0000[a-b|0-9]{4}/',$id3['comments']))
			$document['descriptif'] = utf8_encode($id3['comments']);
	}

	/**
	 * Les covers potentielles
	 * On ne tente de récupération de vignettes que lorsque l'on n'a pas de vignettes
	 * associée au document
	 * On ne met en vignette de document que la première que l'on trouve
	 */
	foreach($id3 as $key=>$val){
		if(preg_match('/cover/',$key)){
			$covers[] = $val;
		}
	}

	if(count($covers) > 0){
		$id_vignette = sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id_document));

		if(($id_vignette == 0)){
			include_spip('inc/documents');
			$ajouter_documents = charger_fonction('ajouter_documents', 'inc');

			list($extension,$arg) = fixer_extension_document($covers[0]);
			$x = $ajouter_documents($covers[0], $covers[0],
					    $type, $id, 'vignette', $id_document, $actifs);
		}
		/**
		 * On supprime les covers temporaires
		 */
		foreach($covers as $fichier){
			supprimer_fichier($fichier);
		}
	}else if(strlen($cover_defaut = lire_config('getid3/cover_defaut','')) > 1){
		/**
		 * Si on n'a pas de cover,
		 * On ajoute la cover par défaut si elle existe comme vignette de document et
		 * comme cover du fichier
		 */
		$id_vignette = sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id_document));
	
		if(($id_vignette == 0)){
			include_spip('inc/documents');
			include_spip('inc/distant');
			$cover_defaut = find_in_path(copie_locale($cover_defaut));
			$ajouter_documents = charger_fonction('ajouter_documents', 'inc');

			list($extension,$arg) = fixer_extension_document($cover_defaut);
			$x = $ajouter_documents($cover_defaut, $cover_defaut,
					    $type, $id, 'vignette', $id_document, $actifs);
		}
	}
	sql_updateq('spip_documents',
		array(
			'titre'=>filtrer_entites($document['titre']),
			'descriptif'=>filtrer_entites($document['descriptif']),
			'duree'=> $id3['duree_secondes'],
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
