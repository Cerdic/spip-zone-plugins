<?php
/**
 * XMP php
 * Récupération des métadonnées XMP
 *
 * Auteur : kent1
 * ©2011 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions'); // *action_auteur

function exec_xmpphp_infos_dist(){
	$type = _request("type");
	$id = _request('id_article');
	$script = _request("script"); // generalisation a tester
	$id_document= _request('show_infos_docs');
	exec_xmpphp_infos_args($id, $type, $id_document, $script);
}

function exec_xmpphp_infos_args($id_article, $type,$id_document,$script) {
	include_spip('inc/actions');
	$infos_fichiers = charger_fonction('xmpphp_infos_fichiers', 'inc');
	$res = $infos_fichiers($id, $id_document,$type,$script, 'ajax');
	ajax_retour($res);
}
?>