<?php

/**
 * spip.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Erational
 *
 * © 2007-2012 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function action_spipicious_supprimer_tags_dist(){
	global $visiteur_session;

	$id_objet = _request('spipicious_id');
	$type = _request('spipicious_type');

	include_spip('inc/autoriser');
	if(!autoriser('tagger_spipicious',$type,$id_objet))
		return false;

	$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	$id_table_objet = id_table_objet($type);

	$remove_tags = _request('remove_tags');

	$suppression = spipicious_supprimer_tags($remove_tags,$id_auteur,$id_objet,$type,$id_table_objet);
	return $suppression;
}

function spipicious_supprimer_tags($remove_tags,$id_auteur,$id_objet,$type,$id_table_objet){
	$compte = 0;
	$tags_removed = array();
	foreach($remove_tags as $remove_tag){
		include_spip('action/editer_mot');
		// On le vire de notre auteur dans spipicious
		sql_delete("spip_spipicious","id_auteur=".intval($id_auteur)." AND id_objet=".intval($id_objet)." AND id_mot=".intval($remove_tag)." AND objet=".sql_quote($type)); // on efface le mot associe a l'auteur sur l'objet
		$invalider = true;

		$titre_mot = sql_getfetsel("titre","spip_mots","id_mot=".intval($remove_tag));

		// Utilisation par un autre utilisateur => sinon : il n'est plus du tout utilise =>
		// suppression du mot pure et simple dans spip_mots_$type et spip_mot
		$newt = sql_getfetsel("id_auteur","spip_spipicious","id_mot=".intval($remove_tag));
		if (!$newt)
			mot_supprimer($remove_tag);
		else {
			// Utilisation par un autre utilisateur ok mais utilisation sur le meme id_$type
			$newt2 = sql_getfetsel("id_auteur","spip_spipicious","id_mot=".intval($remove_tag)." AND id_objet=".intval($id_objet)." AND objet=".sql_quote($type));
			if(!$newt2){
				mot_dissocier($remove_tag,array($type=>$id_objet));
			}
		}
		$message = _T('spipicious:tag_supprime',array('name'=>$titre_mot));
		$tags_removed[] = $titre_mot;
		$compte++;
	}

	if($compte > 1){
		$tags = implode('<br />',$tags_removed);
		$message = _T('spipicious:tags_supprimes',array('name'=>$tags,'nb'=>$compte));
	}

	return array($message,$invalider,'');
}
?>