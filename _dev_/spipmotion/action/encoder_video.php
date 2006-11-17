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
global $hash, $url, $chemin, $ancre,
	  $sousaction1,
	  $sousaction2,
	  $sousaction3,
	  $sousaction4,
	  $sousaction5,
	  $_FILES,  $HTTP_POST_FILES;

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$redirect = _request('redirect');
	$iframe_redirect = _request('iframe_redirect');
	if (!preg_match(',^(-?\d+)\D(\d+)\D(\w+)/(\w+)$,',_request('arg'),$r)) {
	  spip_log("action_encoder_videos_dist incompris: " . _request('arg'));
	  redirige_par_entete(urldecode($redirect));
	}
	list($arg, $id, $id_document, $mode, $type) = $r;

     // pas terrible, mais c'est le pb du bouton Submit qui retourne son texte,
     // et son transcodage est couteux et perilleux
     $sousaction = 'spip_action_encoder_video' .
       ($sousaction1 ? 1 :
	($sousaction2 ? 2 :
	 ($sousaction3 ? 3 : 
	  ($sousaction4 ? 4 :
	   $sousaction5 ))));

     $path = ($sousaction1 ? ($_FILES ? $_FILES : $HTTP_POST_FILES) :
	     ($sousaction2 ? $url : $chemin));

     $documents_actifs = array();

     if (function_exists($sousaction))
       $type_image = $sousaction($path, $mode, $type, $id, $id_document, 
				 $hash, $redirect, $documents_actifs, $iframe_redirect);

     else spip_log("spip_action: sousaction inconnue $sousaction");

     $redirect = urldecode($redirect);
     if ($documents_actifs) {
	$redirect .= '&show_docs=' . join(',',$documents_actifs);
     }

	if (!$ancre) {
		$ancre = 'videos';
	}

	$redirect .= '#' . $ancre;

	if(_request("iframe") == 'iframe') {
		$redirect = urldecode($iframe_redirect)."&show_docs=".join(',',$documents_actifs)."&iframe=iframe";
	}

	redirige_par_entete($redirect);
     ## redirection a supprimer si on veut poster dans l'espace prive directement (UPLOAD_DIRECT)

}


// http://doc.spip.org/@spip_action_joindre3
function spip_action_encoder_video3($path, $mode, $type, $id, $id_document,$hash, $redirect, &$actifs, $iframe_redirect)
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

	$encodage = charger_fonction('encodage', 'inc');
	return $encodage($files);
}

?>