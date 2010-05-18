<?php
include_spip('inc/panoramas');

function action_lieux_supprime(){
	global $auteur_session;
	$id_lieu = _request('arg');
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = _request('redirect');
	if ($redirect==NULL) $redirect="";
	if (!include_spip("inc/securiser_action"))
		include_spip("inc/actions");
	if (verifier_action_auteur("lieux_supprime-$id_lieu",$hash,$id_auteur)==TRUE) {
		if (!include_spip('inc/autoriser'))
			include_spip('inc/autoriser_compat');
		if (autoriser('supprimer','lieu',$id_lieu)){
			$result = sql_delete('spip_visites_virtuelles_lieux', "id_lieu="._q($id_lieu));
			$result = sql_delete('spip_visites_virtuelles_interactions', "id_lieu="._q($id_lieu));
		}
	}
	redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
}

?>