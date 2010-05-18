<?php
include_spip('inc/panoramas');

function action_interactions_supprime(){
	global $auteur_session;
	$id_interaction = _request('arg');
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = _request('redirect');
	if ($redirect==NULL) $redirect="";
	if (!include_spip("inc/securiser_action"))
		include_spip("inc/actions");
	if (verifier_action_auteur("interactions_supprime-$id_interaction",$hash,$id_auteur)==TRUE) {
		if (!include_spip('inc/autoriser'))
			include_spip('inc/autoriser_compat');
		if (autoriser('supprimer','interaction',$id_interaction)){
			$result = sql_delete('spip_visites_virtuelles_interactions', "id_interaction="._q($id_interaction));
		}
	}
	redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
}

?>