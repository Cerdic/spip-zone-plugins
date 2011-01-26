<?php

/**
 * spip.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 * Erational
 *
 * © 2007-2011 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function action_spipicious_supprimer_tags_dist(){
	global $visiteur_session;

	$id_objet = _request('spipicious_id');
	$type = _request('spipicious_type');

	include_spip('inc/autoriser');
	if(!autoriser('tagger_spipicious',$type,$id_objet,$visiteur_session,$opt)){
		return '';
	}

	$id_auteur = $visiteur_session['id_auteur'];
	$id_groupe = lire_config('spipicious/groupe_mot','1');
	$id_table_objet = id_table_objet($type);
	$table_mot = table_objet_sql('spip_mots_'.table_objet($type));

	$remove_tags = _request('remove_tags');

	$suppression = spipicious_supprimer_tags($remove_tags,$id_auteur,$id_objet,$type,$id_table_objet,$table_mot,$id_groupe);
	return $suppression;
}

function spipicious_supprimer_tags($remove_tags,$id_auteur,$id_objet,$type,$id_table_objet,$table_mot,$id_groupe){
	$compte = 0;
	$tags_removed = array();
	foreach($remove_tags as $remove_tag){

		// On le vire de notre auteur dans spipicious
		sql_delete("spip_spipicious","id_auteur=".intval($id_auteur)." AND id_objet=".intval($id_objet)." AND id_mot=".intval($remove_tag)." AND objet=".sql_quote($type)); // on efface le mot associe a l'auteur sur l'objet
		$invalider = true;

		$titre_mot = sql_getfetsel("titre","spip_mots","id_mot=".intval($remove_tag));

		// Utilisation par un autre utilisateur => sinon : il n'est plus du tout utilise =>
		// suppression du mot pure et simple dans spip_mots_$type et spip_mot
		$newt = sql_getfetsel("id_auteur","spip_spipicious","id_mot=".intval($remove_tag));
		if (!$newt){
			sql_delete("$table_mot","id_mot=".$remove_tag." AND $id_table_objet=".intval($id_objet));
			sql_delete("spip_mots","id_mot=$remove_tag"); // on efface le mot si il n'est plus associe a rien
		}
		else {
			// Utilisation par un autre utilisateur ok mais utilisation sur le meme id_$type
			$newt2 = sql_getfetsel("id_auteur","spip_spipicious","id_mot=".intval($remove_tag)." AND id_objet=".intval($id_objet)." AND objet=".sql_quote($type));
			if(!$newt2){
				sql_delete("$table_mot","id_mot=".intval($remove_tag)." AND $id_table_objet=".intval($id_objet));
			}
		}
		$message = _T('spipicious:tag_supprime',array('name'=>$titre_mot));
		$tags_removed[] = $titre_mot;
		$compte++;
	}

	if($compte > 1){
		$tags = implode('<br />',$tags_removed);
		$message = _T('spipicious:tags_supprimes',array('name'=>$tags));
	}

	return array($message,$invalider,'');
}
?>