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

function inc_encoder_videos_dist($v) {
	global $spip_lang_right;

	$vignette_de_doc = ($v['mode'] == 'vignette' AND $v['id_document']>0);

	# indiquer un choix d'upload FTP
	$dir_ftp = '';
	if (test_espace_prive()
	AND !($v['mode'] == 'vignette')	# si c'est pour un document
	AND !$vignette_de_doc		# pas pour une vignette (NB: la ligne precedente suffit, mais si on la supprime il faut conserver ce test-ci)
	AND $GLOBALS['flag_upload']) {
		if($dir = determine_upload('documents')) {
			// quels sont les docs accessibles en ftp ?
			$l = texte_encoder_manuel_videos($dir, '', $v['mode']);
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
  if($v['iframe_script'])
    $iframe = "<input type='hidden' name='iframe_redirect' value='".rawurlencode($v['iframe_script'])."' />\n";

	if ($vignette_de_doc)
		$res = $milieu . $res;
	else
		$res = $res . $milieu;
	// Un menu depliant si on a une possibilite supplementaire

	$res =  generer_action_auteur('encoder_video',
		(intval($v['id']) .'/' .intval($v['id_document']) . "/".$v['mode'].'/'.$v['type']),
		(!test_espace_prive())?$v['script']:generer_url_ecrire($v['script'], $v['args'], true),
		"$iframe$debut$res$dir_ftp$distant$fin",
		" method='post' enctype='multipart/form-data' class='form_encode'");
		
	if ($v['cadre']) {
		$debut_cadre = 'debut_cadre_'.$v['cadre'];
		$fin_cadre = 'fin_cadre_'.$v['cadre'];
		$res1 = $debut_cadre($v['icone'], true, $v['fonction'], $v['titre']);
		if ($dir_ftp){
			$res = $res;
		}
		else{
		$res = _T('spipmotion:info_installer_encoder_ftp');
		}
			$res1 .= $res . $fin_cadre(true);
	}
	
	return "\n<div class='joindre'>".$res1."</div>\n";
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
				if (($ext == 'mp4') OR ($ext == 'avi') OR ($ext == 'mpg') OR ($ext == 'mov'))
			  $texte_upload[] = "\n<option value=\"$f\">" .
			    str_repeat("&nbsp;",$k+2) .
			    $lefichier .
			    "</option>";
				
			}
		}
	} 

	$texte = join('', $texte_upload);
	if (!$texte) {
		
	}
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
	else {  
	$jsfunction = '$("#encodage").css("opacity",0.5).html("'._T("spipmotion:encodage_en_cours").'");';
	return
		"<div style='color: #505050;' id='encodage'><div id='encodage_txt'>"
		._T('spipmotion:info_selectionner_fichier_encoder', $doc)
		."&nbsp;:<br /></div>\n" .
		"\n<select name='chemin' id='chemin_encodage' size='1' class='fondl'>" .
		$texte_upload .
		"\n</select>" .
		"\n</div><div align='".
		$GLOBALS['spip_lang_right'] .
		"'><input  name='sousaction3' type='submit' value='"._T('spipmotion:bouton_encoder')."' class='fondo' />" .
		"</div>\n";
	}
}
?>