<?php
/*
 * SPIPmotion
 * Gestion de l'encodage des videos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet
 * 2006-2008 - Distribue sous licence GNU/GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions'); // *action_auteur

function inc_infos_videos_dist($id, $id_document,$type,$script='',$ignore_flag = false) {
	global $connect_id_auteur, $connect_statut;
	
	if(_AJAX){
		include_spip('public/assembler');
		include_spip('inc/presentation');
	}
	$corps = recuperer_fond('prive_infos_video', $contexte=array('id_document'=>$id_document));
	
	// Si on a le droit de modifier les documents, on affiche les icones pour récupérer les infos et le logo
	if(autoriser('joindredocument',$type, $id)){
		$texte = _T('spipmotion:recuperer_logo');
		$texte2 = _T('spipmotion:recuperer_infos');
		$script = $type.'s';
		$redirect =  generer_url_ecrire($script,"id_$type=$id#portfolio_documents");
	
		// Inspire de inc/legender
		if (test_espace_prive()){
			$redirect = str_replace('&amp;','&',$redirect);
			$action = generer_action_auteur('spipmotion_logo', "$id/$type/$id_document", $redirect);
			$action = "<a href='$action'>$texte</a>";
			//$action = ajax_action_auteur('spipmotion_logo', "$id/$type/$id_document", $script, "type=$type&id_$type=$id&show_infos_docs=$id_document#infosdoc-$id_document", array($texte));
			$action2 = ajax_action_auteur('spipmotion_infos', "$id/$type/$id_document", $script, "type=$type&id_$type=$id&show_infos_docs=$id_document#infosdoc-$id_document", array($texte2));
		}
		else{
			$redirect = str_replace('&amp;','&',$redirect);
			$action = generer_action_auteur('spipmotion_logo', "$id/$type/$id_document", $redirect);
			$action = "<a href='$action'>$texte</a>";
			$action2 = generer_action_auteur('spipmotion_infos', "$id/$type/$id_document", $redirect);
			$action2 = "<a href='$action2'>$texte2</a>";
		}
		if(!_AJAX){
			$corps .= icone_horizontale($texte, $action, $supp, "creer.gif", false);
			$corps .= icone_horizontale($texte2, $action2, $supp, "creer.gif", false);
		}
	}
	//return ajax_action_greffe("spipmotion", $id_document, $corps);
	return $corps;
}
?>