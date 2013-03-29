<?php 
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * b_b (http://http://www.weblog.eliaz.fr)
 *
 * © 2008/2012 - Distribue sous licence GNU/GPL
 *
 * Formulaire de chargement par ftp
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;
 
function formulaires_em_charger_media_ftp_charger_dist($objet,$id_objet=0,$extensions=array(),$type=null,$max=null,$redirect='',$proposer_ftp=true){
	include_spip('inc/joindre');
	
	if(!is_array($extensions) OR (count($extensions) < 1))
		$extensions = null;
	
	$valeurs = array();
	$mode = 'document';
	$valeurs['_options_upload_ftp'] = $valeurs['_dir_upload_ftp'] = $valeurs['joindre_ftp'] = '';
	$valeurs['proposer_ftp'] = is_string($proposer_ftp) ? (preg_match('/^(false|non|no)$/i', $proposer_ftp) ? false : true) : $proposer_ftp;
	$valeurs['em_charger_supprimer'] = _request('em_charger_supprimer');
	if ($valeurs['proposer_ftp']
		AND ($mode == 'document' OR $mode == 'choix') # si c'est pour un document
		AND $GLOBALS['flag_upload']) {
		include_spip('inc/actions');
		if ($dir = determine_upload('documents')) {
			// quels sont les docs accessibles en ftp ?
			$valeurs['_options_upload_ftp'] = joindre_options_upload_ftp($dir, $mode, $extensions,$max);
			// s'il n'y en a pas, on affiche un message d'aide
			// en mode document, mais pas en mode image
			if ($valeurs['_options_upload_ftp'] OR ($mode == 'document' OR $mode=='choix'))
				$valeurs['_dir_upload_ftp'] = "<b>".joli_repertoire($dir)."</b>";
		}
	}
	// On ne propose le FTP que si on a des choses a afficher
	$valeurs['proposer_ftp'] = ($valeurs['_options_upload_ftp'] && $valeurs['_dir_upload_ftp']);
	if ($objet AND $id_objet){
		$valeurs['id_objet'] = $id_objet;
		$valeurs['objet'] = $objet;
		$valeurs['refdoc_joindre'] = '';
		if ($valeurs['editable']){
			include_spip('inc/autoriser');
			$valeurs['editable'] = autoriser('modifier',$objet,$id_objet)?' ':'';
		}
	}
	$valeurs['_cheminftp'] = _request('cheminftp');
	if($valeurs['proposer_ftp']){
		$files = joindre_trouver_fichier_envoye();
		return $valeurs;
	}else
		return false;
}

function formulaires_em_charger_media_ftp_verifier_dist($objet,$id_objet=0,$extensions=array(),$type=null,$max=null,$redirect='',$proposer_ftp=true){
	$erreurs = array();
	$files = joindre_trouver_fichier_envoye();
	if (is_string($files))
		$erreurs['message_erreur'] = $files;
	else if(is_array($files)){
		// erreur si on a pas trouve de fichier
		if (!count($files))
			$erreurs['message_erreur'] = _T('emballe_medias:erreur_aucun_fichier');
	}

	/**
	 * On regarde si un fichier à peu près identique est envoyé
	 * Si oui :
	 * - On envoit un message d'erreur
	 * - On propose de forcer son upload
	 */
	if(!_request('em_charger_forcer') && (count($erreurs) == 0)){
		foreach($files as $file){
			include_spip('action/ajouter_documents');
			preg_match(",^(.*)\.([^.]+)$,", $file['name'], $match);
			@list(,$titre,$ext) = $match;
			$nom_envoye = str_replace('.','-',$titre).'.'.$ext;
			$nom_envoye =  preg_replace(',\.\.+,', '.', $nom_envoye); // pas de .. dans le nom du doc
			$nom_envoye = preg_replace("/[^._=-\w\d]+/", "_", 
			translitteration(preg_replace("/\.([^.]+)$/", "", 
						      preg_replace("/<[^>]*>/", '', basename($nom_envoye)))));
			$ext = corriger_extension(strtolower($ext));
			$nom_envoye = $ext.'/'.$nom_envoye.'.'.$ext;
			$document = sql_fetsel('*','spip_documents','fichier = '.sql_quote($nom_envoye));
			if(intval($document['id_document']) && (filesize($file['tmp_name']) == $document['taille'])){
				$titre = $document['titre'] ? $document['titre'] : basename($document['fichier']);
				$erreurs['message_erreur'] = _T('emballe_medias:erreur_document_existant',array('nom'=>$titre));
				$erreurs['erreur_em_charger_forcer'] = 'oui';
				break;
			}
		}
	}
	
	return $erreurs;
}

function formulaires_em_charger_media_ftp_traiter_dist($objet,$id_objet=0,$extensions=array(),$type=null,$max=null,$redirect='',$proposer_ftp=true){
	include_spip('action/editer_article');
	$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		
	$res = array();
	$nouvel_article = false;
	$mode = 'document';
	$files = joindre_trouver_fichier_envoye();

	$titre_sans_extension = explode('.',basename($files[0]['name']));
	if(count($titre_sans_extension > 1))
		array_pop($titre_sans_extension);
	$titre_doc = str_replace('_',' ',implode(' ',$titre_sans_extension));
	
	if (!$id_objet AND $objet) {
		$id_objet = sql_getfetsel("A.id_article","spip_auteurs_liens AS L LEFT JOIN spip_articles AS A ON (L.objet='article' AND L.id_objet=A.id_article)","A.statut IN ('prop','prepa') AND A.em_type = ".sql_quote($type)." AND L.id_auteur = ".intval($id_auteur),"","A.maj");
		if(!intval($id_objet)){
			$nouvel_article = true;
			/**
			 * On recherche la rubrique dans laquelle on va préenregistrer notre article
			 * -* soit elle est passée directement par le script d'upload
			 * -* soit on récupère le secteur du premier spip_diogènes dont l'objet est emballe_medias
			 */
			$id_rubrique = _request('id_rubrique') ? _request('id_rubrique') : sql_getfetsel('id_secteur','spip_diogenes','objet='.sql_quote('emballe_media'));

			/**
			 * Insertion de l'article dans la rubrique idoine
			 */
			$id_objet = article_inserer($id_rubrique);

			/**
			 * Mise à jour de l'article en lui donnant :
			 * -* un titre temporaire qui correspond au nom du fichier
			 * -* un em_type
			 */
			$c = array(
				'titre' => $titre_doc,
				'em_type' => $type
			);

			article_modifier($id_objet, $c);

			if(lire_config('diogene/statuts/article_statut_defaut','prop') != 'prop'){
				$c = array(
						'date'=> date(),
						'statut' => lire_config('diogene/statuts/article_statut_defaut'),
						'id_parent' => $id_rubrique
					);
				article_instituer($id_objet, $c);
			}
		}
	}

	$messages_erreur = array();
	$nb_docs = 0;
	foreach($files as $file){
		$nouveaux_doc = $ajouter_documents('new',$files,$objet,$id_objet,$mode);
		if (!is_numeric(reset($nouveaux_doc)))
			$messages_erreur[] = $nouveaux_doc;
		else{
			if(_request('em_charger_supprimer')){
				if(!function_exists('supprimer_fichier'))
					include_spip('inc/flock');
				supprimer_fichier($file['tmp_name']);
			}
			if (!$ancre)
				$ancre = reset($nouveaux_doc);
			$nb_docs++;
		}
		/**
		 * Si le document a un titre et un descriptif, on les donne aussi à l'article
		 */
		if($nouvel_article){
			$infos_doc = sql_fetsel('titre,descriptif','spip_documents','id_document='.intval(reset($nouveaux_doc)));
			$infos_doc['titre'] = $infos_doc['titre'] ? $infos_doc['titre'] : $titre_doc;
			$infos_doc['texte'] = $infos_doc['descriptif'];
			unset($infos_doc['descriptif']);
			article_modifier($id_objet, $infos_doc);
		}
		pipeline('em_post_upload_medias',
			array(
				'args' => array(
					'id_document' => reset($nouveaux_doc),
					'objet' => $objet,
					'id_objet' => $id_objet,
					'id_auteur' => $GLOBALS['visiteur_session']['id_auteur'],
					'ancien_document' => $ancien_document,
					'action_document' => 'charger_ftp'
				)
			)
		);
	}

	if (count($messages_erreur))
		$res['message_erreur'] = implode('<br />',$messages_erreur);
	else
		$res['message_ok'] = _T('emballe_medias:analyze_document').'<br />'._T('emballe_medias:message_navigateur_redirection');

	$res['redirect'] = $redirect ? $redirect : parametre_url(self(),'hasard',rand());
	if ($ancre)
		$res['redirect'] .= "#doc$ancre";
	
	return $res;
}

/**
 * Retourner le contenu du select HTML d'utilisation de fichiers envoyes
 *
 * @param string $dir
 * @param string $mode
 * @return string
 */
function joindre_options_upload_ftp($dir, $mode = 'document',$extensions = null,$max=null) {
	include_spip('action/ajouter_documents');

	$exts = $dirs = $texte_upload = array();
	
	$fichiers = preg_files($dir);
	$file = _request('cheminftp');
	// en mode "charger une image", ne proposer que les inclus
	$inclus = ($mode == 'document' OR $mode =='choix')
		? ''
		: " AND inclus='image'";

	foreach ($fichiers as $f) {
		$full_file = $f;
		$f = preg_replace(",^$dir,",'',$f);
		if (preg_match(",\.([^.]+)$,", $f, $match)) {
			$ext = strtolower($match[1]);
			if (!isset($exts[$ext])) {						
				$ext = corriger_extension($ext);
				if(is_array($extensions)){
					if(in_array($ext,$extensions))
						$exts[$ext] = 'oui';
					else 
						$exts[$ext] = 'non';
				}
				else if (sql_fetsel('extension', 'spip_types_documents', $a = "extension='$ext'" . $inclus))
					$exts[$ext] = 'oui';
				else $exts[$ext] = 'non';
			}
			
			$k = substr_count($f,'/');
			$n = strrpos($f, "/");
			$lefichier = substr($f, $n+1, strlen($f));
			$ledossier = substr($f, 0, $n);
			if ($n === false)
			  $lefichier = $f;
			else if(!in_array($ledossier, $dirs) && is_array($newfiles = preg_files($dir.$ledossier))){
				foreach($newfiles as $newfile){
					$newfile = preg_replace(",^$dir,",'',$newfile);
					preg_match(",\.([^.]+)$,", $newfile, $match_newfile);
					$ext_newfile = strtolower($match_newfile[1]);
					$ext_newfile = corriger_extension($ext_newfile);
					if(is_array($extensions)){
						if(in_array($ext_newfile,$extensions)){
							$file_ok = 'oui';
							break;
						}
					}
					else if (sql_fetsel('extension', 'spip_types_documents', $a = "extension='$ext'" . $inclus)){
						$file_ok = 'oui';
						break;
					}
					else
						$file_ok = 'non';
				}
				if($file_ok == 'oui'){
					if($max > 1){
						$texte_upload[] = "\n<option value=\"$ledossier\">"
						. str_repeat("&nbsp;",$k) 
						._T('tout_dossier_upload', array('upload' => $ledossier))
						."</option>";
					}else{
						$texte_upload[] = "\n<option>"
						. str_repeat("&rsaquo;&nbsp;",$k) 
						.$ledossier
						."</option>";
					}
					$dirs[]= $ledossier;
				}
				unset($file_ok);
			}

			if ($exts[$ext] == 'oui'){
				$selected="";
				if($f == $file)
					$selected = " selected='selected'";
				$texte_upload[] = "\n<option value=\"$f\"".$selected.">" .
			    	str_repeat("&nbsp;",$k+2) .
			    	$lefichier .
			    "</option>";
			}
		}
	}
	
	$texte = join('', $texte_upload);
	
	if ((!$max OR ($max>=count($texte_upload))) && (count($texte_upload)>1)) {
		$texte = "\n<option value=\"/\" style='font-weight: bold;'>"
			._T('info_installer_tous_documents')
			."</option>" . $texte;
	}

	return $texte;
}

function joindre_trouver_fichier_envoye(){
	if (_request('joindre_ftp') OR _request('em_charger_forcer')){
		$path = _request('cheminftp');
		
		if (!$path || strstr($path, '..')) 
			return _T('emballe_medias:erreur_aucun_fichier');
		
		include_spip('inc/documents');
		$upload = determine_upload();
		
		if ($path != '/' AND $path != './') 
			$upload .= $path;
	
		if (!is_dir($upload))
		  // seul un fichier est demande
		  return array(
		  	array (
		  		'name' => basename($upload),
					'tmp_name' => $upload
				)
			);
		else {
		  // on upload tout un repertoire
		  $files = array();
		  foreach (preg_files($upload) as $fichier) {
				$files[]= array (
					'name' => basename($fichier),
					'tmp_name' => $fichier
				);
		  }
		  return $files;
		}
	}
}
?>