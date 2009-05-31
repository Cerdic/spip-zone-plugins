<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function joindre_determiner_mode($mode,$id_document,$objet){
	if ($mode=='auto'){
		if (intval($id_document))
			$mode = sql_getfetsel('mode','spip_documents','id_document='.intval($id_document));
		if (!in_array($mode,array('choix','document','image'))){
			$mode='choix';
			if ($objet AND $GLOBALS['meta']["documents_$objet"]=='non')
				$mode = 'image';
		}
	}
	return $mode;
}


function formulaires_joindre_document_charger_dist($id_document='new',$id_objet=0,$objet='',$mode = 'auto'){
	$valeurs = array();
	$mode = joindre_determiner_mode($mode,$id_document,$objet);
	
	$valeurs['id'] = $id_document;
	$valeurs['mode'] = $mode;
	
	$valeurs['url'] = 'http://';
	$valeurs['fichier'] = '';
	
	$valeurs['_options_upload_ftp'] = '';
	$valeurs['_dir_upload_ftp'] = '';
	
	$valeurs['joindre_upload']=''; 
	$valeurs['joindre_distant']=''; 
	$valeurs['joindre_ftp']='';
	
	# regarder si un choix d'upload FTP est possible
	if (
	 test_espace_prive() # ??
	 AND ($mode == 'document' OR $mode == 'choix') # si c'est pour un document
	 //AND !$vignette_de_doc		# pas pour une vignette (NB: la ligne precedente suffit, mais si on la supprime il faut conserver ce test-ci)
	 AND $GLOBALS['flag_upload']
	 ) {
		include_spip('inc/actions');
		if ($dir = determine_upload('documents')) {
			// quels sont les docs accessibles en ftp ?
			$valeurs['_options_upload_ftp'] = joindre_options_upload_ftp($dir, $mode);
			// s'il n'y en a pas, on affiche un message d'aide
			// en mode document, mais pas en mode image
			if ($valeurs['_options_upload_ftp'] OR ($mode == 'document' OR $mode=='choix'))
				$valeurs['_dir_upload_ftp'] = "<b>".joli_repertoire($dir)."</b>";
		}
	}
	
	return $valeurs;
}


function formulaires_joindre_document_verifier_dist($id_document='new',$id_objet=0,$objet='',$mode = 'auto'){
	include_spip('inc/joindre_document');
	
	$erreurs = array();
	$files = joindre_trouver_fichier_envoye();
	if (is_string($files)){
		$erreurs['message_erreur'] = $files;
	}
	elseif(is_array($files)){
		// erreur si on a pas trouve de fichier
		if (!count($files))
			$erreurs['message_erreur'] = _T('gestdoc:erreur_aucun_fichier');
		
		else{
			// regarder si on a eu une erreur sur l'upload d'un fichier
			foreach($files as $file){
				if (isset($file['error'])
				  AND $test = joindre_upload_error($file['error'])){
				  	if (is_string($test))
				  		$erreurs['message_erreur'] = $test;
				  	else
							$erreurs['message_erreur'] = _T('gestdoc:erreur_aucun_fichier');
				}
			}
			
			// si ce n'est pas deja un post de zip confirme
			// regarder si il faut lister le contenu du zip et le presenter
			if (!count($erreurs)
			 AND !_request('joindre_zip') 
			 AND $contenu_zip = joindre_verifier_zip($files)){
				list($fichiers,$erreurs,$tmp_zip) = $contenu_zip;
				$erreurs['lister_contenu_archive'] = recuperer_fond("formulaires/inc-lister_archive_jointe",array('chemin_zip'=>$tmp_zip,'liste_fichiers_zip'=>$fichiers,'erreurs_fichier_zip'=>$erreurs));
			}
		}
	}
	
	if (count($erreurs) AND defined('_tmp_dir'))
		effacer_repertoire_temporaire(_tmp_dir);
	
	return $erreurs;
}


function formulaires_joindre_document_traiter_dist($id_document='new',$id_objet=0,$objet='',$mode = 'auto'){
	$ajouter_documents = charger_fonction('ajouter_documents', 'action');

	$mode = joindre_determiner_mode($mode,$id_document,$objet);
	include_spip('inc/joindre_document');
	$files = joindre_trouver_fichier_envoye();

	$nouveaux_doc = action_ajouter_documents_dist($id_document,$files,$objet,$id_objet,$mode);

	if (defined('_tmp_dir'))
		effacer_repertoire_temporaire(_tmp_dir);
	
	// checker les erreurs eventuelles
	$messages_erreur = array();
	$nb_docs = 0;
	foreach ($nouveaux_doc as $doc) {
		if (!is_numeric($doc))
			$messages_erreur[] = $doc;
		else 
			$nb_docs++;
	}
	
	$res = array('editable'=>true);
	if (count($messages_erreur))
		$res['message_erreur'] = implode('<br />',$messages_erreur);
	if ($nb_docs)
		$res['message_ok'] = $nb_docs==1? _T('gestdoc:document_installe_succes'):_T('gestdoc:nb_documents_installe_succes',array('nb'=>$nb_docs));
	
	// todo : 
	// generer les case docs si c'est necessaire
	// rediriger sinon
	return $res;
}



/**
 * Retourner le contenu du select HTML d'utilisation de fichiers envoyes
 *
 * @param string $dir
 * @param string $mode
 * @return string
 */
function joindre_options_upload_ftp($dir, $mode = 'document') {
	$fichiers = preg_files($dir);
	$exts = array();
	$dirs = array(); 
	$texte_upload = array();

	// en mode "charger une image", ne proposer que les inclus
	$inclus = ($mode == 'document' OR $mode =='choix')
		? ''
		: " AND inclus='image'";

	foreach ($fichiers as $f) {
		$f = preg_replace(",^$dir,",'',$f);
		if (preg_match(",\.([^.]+)$,", $f, $match)) {
			$ext = strtolower($match[1]);
			if (!isset($exts[$ext])) {
				include_spip('action/ajouter_documents');
				$ext = corriger_extension($ext);
				if (sql_fetsel('extension', 'spip_types_documents', $a = "extension='$ext'" . $inclus))
					$exts[$ext] = 'oui';
				else $exts[$ext] = 'non';
			}

			$k = 2*substr_count($f,'/');
			$n = strrpos($f, "/");
			if ($n === false)
			  $lefichier = $f;
			else {
			  $lefichier = substr($f, $n+1, strlen($f));
			  $ledossier = substr($f, 0, $n);
			  if (!in_array($ledossier, $dirs)) {
				$texte_upload[] = "\n<option value=\"$ledossier\">"
				. str_repeat("&nbsp;",$k) 
				._T('tout_dossier_upload', array('upload' => $ledossier))
				."</option>";
				$dirs[]= $ledossier;
			  }
			}

			if ($exts[$ext] == 'oui')
			  $texte_upload[] = "\n<option value=\"$f\">" .
			    str_repeat("&nbsp;",$k+2) .
			    $lefichier .
			    "</option>";
		}
	} 

	$texte = join('', $texte_upload);
	if (count($texte_upload)>1) {
		$texte = "\n<option value=\"/\" style='font-weight: bold;'>"
				._T('info_installer_tous_documents')
				."</option>" . $texte;
	}

	return $texte;
}


/**
 * Lister les fichiers contenus dans un zip
 *
 * @param unknown_type $files
 * @return unknown
 */
function joindre_liste_contenu_tailles_archive($files) {
	include_spip('inc/charsets'); # pour le nom de fichier

	$res = '';
	if (is_array($files))
		foreach ($files as $nom => $file) {
			$nom = translitteration($nom);
			$date = date_interface(date("Y-m-d H:i:s", $file['mtime']));
	
			$taille = taille_en_octets($file['size']);
			$res .= "<li title=\"".attribut_html($title)."\"><b>$nom</b> &ndash; $taille<br />&nbsp; $date</li>\n";
		}
	
	return $res;
}


function joindre_liste_erreurs_to_li($erreurs){
	$res = implode("</li><li>",$erreurs);
	if (strlen($res)) $res = "<li>$res</li>";
	return $res;
}

?>