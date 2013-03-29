<?php
/**
 * Plugin Emballe Medias / Wrap medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * b_b (http://http://www.weblog.eliaz.fr)
 *
 * © 2008/2012 - Distribue sous licence GNU/GPL
 *
 * Action d'upload depuis SWFUpload
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action appelée à chaque upload de fichier par SWFUpload
 * Elle retourne un echo de l'id_objet créé ou mis à jour
 */
function action_emballe_medias_upload_dist(){
	$erreur = $nouvel_objet = $invalider = false;
	
	/**
	 * Le flash ne passe pas les cookies et donc on pert notre session ...
	 * On la rétabli avec ce hack horrible ...
	 */
	if(!$GLOBALS['visiteur_session']){
		if($cookie_session = _request($GLOBALS['cookie_prefix'].'_session')){
			preg_match('/^([0-9]+)_/',$cookie_session,$resultats);
			$id_auteur = $resultats[1];
		}else
			$id_auteur = _request('id_auteur');
		$GLOBALS['visiteur_session'] = sql_fetsel('*','spip_auteurs','id_auteur='.intval($id_auteur));
	}

	$arg = _request('arg');
	
	if (!$GLOBALS['visiteur_session']['id_auteur']) {
		$erreur = true;
		$message = 'Pas de session visiteur';
	}
	else if(!$objet = _request('objet')){
		$erreur = true;
		$message = "Vous devez fournir un type d objet.";
	}else if ($files = ($_FILES ? $_FILES : $HTTP_POST_FILES)){
		$table_objet_sql = table_objet_sql($objet);
		$id_table_objet= id_table_objet($objet);
		$result = array();
		include_spip('inc/config');
		
		if(!is_array($files['Filedata'])){
			$erreur = true;
			$message = "Pas de fichier.";
		}else{
			$error=$files['Filedata']['error'];
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
				$erreur = true;
				$message = $message;
				spip_log("EM : Erreur upload $error -- $message","emballe_medias");
			}
		}
		if(!$erreur){
			include_spip('action/editer_objet');
			$institution = $modification = array();
			
			/**
			 * Récupération d'un titre de document via le nom de fichier
			 * au cas où les metas ne permettent pas d'en récupérer un
			 */
			$titre_sans_extension = explode('.',basename($files['Filedata']['name']));
			if(count($titre_sans_extension) > 1)
				array_pop($titre_sans_extension);
			$titre_doc = str_replace('_',' ',implode(' ',$titre_sans_extension));
			
			/**
			 * Si on n'a pas d'id_objet dans le post
			 * C'est un nouvel objet à créer
			 */
			if (!$id_objet = intval($arg)) {
				$nouvel_objet = true;
				
				// si l'option de config "chercher_article" est active
				if (lire_config('emballe_medias/fichiers/chercher_article'))
					$id_objet = sql_getfetsel("art.id_article","spip_articles AS art LEFT JOIN spip_auteurs_liens AS aut ON (art.id_article=aut.id_objet)","aut.objet='article' AND art.statut IN ('prop','prepa') AND aut.id_auteur = ".intval($id_auteur),"","art.maj");
				
				if(!intval($id_objet)){
					/**
					 * On recherche la rubrique dans laquelle on va préenregistrer notre objet
					 * -* soit elle est passée directement par le script d'upload
					 * -* soit on récupère le secteur du premier spip_diogènes dont l'objet est emballe_medias et dans ce cas :
					 * -** Soit on autorise à publier à la racine de ce secteur et c'est cette rubrique (cf config)
					 * -** Soit on prend la première rubrique de ce secteur
					 */
					$id_parent = _request('id_parent') ? _request('id_parent') : _request('id_rubrique');
					if(!$id_parent){
						$rub_diogene = sql_getfetsel('id_secteur','spip_diogenes','objet='.sql_quote('emballe_media'));
						if(lire_config('emballe_medias/fichiers/publier_dans_secteur','off') != 'on')
							$id_parent = sql_getfetsel('id_rubrique','spip_rubriques','id_parent='.intval($rub_diogene));
						if(!$id_parent)
							$id_parent = $rub_diogene;
					}
					/**
					 * Insertion du nouvel objet dans la rubrique idoine
					 */
					$id_objet = objet_inserer($objet,$id_parent);
					
					/**
					 * Si le statut par défaut n'est pas "prop", on institue l'objet
					 */
					if(lire_config('diogene/statuts/article_statut_defaut','prop') != 'prop'){
						$institution['date'] = date("Y-m-d H:i:s");
						$institution['statut'] = lire_config('diogene/statuts/article_statut_defaut');
						$institution['id_parent'] = $id_parent;
					}
				}
			}
			
			/**
			 * Correction d'un mime-type erroné
			 * Fonction dans le plugin swfupload
			 */
			if($files['Filedata']['type'] == 'application/octet-stream') {
				include_spip('inc/swfupload');
				$files = swfupload_verifier_mime($files);
			}
			
			$result['result'] = 'success';
			$mime = $files['Filedata']['type'];
	
			/**
			 * Ajout du document à l'objet en question
			 * Si on a action_document = remplacer et un id_document, on remplace le document
			 */
			if(($ancien_document = _request('id_document')) && ($action_document = _request('action_document')))
				$id_document = $ancien_document;
			$ajouter_doc = charger_fonction('ajouter_documents','action');
			$id_document = $ajouter_doc($id_document,$files, $objet, $id_objet, 'document');
			if(intval(reset($id_document))){
				$id_document = reset($id_document);
			}else{
				$erreur = true;
				$message = 'Erreur dans l insertion en base';
			}
			if(!$erreur){
				/**
				 * Récupération des infos du document (après récupération des diverses metas dans les pipelines normalement) 
				 * pour modifier l'article en conséquence si besoin
				 */
				$infos_doc = sql_fetsel('*','spip_documents','id_document='.intval($id_document));
				$infos_objet = sql_fetsel('*',$table_objet_sql,"$id_table_objet=".intval($id_objet));
				
				/**
				 * On a em_type que sur les articles
				 */
				if($objet == 'article')
					$modification['em_type'] = $infos_doc['media'];
				
				/**
				 * On ne met de licence que sur les articles
				 */
				if(($objet == 'article') && defined('_DIR_PLUGIN_LICENCE') && ($infos_doc['id_licence'] != 0))
					$modification['id_licence'] = $infos_doc['id_licence'];
				
				/**
				 * Si l'objet n'a pas de titre ou de texte, on lui donne celui du document
				 */
				if(!$infos_objet['titre'])
					$modification['titre'] = $infos_doc['titre'] ? $infos_doc['titre'] : $titre_doc;
	
				if(!$infos_objet['texte'])
					$modification['texte'] = $infos_doc['descriptif'];
				
				objet_modifier($objet,$id_objet, $modification);
				
				/**
				 * Mise à jour de l'objet
				 * on lui change juste sa date de MAJ
				 */
				$institution['date'] = date("Y-m-d H:i:s");
				objet_instituer($objet,$id_objet, $institution);
				
				// Traitements spécifiques après l'upload
				pipeline('em_post_upload_medias',
					array(
						'args' => array(
							'id_document' => $id_document,
							'mime' => $mime,
							'objet' => $objet,
							'id_objet' => $id_objet,
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
			}
		}
	}else{
		$erreur = true;
		$message = "Rien dans le post.";
	}
	
	if($erreur){
		$info = array('error'=>array('code'=>101,'message'=> $message));
		header('HTTP/1.1 500 '.$message);
		header('Content-Type: application/json');
		die(json_encode($info));
	}
	die('{"jsonrpc" : "2.0", "message" : {"id_objet" : "'.$id_objet.'", "position_auto" : "'._request('position_auto').'"}}');
}

?>