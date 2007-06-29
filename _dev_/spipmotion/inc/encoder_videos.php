<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions'); // *action_auteur et determine_upload
include_spip('base/abstract_sql');

//
// Construire un formulaire pour telecharger une video
//

function inc_encoder_videos_dist($script, $args, $id=0, $intitule='', $mode='', $type='', $ancre='', $id_document=0,$iframe_script='') {
	global $spip_lang_right;

	if (!_DIR_RESTREINT AND !$vignette_de_doc AND $GLOBALS['flag_upload']) {
		if($dir_ftp = determine_upload()) {
			// quels sont les docs accessibles en ftp ?
			$l = texte_encoder_manuel_videos($dir_ftp, '', $mode);
			// s'il n'y en a pas, on affiche un message d'aide
			// en mode document, mais pas en mode vignette
			if ($l OR ($mode == 'videos'))
				$dir_ftp = afficher_transferer_encoder_videos($l, $dir_ftp);
			else
				$dir_ftp = '';
		}
	}

  // Add the redirect url when uploading via iframe

  $iframe = "";
  if($iframe_script)
    $iframe = "<input type='hidden' name='iframe_redirect' value='".rawurlencode($iframe_script)."' />\n";

	if ($vignette_de_doc)
		$res = $milieu . $res;
	else
		$res = $res . $milieu;

	return generer_action_auteur('encoder_video',
		(intval($id) .'/' .intval($id_document) . "/$mode/$type"),
		generer_url_ecrire($script, $args, true),
		"$iframe$debut$res$dir_ftp$distant$fin",
		" method='post' style='border: 0px; margin: 0px;'");
}

//
// Retourner le code HTML d'utilisation de fichiers envoyes
//

function texte_encoder_manuel_videos($dir, $inclus = '', $mode = 'videos') {
	$fichiers = preg_files($dir);
	$exts = array();
	$dirs = array(); 
	$texte_upload = array();
	foreach ($fichiers as $f) {
		$f = preg_replace(",^$dir,",'',$f);
		if (ereg("\.([^.]+)$", $f, $match)) {
			$ext = strtolower($match[1]);
			if (!isset($exts[$ext])) {
				if ($ext == 'jpeg') $ext = 'jpg'; # cf. corriger_extension dans inc/getdocument
				if (spip_abstract_fetsel('extension', 'spip_types_documents', "extension='$ext'" . (!$inclus ? '':  " AND inclus='$inclus'")))
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
			// si l'extension est connu par spip et que c'est un format video que l'on peut encoder
			if ($exts[$ext] == 'oui'){
				if (($ext == 'mp4') OR ($ext == 'avi') OR ($ext == 'mpg'))
			  $texte_upload[] = "\n<option value=\"$f\">" .
			    str_repeat("&nbsp;",$k+2) .
			    $lefichier .
			    "</option>";
				
			}
		}
	} 

	$texte = join('', $texte_upload);

	return $texte;
}


// http://doc.spip.org/@afficher_transferer_upload
function afficher_transferer_encoder_videos($texte_upload, $dir)
{
	$doc = array('upload' => '<b>' . joli_repertoire($dir) . '</b>');
	if (!$texte_upload) {
		return "\n<div style='border: 1px #303030 solid; padding: 4px; color: #505050;'>" .
			_T('spipmotion:info_installer_encoder_ftp', $doc).
			"</div>";
		}
	else {  return
		"\n<div style='color: #505050;'>"
		._T('spipmotion:info_selectionner_fichier_encoder', $doc)
		."&nbsp;:<br />\n" .
		"\n<select name='chemin' size='1' class='fondl'>" .
		$texte_upload .
		"\n</select>" .
		"\n<div align='".
		$GLOBALS['spip_lang_right'] .
		"'><input name='sousaction3' type='submit' value='" .
		_T('spipmotion:bouton_encoder').
		"' class='fondo' /></div>" .
		"</div>\n";
	}
}
?>