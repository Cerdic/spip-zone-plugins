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

function exec_spipmotion_infos_dist()
{
	$type = _request("type");
	$id = _request('id_article');
	//$id = intval(_request(id_table_objet($type)));
	$script = _request("script"); // generalisation a tester
	$id_document= _request('show_infos_docs');
	exec_spipmotion_infos_args($id, $type, $id_document, $script);
	spip_log('exec_spipmotion_infos_dist');
}

function exec_spipmotion_infos_args($id_article, $type,$id_document,$script) {
		include_spip('inc/actions');
		$infos_videos = charger_fonction('spipmotion_infos_videos', 'inc');
		if(_request("iframe")=="iframe") {
			$res = $infos_videos($id, $id_document,$type,$script, 'ajax').
			  $infos_videos($id, $id_document,$type,$script, 'ajax');
			ajax_retour("<div class='upload_answer upload_document_added'>".$res."</div>",false);
		} else ajax_retour($infos_videos($id, $id_document,$type,$script, 'ajax'));
	//return ajax_action_greffe("spipmotion", $id_document, $corps);
}
?>