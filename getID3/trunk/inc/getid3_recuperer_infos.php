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

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Récupération des informations d'un document ou d'un fichier audio
 * Si on a un id_document (en premier argument) on enregistre en base dans cette fonction
 * Si on a seulement un chemin de fichier (en second argument), on retourne un tableau des metas
 *
 * @param int/null $id_document : id_document duquel on doit récupérer les infos
 * @param string/false $fichier : chemin du fichier duquel on doit récupérer les infos
 */

function inc_getid3_recuperer_infos($id_document=null,$fichier=false){
	if(!intval($id_document) && !$fichier){
		return false;
	}
	
	/**
	 * Récupérer le fichier si on part d'un id_document
	 */
	$document = array();
	
	if(intval($id_document)){
		include_spip('action/editer_document');
		include_spip('inc/documents');
		include_spip('inc/filtres');
		$document = sql_fetsel("*", "spip_documents","id_document=".intval($id_document));
		$son_chemin = get_spip_doc($document['fichier']);
		if(!file_exists($son_chemin))
			return false;
	}else{
		$son_chemin = $fichier;
	}
	
	/**
	 * Récupération des metas du fichier
	 */
	$recuperer_id3 = charger_fonction('recuperer_id3','inc');
	$id3 = $recuperer_id3($son_chemin);
	
	/**
	 * On remplit les champs de base de SPIP avec ce dont on dispose
	 *
	 * -* titre
	 * -* descriptif
	 */
	if((!isset($document['titre']) OR ($document['titre'] == '')) && isset($id3['title'])){
		$document['titre'] = preg_replace('/_/',' ',utf8_encode($id3['title']));
	}
	if($document['titre'] == ''){
		$titre = strtolower(array_shift(explode('.',basename($son_chemin))));
		$titre = utf8_encode($titre);
		$document['titre'] = preg_replace('/_/',' ',$titre);
	}

	if(!isset($document['descriptif']) OR ($document['descriptif'] == '')){
		/**
		 * Ne pas prendre les comments foireux d'itunes
		 */
		if(isset($id3['comments']) && !preg_match('/0000[a-b|0-9]{4}/',$id3['comments']))
			$document['descriptif'] = utf8_encode($id3['comments']);
		else{
			if(isset($id3['artist']))
				$document['descriptif'] .= utf8_encode($id3['artist'])."\n";
			if(isset($id3['album']))
				$document['descriptif'] .= utf8_encode($id3['album'])."\n";
			if(isset($id3['year']))
				$document['descriptif'] .= utf8_encode($id3['year'])."\n";
			if(isset($id3['genre']))
				$document['descriptif'] .= utf8_encode($id3['genre'])."\n";
			if(isset($id3['track_number']))
				$document['descriptif'] .= utf8_encode($id3['track_number'])."\n";
			if(isset($id3['comment']) && !preg_match('/0000[a-b|0-9]{4}/',$id3['comment']))
				$document['descriptif'] .= "\n".utf8_encode($id3['comment'])."\n";
		}
	}

	/**
	 * Les covers potentielles
	 * On ne tente de récupération de vignettes que lorsque l'on n'a pas de vignettes
	 * associée au document
	 * On ne met en vignette de document que la première que l'on trouve
	 */
	$covers = array();
	foreach($id3 as $key=>$val){
		if(preg_match('/cover/',$key))
			$covers[] = $val;
	}
	
	$credits = $id3['copyright_message']?$id3['copyright_message']:$id3['copyright'];
	
	if(!isset($document['credits']) OR ($document['credits'] == '') && ($credits != ''))
		$credits = filtrer_entites(utf8_encode($credits));
	
	/**
	 * Les valeurs que l'on mettra en base à la fin
	 */
	$valeurs = array(
			'titre'=>filtrer_entites($document['titre']),
			'descriptif'=>filtrer_entites($document['descriptif']),
			'duree'=> $id3['duree_secondes'],
			'bitrate' => intval($id3['bitrate']),
			'audiobitrate' => intval($id3['bitrate']),
			'audiobitratemode'=>$id3['bitrate_mode'],
			'audiochannels' => $id3['channels'],
			'audiosamplerate'=>$id3['audiosamplerate'],
			'encodeur'=>$id3['codec'],
			'bits'=>$id3['bits'],
			'credits'=>$credits
		);
	
	if((isset($id3['date']) OR isset($id3['original_release_time']) OR isset($id3['encoded_time']))){
		if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$id3['date']))
			$valeurs['date'] = $id3['date'];
		else if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$id3['original_release_time']))
			$valeurs['date'] = $id3['original_release_time'];
		else if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$id3['encoded_time']))
			$valeurs['date'] = $id3['encoded_time'];
		
		if(isset($valeurs['date']) && (strlen($valeurs['date'])=='10'))
			$valeurs['date'] = $valeurs['date'].' 00:00:00';
	}

	/**
	 * Si on a du contenu dans les messages de copyright, 
	 * on essaie de trouver la licence, si on a le plugin Licence
	 * 
	 * Pour l'instant uniquement valable sur les CC
	 */
	if(defined('_DIR_PLUGIN_LICENCE') && ((strlen($id3['copyright_message']) > 0) OR strlen($id3['copyright']) > 0)){
		include_spip('licence_fonctions');
		if(function_exists('licence_recuperer_texte')){
			foreach(array($id3['copyright_message'],$id3['copyright']) as $contenu){
				$valeurs['id_licence'] = licence_recuperer_texte($contenu);
				if(intval($valeurs['id_licence']))
					break;
			}
		}
	}
	
	if((count($covers) > 0)){
		if(intval($id_document))
			$id_vignette = sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id_document));
		else
			$id_vignette = 0;

		if(($id_vignette == 0)){
			include_spip('inc/joindre_document');
			$ajouter_documents = charger_fonction('ajouter_documents', 'action');

			list($extension,$arg) = fixer_extension_document($covers[0]);
			$cover_ajout = array(array('tmp_name'=>$covers[0],'name'=> basename($covers[0])));
			$ajoute = $ajouter_documents('new',$cover_ajout,'',0,'vignette');

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
		if(intval($id_document)) 
			$id_vignette = sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id_document));
		else
			$id_vignette = 0;
	
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
	
	if(intval($id_document))
		document_modifier($id_document,$valeurs);
	
	return $valeurs;
}
?>