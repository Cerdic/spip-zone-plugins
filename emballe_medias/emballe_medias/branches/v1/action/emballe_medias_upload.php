<?php
/**
 * Plugin Emballe Medias / Wrap medias
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2008/2011 - Distribue sous licence GNU/GPL
 *
 * Action d'upload depuis SWFUpload
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action appelée à chaque upload de fichier par SWFUpload
 * Elle retourne un echo de l'id_article créé ou mis à jour
 */
function action_emballe_medias_upload_dist(){

	/**
	 * Le flash ne passe pas les cookies et donc on pert notre session ...
	 * On la rétabli avec ce hack horrible ...
	 */
	if(!$GLOBALS['visiteur_session']){
		if($cookie_session = _request($GLOBALS['cookie_prefix'].'_session')){
			preg_match('/^([0-9]+)_/',$cookie_session,$resultats);
			$id_auteur = $resultats[1];
		}else{
			$id_auteur = _request('id_auteur');
		}
		$GLOBALS['visiteur_session'] = sql_fetsel('*','spip_auteurs','id_auteur='.intval($id_auteur));
	}

	$arg = _request('arg');

	if (!$GLOBALS['visiteur_session']['id_auteur']) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Aucun article."}, "id_article" : "id"}');
	}

	$type = _request('em_type') ? _request('em_type') : 'normal';

	$result = array();
	if ($files = ($_FILES ? $_FILES : $HTTP_POST_FILES)){
		include_spip('action/editer_article');
		include_spip('inc/modifier');
		if(!is_array($files['Filedata'])){
			header('Content-type: application/json');
			$info = array('error'=>array('code'=>101,'message'=> 'Pas de fichier.'));
        	die(json_encode($info));
		}
		$error = $files['Filedata']['error'];
		if(intval($error)){
			switch ($error) {
				case UPLOAD_ERR_INI_SIZE:
					$message =  'The uploaded file exceeds the upload_max_filesize directive in php.ini';
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
					break;
				case UPLOAD_ERR_PARTIAL:
					$message =  'The uploaded file was only partially uploaded';
					break;
				case UPLOAD_ERR_NO_FILE:
					$message = 'No file was uploaded';
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$message = 'Missing a temporary folder';
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$message = 'Failed to write file to disk';
					break;
				case UPLOAD_ERR_EXTENSION:
					$message = 'File upload stopped by extension';
					break;
				default:
		            $message = 'Unknown upload error';
					break; 
			}
			spip_log("EM : Erreur upload $error -- $message","emballe_medias");
			$info = array('error'=>array('code'=>101,'message'=> $message));
			header('Content-type: application/json');
        	die(json_encode($info));
		}

		$titre_sans_extension = explode('.',basename($files['Filedata']['name']));
		if(count($titre_sans_extension > 1))
			array_pop($titre_sans_extension);
		$titre_doc = str_replace('_',' ',implode(' ',$titre_sans_extension));

		if (!$id_article = intval($arg)) {
			$nouvel_article = true;
			
			// si l'option de config "chercher_article" est active
			if (lire_config('emballe_medias/fichiers/chercher_article'))
				$id_article = sql_getfetsel("art.id_article","spip_articles AS art LEFT JOIN spip_auteurs_articles AS aut ON (art.id_article=aut.id_article)","art.statut IN ('prop','prepa') AND art.em_type = ".sql_quote($type)." AND aut.id_auteur = ".intval($id_auteur),"","art.maj");
			
			if(!intval($id_article)){
				/**
				 * On recherche la rubrique dans laquelle on va préenregistrer notre article
				 * -* soit elle est passée directement par le script d'upload
				 * -* soit on récupère le secteur du premier spip_diogènes dont l'objet est emballe_medias et dans ce cas :
				 * -** Soit on autorise à publier à la racine de ce secteur et c'est cette rubrique (cf config)
				 * -** Soit on prend la première rubrique de ce secteur
				 */
				$id_rubrique = _request('id_rubrique');
				
				
				if(!$id_rubrique){
					$rub_diogene = sql_getfetsel('id_secteur','spip_diogenes','objet='.sql_quote('emballe_media'));
					if(lire_config('emballe_medias/fichiers/publier_dans_secteur','off') != 'on'){
						$id_rubrique = sql_getfetsel('id_rubrique','spip_rubriques','id_parent='.intval($rub_diogene));
					}
					if(!$id_rubrique){
						$id_rubrique = $rub_diogene;
					}
				}
				/**
				 * Insertion de l'article dans la rubrique idoine
				 */
				$id_article = insert_article($id_rubrique);

				/**
				 * Mise à jour de l'article en lui donnant :
				 * -* un titre temporaire qui correspond au nom du fichier
				 * -* un em_type
				 */
				$c = array(
					'titre' => $titre_doc,
					'em_type' => $type
				);
				revision_article($id_article, $c);

				if(lire_config('diogene/statuts/article_statut_defaut','prop') != 'prop'){
					$c = array(
							'date'=> date("Y-m-d H:i:s"),
							'statut' => lire_config('diogene/statuts/article_statut_defaut'),
							'id_parent' => $id_rubrique
						);
					instituer_article($id_article, $c);
				}
			}
		}else{
			$nouvel_article = false;
		}
		if(!$titre = sql_getfetsel('titre','spip_articles','id_article='.intval($id_article))){
			/**
			 * Mise à jour de l'article en lui donnant :
			 * -* un titre temporaire qui correspond au nom du fichier
			 * -* un em_type
			 */
			$c = array(
				'titre' => $titre_doc,
				'em_type' => $type
			);
			revision_article($id_article, $c);
		}
		/**
		 * Correction d'un mime-type erroné
		 * Fonction dans le plugin swfupload
		 */
		if($files['Filedata']['type'] == 'application/octet-stream') {
			include_spip('inc/swfupload');
			$files = swfupload_verifier_mime($files);
		}

		$mode = 'document';
		$invalider = false;
		$type_lien = 'article';
		$id_lien = $id_article;

		$result['result'] = 'success';

		$mime = $files['Filedata']['type'];

		/**
		 * Ajout du document à l'article en question
		 * Si on a action_document = remplacer et un id_document, on remplace le document
		 */
		if(($ancien_document = _request('id_document')) && ($action_document = _request('action_document')))
			$id_document = $ancien_document;
		$ajouter_doc = charger_fonction('ajouter_documents','inc');
		$id_document = $ajouter_doc($files['Filedata']['tmp_name'], $files['Filedata']['name'], $type_lien, $id_lien, $mode, $id_document, $documents_actifs, $titrer=false);
		
		/**
		 * Si le document a un titre et un descriptif, on les donne aussi à l'article
		 */
		if($nouvel_article){
			$infos_doc = sql_fetsel('titre,descriptif','spip_documents','id_document='.intval($id_document));
			$infos_doc['titre'] = $infos_doc['titre'] ? $infos_doc['titre'] : $titre_doc;
			$infos_doc['texte'] = $infos_doc['descriptif'];
			unset($infos_doc['descriptif']);
			revision_article($id_article, $infos_doc);
		}
		
		/**
		 * Mise à jour de l'article
		 * on lui change juste sa date de MAJ
		 */
		$c = array(
				'date'=> date("Y-m-d H:i:s"),
			);
		instituer_article($id_article, $c);
		
		// Traitements spécifiques après l'upload
		pipeline('em_post_upload_medias',
			array(
				'args' => array(
					'id_document' => $id_document,
					'mime' => $mime,
					'objet' => 'article',
					'id_objet' => $id_article,
					'id_auteur' => $id_auteur,
					'ancien_document' => $ancien_document,
					'action_document' => $action_document
				)
			)
		);

		$invalider = true;

		if ($invalider) {
			include_spip('inc/invalideur');
			suivre_invalideur("0",true);
		}
	}else{
		$info = array('error'=>array('code'=>101,'message'=> 'Rien dans le post.'));
		header('Content-type: application/json');
        echo json_encode($info);
	}
	spip_log('on retourne l id_article');
	die('{"jsonrpc" : "2.0", "message" : {"id_article" : "'.$id_article.'", "position_auto" : "'._request('position_auto').'"}}');
}

?>