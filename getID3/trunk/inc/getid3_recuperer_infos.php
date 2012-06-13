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
 * Enregistrement en base le contenu des données des tags et des données audio
 *
 * @param int $id_document
 */

function inc_getid3_recuperer_infos($id_document){
	if(!intval($id_document)){
		return;
	}
	
	include_spip('action/editer_document');
	include_spip('inc/documents');
	include_spip('inc/filtres');
	$document = sql_fetsel("*", "spip_documents","id_document=".intval($id_document));
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

	/**
	 * Les valeurs que l'on mettra en base à la fin
	 */
	$valeurs = array(
			'titre'=>filtrer_entites($document['titre']),
			'descriptif'=>filtrer_entites($document['descriptif']),
			'duree'=> $id3['duree_secondes'],
			'bitrate' => $id3['bitrate'],
			'bitrate_mode'=>$id3['bitrate_mode'],
			'audiosamplerate'=>$id3['audiosamplerate'],
			'encodeur'=>$id3['codec'],
			'bits'=>$id3['bits'],
			'canaux' => $id3['channels']
		);

	if(count($covers) > 0){
		$id_vignette = sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id_document));

		if(($id_vignette == 0)){
			include_spip('inc/joindre_document');
			$ajouter_documents = charger_fonction('ajouter_documents', 'action');

			list($extension,$arg) = fixer_extension_document($covers[0]);
			$cover_ajout = array(array('tmp_name'=>$covers[0],'name'=> basename($covers[0])));
			$ajoute = $ajouter_documents($id_vignette,$cover_ajout,'',0,'vignette');

			if (is_numeric(reset($ajoute))
			  AND $id_vignette = reset($ajoute)){
			  	$valeurs['id_vignette'] = $id_vignette;
			}
		}
	}else if(strlen($cover_defaut = lire_config('getid3/cover_defaut','')) > 1){
		/**
		 * Si on n'a pas de cover,
		 * On ajoute la cover par défaut si elle existe comme vignette de document et
		 * comme cover du fichier
		 */
		$id_vignette = sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id_document));
	
		if(($id_vignette == 0)){
			include_spip('inc/joindre_document');
			include_spip('inc/distant');
			$cover_defaut = find_in_path(copie_locale($cover_defaut));
			$ajouter_documents = charger_fonction('ajouter_documents', 'action');

			list($extension,$arg) = fixer_extension_document($cover_defaut);
			$cover_defaut = array(array('tmp_name'=>$cover_defaut,'name'=> basename($cover_defaut)));
			$ajoute = $ajouter_documents($id_vignette,$cover_defaut,'',0,'vignette');

			if (is_numeric(reset($ajoute))
			  AND $id_vignette = reset($ajoute)){
			  	$valeurs['id_vignette'] = $id_vignette;
			}
		}
	}
	
	document_modifier($id_document,$valeurs);

	return $id3;
}
?>
