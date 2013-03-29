<?php
/**
 * Plugin Emballe Medias / Wrap medias
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2008/2011 - Distribue sous licence GNU/GPL
 *
 * Action d'upload depuis plupload
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action appelée à chaque upload de fichier par SWFUpload
 * Elle retourne un echo de l'id_article créé ou mis à jour
 */
function action_emballe_medias_upload_chunks_dist(){
	// HTTP headers for no cache etc
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
		
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

	if (!$GLOBALS['visiteur_session']['id_auteur']) {
		die('Vous n etes pas identifié');
	}
	
	$chunk = isset($_POST["chunk"]) ? intval($_POST["chunk"]) : 0;
	$chunks = isset($_POST["chunks"]) ? intval($_POST["chunks"]) : 0;
	$filename = isset($_POST["name"]) ? $_POST["name"] : '';
	// Clean the fileName for security reasons
	$fileName = preg_replace('/[^\w\._]+/', '', $filename);
	$file = _DIR_TMP.$filename;
	
	if(isset($_POST['delete_tmp'])){
		if(file_exists($file)){
			supprimer_fichier($file);
			die('Fichier temporaire supprimé');
		}else{
			die('Fichier temporaire n existait pas (encore)');
		}
	}

	// Look for the content type header
	if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
		$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
	
	if (isset($_SERVER["CONTENT_TYPE"]))
		$contentType = $_SERVER["CONTENT_TYPE"];

	// Make sure the fileName is unique but only if chunking is disabled
	if ($chunks < 2 && file_exists($file)) {
		$ext = strrpos($filename, '.');
		$fileName_a = substr($filename, 0, $ext);
		$fileName_b = substr($filename, $ext);
	
		$count = 1;
		while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
			$count++;
	
		$filename = $fileName_a . '_' . $count . $fileName_b;
	}
	
	// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
	if (strpos($contentType, "multipart") !== false) {
		if (isset($_FILES['Filedata']['tmp_name']) && is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
			// Open temp file
			$out = fopen($file, $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen($_FILES['Filedata']['tmp_name'], "rb");
	
				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id_article" : "id"}');
				fclose($in);
				fclose($out);
				if(($chunk != ($chunks - 1)) && file_exists($_FILES['Filedata']['tmp_name']))
					supprimer_fichier($_FILES['Filedata']['tmp_name']);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id_article" : "id"}');
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id_article" : "id"}');
	} else {
		// Open temp file
		$out = fopen($file, $chunk == 0 ? "wb" : "ab");
		if ($out) {
			// Read binary input stream and append it to temp file
			$in = fopen("php://input", "rb");
	
			if ($in) {
				while ($buff = fread($in, 4096))
					fwrite($out, $buff);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id_article" : "id"}');
	
			fclose($in);
			fclose($out);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id_article" : "id"}');
	}
	if(($chunk == ($chunks - 1)) OR ($chunks == 0)){
		$arg = _request('arg');
		$type = _request('em_type') ? _request('em_type') : 'normal';
		
		include_spip('action/editer_article');
		include_spip('inc/modifier');

		if (isset($_FILES['Filedata']['name'])){
			$filename = $_FILES['Filedata']['name'];
		}
		$titre_sans_extension = explode('.',basename($filename));
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
		if(($_FILES['Filedata']['type'] == 'application/octet-stream') && file_exists($_FILES['Filedata']['tmp_name'])) {
			include_spip('inc/swfupload');
			$_FILES = swfupload_verifier_mime($_FILES);
		}

		$mode = 'document';
		$invalider = false;
		$type_lien = 'article';
		$id_lien = $id_article;

		$mime = $_FILES['Filedata']['type'];
		
		/**
		 * Ajout du document à l'article en question
		 * Si on a action_document = remplacer et un id_document, on remplace le document
		 */
		if(($ancien_document = _request('id_document')) && ($action_document = _request('action_document')))
			$id_document = $ancien_document;
		$ajouter_doc = charger_fonction('ajouter_documents','inc');
		$id_document = $ajouter_doc($file, $filename, $type_lien, $id_lien, $mode, $id_document, &$documents_actifs, $titrer=false);
		
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
		
		/**
		 * On supprimer les fichiers temporaires
		 */
		if(file_exists($file))
			supprimer_fichier($file);
		if(file_exists($_FILES['Filedata']['tmp_name']))
			supprimer_fichier($_FILES['Filedata']['tmp_name']);
		die('{"jsonrpc" : "2.0", "result" : "OK", "id_article" : "'.$id_article.'"}');
	}else{
		// Return JSON-RPC response telling we are not complete
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "File not complete."}, "id_article" : "id"}');
	}
}

?>