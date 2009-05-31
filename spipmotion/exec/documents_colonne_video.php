<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@exec_documents_colonne_dist
function exec_documents_colonne_video_dist()
{
	$id = intval(_request('id'));
	$show = _request('show_videos');
	$type = _request('type');

	if (!autoriser('joindredocument', $type, $id)) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	include_spip('inc/documents');
	include_spip('inc/presentation');
	include_spip('inc/spipmotion');

	$script = $type."s_edit";
	$res = "";
	foreach(explode(",",$show) as $doc) {
		$res .= afficher_videos_joindre($id, $type="article", $flag_modif = true);
	} 
	ajax_retour("<div class='upload_answer_video upload_document_video_added'>". $res.	"</div>",false);
}
?>