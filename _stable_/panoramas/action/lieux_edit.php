<?php

include_spip('inc/panoramas');
include_spip('inc/panoramas_edit');
if (!include_spip('inc/autoriser'))
	include_spip('inc/autoriser_compat');


function Lieux_update($id_lieu){
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$id_visite = intval(_request('id_visite'));
	$boucler = _request('boucler');
	$id_photo = intval(_request('id_photo'));
	$id_audio = intval(_request('id_audio'));
	$audio_repeter = _request('audio_repeter');
	$position_x_carte = intval(_request('position_x_carte'));
	$position_y_carte = intval(_request('position_y_carte'));
	
	//
	// Modifications des donnees du lieu
	//

	// creation
	if ($id_lieu == 'new' && $titre) {
		spip_query("INSERT INTO spip_visites_virtuelles_lieux (titre) VALUES ("._q($titre).")");
		$id_lieu = spip_insert_id();
	}
	// maj
	if (intval($id_lieu) && $titre) {
		$query = "UPDATE spip_visites_virtuelles_lieux SET ".
			"titre="._q($titre).", ".
			"descriptif="._q($descriptif).", ".
			"boucler="._q($boucler).", ".
			"id_photo="._q($id_photo).", ".
			"id_audio="._q($id_audio).", ".
			"audio_repeter="._q($audio_repeter).", ".
			"position_x_carte="._q($position_x_carte).", ".
			"position_y_carte="._q($position_y_carte).", ".
			"id_visite="._q($id_visite).
		" WHERE id_lieu="._q($id_lieu);
		$result = spip_query($query);
	}
	// lecture
	$result = spip_query("SELECT * FROM spip_visites_virtuelles_lieux WHERE id_lieu="._q($id_lieu));
	if ($row = spip_fetch_array($result)) {
		$id_visite = $row['id_visite'];
		$titre = $row['titre'];
		
	}

	return array($id_lieu);
}


function action_lieux_edit(){
	
	global $auteur_session;
	$arg = _request('arg');
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = str_replace("&amp;","&",urldecode(_request('redirect')));
	if ($redirect==NULL) $redirect="";
	if (!include_spip("inc/securiser_action"))
		include_spip("inc/actions");
	if (verifier_action_auteur("lieux_edit-$arg",$hash,$id_auteur)==TRUE) {
		$arg=explode("-",$arg);
		$id_lieu = $arg[0];
		if ((intval($id_lieu) && autoriser('modifier','lieu',$id_lieu))
			|| (($id_lieu=='new') && (autoriser('creer','lieu'))) ) {
			list($id_lieu) = Lieux_update($id_lieu);
			if ($redirect) $redirect = parametre_url($redirect,"id_lieu",$id_lieu);
		}
	}
	if ($redirect)
		redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
	
}

?>