<?php
/*
 * SPIPmotion
 * Gestion de l'encodage des videos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet
 * 2006 - Distribue sous licence GNU/GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions'); // *action_auteur et determine_upload


function inc_infos_videos_dist($id, $id_document,$type) {
	global $connect_id_auteur, $connect_statut;
	$script = _request('exec');
	$texte = _T('spipmotion:recuperer_logo');
	
	// Inspire de inc/legender
	if (test_espace_prive())
		$action = ajax_action_auteur('spipmotion_logo', "$id/$type/$id_document", $script, "id_$type=$id&type=$type", array($texte));
	else{
		$redirect = str_replace('&amp;','&',$script);
		$action = generer_action_auteur('spipmotion_logo', "$id/$type/$id_document", $redirect);
		$action = "<a href='$action'>$texte</a>";
	}
	$corps = icone_horizontale($texte, $action, $supp, "supprimer.gif", false);
	$corps = block_parfois_visible("spipmotion_logo-aff-$id_document", sinon($texte,_T('info_sans_titre')), $corps, "text-align:center;", $flag);

	return ajax_action_greffe("spipmotion_logo", $id_document, $corps);
	
	// Ajouter le formulaire d'encodage de videos
	$res =  generer_action_auteur('spipmotion_logo',
		(intval($id).'/'.intval($id_document).'/'.$type),
		(!test_espace_prive())?$v['script']:generer_url_ecrire('documenter', 'id_article='.$id.'&type='.$type, true),
		"$iframe$debut$res$dir_ftp$distant$fin",
		" method='post' enctype='multipart/form-data' class='spipmotion'");
	return $res;
}
?>