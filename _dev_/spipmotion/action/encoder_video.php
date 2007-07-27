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

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('base/abstract_sql');
include_spip('inc/actions');

function action_encoder_video_dist(){
	global $redirect;
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(',^(-?\d+)\D(\d+)\D(\w+)/(\w+)$,',$arg,$r)) {
		spip_log("action_encoder_dist incompris: " . $arg);
		$redirect = urldecode(_request('redirect'));
		return;
	}
	list(, $id, $id_document, $mode, $type) = $r;
	$actifs = array();
	$redirect = action_encoder_video_sous_action($id, $id_document, $mode, $type, $actifs);
}

function action_encoder_video_sous_action($id, $id_document, $mode, $type, &$documents_actifs)
{

	$hash = _request('hash');
	$url = _request('url');
	$chemin = _request('chemin');
	$ancre = _request('ancre');
	$sousaction1 = _request('sousaction1');
	$sousaction2 = _request('sousaction2');
	$sousaction3 = _request('sousaction3');
	$sousaction4 = _request('sousaction4');
	$sousaction5 = _request('sousaction5');
	$redirect = _request('redirect');
	$iframe_redirect = _request('iframe_redirect');
	
	// pas terrible, mais c'est le pb du bouton Submit qui retourne son texte,
// et son transcodage est couteux et perilleux
	$sousaction = 
       ($sousaction1 ? 1 :
	($sousaction2 ? 2 :
	 ($sousaction3 ? 3 : 
	  ($sousaction4 ? 4 :
	   $sousaction5 ))));

     $path = ($sousaction1 ? ($_FILES ? $_FILES : $GLOBALS['HTTP_POST_FILES']) :
	     ($sousaction2 ? $url : $chemin));

     $sousaction = charger_fonction('encoder_video' . $sousaction, 'inc');
     $type_video = $sousaction($path, $mode, $type, $id, $id_document, 
		 $hash, $redirect, $documents_actifs, $iframe_redirect);

     $redirect = urldecode($redirect);
     if ($documents_actifs) {
	$redirect = parametre_url($redirect,'show_docs',join(',',$documents_actifs),'&');
     }
     
    if (!$ancre) {

		if ($mode=='vignette')
			$ancre = 'images';
		else if ($type_video)
			$ancre = 'portfolio';
		else
			$ancre = 'documents';
     }

    $redirect .= '#' . $ancre;
    if ($type == 'rubrique') {
	include_spip('inc/rubriques');
	calculer_rubriques();
     }

	if(_request("iframe") == 'iframe') {
		$redirect = parametre_url(urldecode($iframe_redirect),"show_docs",join(',',$documents_actifs),'&')."&iframe=iframe";
	}
	return $redirect;
}

// http://doc.spip.org/@inc_joindre3_dist
function inc_encoder_video3_dist($path, $mode, $type, $id, $id_document,$hash, $redirect, &$actifs, $iframe_redirect)
{
	if (!$path || strstr($path, '..')) return;
	    
	$upload = determine_upload();
	if ($path != '/' AND $path != './') $upload .= $path;

	if (!is_dir($upload))
	  // seul un fichier est demande
	  $files = array(array ('name' => basename($upload),
				'tmp_name' => $upload)
			 );
	else {
	  include_spip('inc/documents');
	  $files = array();
	  foreach (preg_files($upload) as $fichier) {
			$files[]= array (
					'name' => basename($fichier),
					'tmp_name' => $fichier
					);
	  }
	}
	return encoder_video($files, $mode, $type, $id, $id_document, $hash, $redirect, &$actifs, $iframe_redirect);
}

function encoder_video($files, $mode, $type, $id, $id_document, $hash, $redirect, &$actifs, $iframe_redirect)
{
	$encodage = charger_fonction('encodage', 'inc');

	foreach ($files as $arg) {
		$x = $encodage($arg['tmp_name'], $arg['name'], $type, $id, $mode, $id_document, $actifs);
	}
	// un invalideur a la hussarde qui doit marcher au moins pour article, breve, rubrique
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_$type/$id'");
	return $x;
}

?>