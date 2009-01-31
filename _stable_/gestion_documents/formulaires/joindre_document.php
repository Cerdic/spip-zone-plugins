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


//
// Retourner le contenu du select HTML d'utilisation de fichiers envoyes
//
function options_upload_ftp($dir, $mode = 'document') {
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
				include_spip('inc/ajouter_documents');
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


function formulaires_joindre_document_charger_dist($id_document='new',$id_objet=0,$objet='',$mode = 'auto'){
	$valeurs = array();
	if ($mode=='auto'){
		$mode='choix';
		if ($objet AND $GLOBALS['meta']["documents_$objet"]=='non')
			$mode = 'image';
	}
	
	$valeurs['mode'] = $mode;
	
	$valeurs['url'] = 'http://';
	$valeurs['fichier'] = '';
	
	$valeurs['_options_upload_ftp'] = '';
	$valeurs['_dir_upload_ftp'] = '';
	
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
			$valeurs['_options_upload_ftp'] = options_upload_ftp($dir, $mode);
			// s'il n'y en a pas, on affiche un message d'aide
			// en mode document, mais pas en mode image
			if ($valeurs['_options_upload_ftp'] OR ($mode == 'document' OR $mode=='choix'))
				$valeurs['_dir_upload_ftp'] = "<b>".joli_repertoire($dir)."</b>";
		}
	}
	
	return $valeurs;
}


?>
