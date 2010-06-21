<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions'); // *action_auteur

function exec_spipmotion_infos_dist(){
	$type = _request("type");
	$id = _request('id_article');
	$script = _request("script"); // generalisation a tester
	$id_document= _request('show_infos_docs');
	exec_spipmotion_infos_args($id, $type, $id_document, $script);
}

function exec_spipmotion_infos_args($id_article, $type,$id_document,$script) {
	include_spip('inc/actions');
	$infos_videos = charger_fonction('spipmotion_infos_videos', 'inc');
	$res = $infos_videos($id, $id_document,$type,$script, 'ajax');
	ajax_retour($res);
}
?>