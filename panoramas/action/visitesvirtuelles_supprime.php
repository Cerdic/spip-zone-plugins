<?php
include_spip('inc/panoramas');

function action_visitesvirtuelles_supprime(){
	global $auteur_session;
	$id_visite = _request('arg');
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = _request('redirect');
	if ($redirect==NULL) $redirect="";
	if (!include_spip("inc/securiser_action"))
		include_spip("inc/actions");
	if (verifier_action_auteur("visitesvirtuelles_supprime-$id_visite",$hash,$id_auteur)==TRUE) {
	if (!include_spip('inc/autoriser'))
			include_spip('inc/autoriser_compat');
		if (autoriser('supprimer','visitevirtuelle',$id_visite)){
			$result = sql_delete('spip_visites_virtuelles', "id_visite="._q($id_visite));
			$result = sql_delete('spip_visites_virtuelles_lieux', "id_visite="._q($id_visite));
			$result = sql_delete('spip_visites_virtuelles_interactions', "id_visite="._q($id_visite));
		}
	}
	redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
}

?>