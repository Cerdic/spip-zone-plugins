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
	$texte = _T('spipmotion:recuperer_logo');
	$texte2 = _T('spipmotion:recuperer_infos');
	$script = $type.'s';
	$redirect =  generer_url_ecrire($script,"id_$type=$id&show_docs=$id_document#documenter");
	
	// Inspire de inc/legender
	if (test_espace_prive()){
		$action = ajax_action_auteur('spipmotion_logo', "$id/$type/$id_document", $script, "id_$type=$id&type=$type&show_docs=$id_document&spipmotion=$id_document#spipmotion-$id_document", array($texte));
		$action2 = ajax_action_auteur('spipmotion_infos', "$id/$type/$id_document", $script, "id_$type=$id&type=$type&show_docs=$id_document&spipmotion=$id_document#spipmotion-$id_document", array($texte2));
	}
	else{
		$redirect = str_replace('&amp;','&',$script);
		$action = generer_action_auteur('spipmotion_logo', "$id/$type/$id_document", $redirect);
		$action = "<a href='$action'>$texte</a>";
		$action2 = generer_action_auteur('spipmotion_infos', "$id/$type/$id_document", $redirect);
		$action2 = "<a href='$action2'>$texte2</a>";
	}
	$corps = icone_horizontale($texte, $action, $supp, "creer.gif", false);
	$corps .= icone_horizontale($texte2, $action2, $supp, "creer.gif", false);

	return ajax_action_greffe("spipmotion", $id_document, $corps);
}
?>