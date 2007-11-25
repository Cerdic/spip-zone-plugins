<?php
/*
 * spipicious_jquery
 * Gestion de mots cles depuis l'espace public
 *
 * Auteurs :
 * kent1
 * © 2007 - Distribue sous licence GNU/GPL
 *
 */

function action_spipicious_del_mots(){
	global $auteur_session;
	$id_auteur = $auteur_session['id_auteur'];
		include_spip("inc/actions");
		$supprimer = _request('supprimer_mot');
		$type = _request('objet_spip');
		$id = _request('id_objet_spip');
		$redirect = urldecode(_request('redirect_del_mots'));
		
		// supprimer le mot ?
		if ($supprimer
		AND $s = sql_select("*","spip_spipicious","id_auteur=".$id_auteur." AND id_${type}=".$id." AND id_mot=".$supprimer)
		AND $t = sql_fetch($s)) {
			sql_delete("spip_spipicious","id_auteur=".$id_auteur." AND id_${type}=".$id." AND id_mot=".$supprimer); // on efface le mot associŽ a l'auteur sur l'objet
			spip_log("suppression spipiciousmot (id_$type=$id) id_mot=".$supprimer."", 'spipicious');
			$newquery = sql_select("*","spip_spipicious","id_mot=".$supprimer);
			$newt = sql_fetch($newquery);
			if (!$newt){
				sql_delete("spip_mots_".$type."s","id_mot=".$supprimer." AND id_".$type."=".$id); 
				spip_log("suppression spip_mots_".$type."s (id_article=$id) non utilise id_mot=".$supprimer, 'spipicious');
				sql_delete("spip_mots","id_mot=".$supprimer); // on efface le mot si il n'est plus associŽ ˆ rien
				spip_log("suppression spip_mot non utilise id_mot=".$supprimer, 'spipicious');
			}
			else {
				spip_log("mot toujours utilise : id_mot=".$delete, 'spipicious');
			}
			$invalider = true;
		}
	if ($invalider) {
		include_spip('inc/invalideur');
		suivre_invalideur("0",true);
		spip_log('invalider', 'spipicious');
	}
	redirige_par_entete(str_replace("&amp;","&",$redirect));
}

?>