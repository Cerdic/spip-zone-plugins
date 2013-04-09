<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores et vidéos directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2013 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Récupération des informations d'un document ou d'un fichier audio ou vidéo
 * 
 * Si on a un id_document (en premier argument) on enregistre en base dans cette fonction
 * Si on a seulement un chemin de fichier (en second argument), on retourne un tableau des metas
 *
 * @param int/bool $id_document 
 * 		Identifiant numérique duquel on doit récupérer les infos
 * @param string/bool $fichier
 * 		Chemin du fichier duquel on doit récupérer les infos
 * @param bool $logo
 * 		Doit on récupérer une vignette
 * @param bool $only_return
 * 		Ne fait t'on que retourner le résultat
 * @return array $infos
 * 		Les infos récupérées
 */

function inc_getid3_recuperer_infos($id_document=false,$fichier=null,$logo=false,$only_return=false){
	
	if((!intval($id_document) && !isset($fichier)))
		return false;
	
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	
	if(!isset($fichier)){
		include_spip('inc/documents');
		$document = sql_fetsel("*", "spip_documents","id_document=".intval($id_document));
		$fichier = get_spip_doc($document['fichier']);
		$extension = $document['extension'];
	}else
		$extension = strtolower(array_pop(explode('.',basename($fichier))));

	/**
	 * Récupération des metas du fichier
	 */
	$recuperer_id3 = charger_fonction('recuperer_id3','inc');
	$infos = $recuperer_id3($fichier);
	
	/**
	 * On enlève les infos vides
	 */
	if(strlen($document['titre']) > 0)
		unset($infos['titre']);

	if(strlen($document['descriptif']) > 0)
		unset($infos['descriptif']);

	foreach($infos as $key => $val){
		if(!$val)
			unset($infos[$key]);
	}
	
	/**
	 * Si les champs sont vides, on ne les enregistre pas
	 * Par contre s'ils sont présents dans le $_POST ou $_GET,
	 * on les utilise (fin de conversion où on récupère le titre et autres infos du document original)
	 */
	if(!function_exists('filtrer_entites'))
		include_spip('inc/filtres');
	foreach(array('titre','descriptif','credit') as $champ){
		if(isset($infos[$champ]))
			$infos[$champ] = filtrer_entites($infos[$champ]);
		if(!isset($infos[$champ]))
			$infos[$champ] = '';
		if(is_null($infos[$champ]) OR ($infos[$champ]=='')){
			if(_request($champ))
				$infos[$champ] = _request($champ);
			else
				unset($infos[$champ]);	
		}
	}
	
	/**
	 * Filesize tout seul est limité à 2Go
	 * cf http://php.net/manual/fr/function.filesize.php#refsect1-function.filesize-returnvalues
	 */
	if(($infos['taille'] = @intval(filesize($fichier))) == '2147483647')
		$infos['taille'] = sprintf("%u", filesize($fichier));
	
	/**
	 * Si on a gis et que les fonctions de récupération de metadonnés nous ont renvoyé :
	 * -* lat = la latitude;
	 * -* lon = la longitude;
	 * 
	 * Deux cas :
	 * -* Si on a un id_document numérique 
	 * -** On recherche si on a déjà un point lié au document et on le modifie
	 * -** Sinon on crée de nouvelles coordonnées
	 * -* Si on n'a pas d'id_document (cas des metadonnées récupérées par les fonctions metadatas/....php)
	 * -** On crée un point avec les coordonnées et on envoit dans le $_POST id_gis_meta 
	 * pour que le point soit lié dans le pipeline post_edition
	 */
	if(defined('_DIR_PLUGIN_GIS') && is_numeric($infos['latitude']) && is_numeric($infos['longitude'])){
		$zoom = lire_config('gis/zoom',4);
		$config = @unserialize($GLOBALS['meta']['gis']);
		$c = array(
			'titre' => $infos['titre'] ? $infos['titre'] : basename($fichier),
			'lat'=> $infos['latitude'],
			'lon' => $infos['longitude'],
			'zoom' => $zoom
		);

		if (defined('_DIR_PLUGIN_GISGEOM')) {
			$geojson = '{"type":"Point","coordinates":['.$infos['longitude'].','.$infos['latitude'].']}';
			set_request('geojson',$geojson);
		}
		
		include_spip('action/editer_gis');
		
		if(intval($id_document)){
			if($id_gis = sql_getfetsel("G.id_gis","spip_gis AS G LEFT  JOIN spip_gis_liens AS T ON T.id_gis=G.id_gis ","T.id_objet=" . intval($id_document) . " AND T.objet='document'")){
				/**
				 * Des coordonnées sont déjà définies pour ce document => on les update
				 */ 
				revisions_gis($id_gis,$c);
			}else{
				/**
				 * Aucune coordonnée n'est définie pour ce document  => on les crée
				 */ 
				$id_gis = insert_gis();
				revisions_gis($id_gis,$c);
				lier_gis($id_gis, 'document', $id_document);
			}
		}else{
			/**
			 * Aucune coordonnée n'est définie pour ce document  => on les crée
			 * On ajoute dans le $_POST id_gis_meta qui sera utilisable dans post_edition
			 */ 
			$id_gis = insert_gis();
			revisions_gis($id_gis,$c);
			set_request('id_gis_meta',$id_gis);
		}
	}

	/**
	 * On remplit les champs de base de SPIP avec ce dont on dispose
	 *
	 * -* titre
	 * -* descriptif
	 */
	if(isset($infos['title']))
		$infos['titre'] = preg_replace('/_/',' ',utf8_encode($infos['title']));

	else if(!isset($infos['title'])){
		$titre = utf8_encode(strtolower(array_shift(explode('.',basename($fichier)))));
		$infos['titre'] = preg_replace('/_/',' ',$titre);
	}

	if(!isset($infos['descriptif'])){
		/**
		 * Ne pas prendre les comments foireux d'itunes
		 */
		if(isset($infos['comments']) && !preg_match('/0000[a-b|0-9]{4}/',$infos['comments']))
			$infos['descriptif'] = utf8_encode($infos['comments']);
		else{
			if(isset($infos['artist']))
				$infos['descriptif'] .= utf8_encode($infos['artist'])."\n";
			if(isset($infos['album']))
				$infos['descriptif'] .= utf8_encode($infos['album'])."\n";
			if(isset($infos['year']))
				$infos['descriptif'] .= utf8_encode($infos['year'])."\n";
			if(isset($infos['genre']))
				$infos['descriptif'] .= utf8_encode($infos['genre'])."\n";
			if(isset($infos['track_number']))
				$infos['descriptif'] .= utf8_encode($infos['track_number'])."\n";
			if(isset($infos['comment']) && !preg_match('/0000[a-b|0-9]{4}/',$infos['comment']))
				$infos['descriptif'] .= "\n".utf8_encode($infos['comment'])."\n";
		}
	}

	/**
	 * Les covers potentielles
	 * On ne tente de récupération de vignettes que lorsque l'on n'a pas de vignettes
	 * associée au document
	 * On ne met en vignette de document que la première que l'on trouve
	 */
	$covers = array();
	foreach($infos as $key=>$val){
		if(preg_match('/cover/',$key))
			$covers[] = $val;
	}
	
	$infos['credits'] = $infos['copyright_message']?$infos['copyright_message']:$infos['copyright'];
	
	if(!$infos['audiobitrate'] && !$infos['hasvideo'])
		$infos['audiobitrate'] = intval($infos['bitrate']);
	
	if(isset($infos['encoded_by']))
		$infos['encodeur'] = $infos['encoded_by'];
	
	if((isset($infos['date']) OR isset($infos['original_release_time']) OR isset($infos['encoded_time']))){
		if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$infos['date']))
			$infos['date'] = $infos['date'];
		else if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$infos['original_release_time']))
			$infos['date'] = $infos['original_release_time'];
		else if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$infos['encoded_time']))
			$infos['date'] = $infos['encoded_time'];
		
		if(isset($valeurs['date']) && (strlen($valeurs['date'])=='10'))
			$infos['date'] = $infos['date'].' 00:00:00';
	}

	/**
	 * Si on a du contenu dans les messages de copyright, 
	 * on essaie de trouver la licence, si on a le plugin Licence
	 * 
	 * Pour l'instant uniquement valable sur les CC
	 */
	if(defined('_DIR_PLUGIN_LICENCE') && ((strlen($infos['copyright_message']) > 0) OR strlen($infos['copyright']) > 0)){
		include_spip('licence_fonctions');
		if(function_exists('licence_recuperer_texte')){
			foreach(array($infos['copyright_message'],$infos['copyright']) as $contenu){
				$infos['id_licence'] = licence_recuperer_texte($contenu);
				if(intval($infos['id_licence']))
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
			  AND $id_vignette = reset($ajoute))
			  	$infos['id_vignette'] = $id_vignette;
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
			  	$infos['id_vignette'] = $id_vignette;
			}
		}
	}
	
	/**
	 * Si on a $only_return à true, on souhaite juste retourner les metas, sinon on les enregistre en base
	 * Utile pour metadatas/video par exemple
	 */
	if(!$only_return && (intval($id_document) && (count($infos) > 0))){
		foreach($infos as $champ=>$val){
			if($document[$champ] == $val)
				unset($infos[$champ]);
		}
		if(count($infos) > 0){
			include_spip('action/editer_document');
			document_modifier($id_document, $infos);
		}
		return true;
	}

	return $infos;
}

?>