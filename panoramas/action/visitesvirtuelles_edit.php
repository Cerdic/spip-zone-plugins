<?php

include_spip('inc/panoramas');
include_spip('inc/panoramas_edit');
if (!include_spip('inc/autoriser'))
	include_spip('inc/autoriser_compat');


function Visitesvirtuelles_update($id_visite){
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$largeur = intval(_request('largeur'));
	$hauteur = intval(_request('hauteur'));
	$id_lieu_depart = intval(_request('id_lieu_depart'));
	$id_carte = intval(_request('id_carte'));
	$mode_jeu = _request('mode_jeu');
	$liste_objets_jeu = _request('liste_objets_jeu');
	$message_fin_jeu = _request('message_fin_jeu');
	$url_fin_jeu = _request('url_fin_jeu');
	
	//
	// Modifications des donnees de base de la visite virtuelle
	//

	// creation
	if ($id_visite == 'new' && $titre) {
		$id_visite = sql_insertq('spip_visites_virtuelles', array('titre' => _q($titre)));

	}
	// maj
	if (intval($id_visite) && $titre) {
		$result = sql_update('spip_visites_virtuelles', array(
			'titre' => _q($titre), 
			'descriptif' => _q($descriptif), 
			'id_lieu_depart' => _q($id_lieu_depart),
			'id_carte' => _q($id_carte), 
			'largeur' => _q($largeur),
			'hauteur' => _q($hauteur), 
			'mode_jeu' => _q($mode_jeu), 
			'liste_objets_jeu' => _q($liste_objets_jeu), 
			'message_fin_jeu' => _q($message_fin_jeu),
			'url_fin_jeu' => _q($url_fin_jeu)), 
			"id_visite="._q($id_visite));
	}
	// lecture
	//$result = sql_query("SELECT * FROM spip_visites_virtuelles WHERE id_visite="._q($id_visite));
	$result = sql_select('*', 'spip_visites_virtuelles', "id_visite="._q($id_visite));
	if ($row = sql_fetch($result)) {
		$id_visite = $row['id_visite'];
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$largeur = $row['largeur'];
		$hauteur = $row['hauteur'];
		$id_lieu_depart = $row['id_lieu_depart'];
		$liste_objets_jeu = $row['liste_objets_jeu'];
		$mode_jeu = $row['mode_jeu'];
		$message_fin_jeu = $row['message_fin_jeu'];
		$url_fin_jeu = $row['url_fin_jeu'];
	}

	return array($id_visite);
}

function action_visitesvirtuelles_edit(){
	
	global $auteur_session;
	$arg = _request('arg');
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = str_replace("&amp;","&",urldecode(_request('redirect')));
	if ($redirect==NULL) $redirect="";
	if (!include_spip("inc/securiser_action"))
		include_spip("inc/actions");
	if (verifier_action_auteur("visitesvirtuelles_edit-$arg",$hash,$id_auteur)==TRUE) {
		$arg=explode("-",$arg);
		$id_visite = $arg[0];
		$id_lieu = $arg[1];
		if ((intval($id_visite) && autoriser('modifier','visitevirtuelle',$id_visite))
			|| (($id_visite=='new') && (autoriser('creer','visitevirtuelle'))) ) {
			list($id_visite) = Visitesvirtuelles_update($id_visite);
			if ($redirect) $redirect = parametre_url($redirect,"id_visite",$id_visite);
		}
	}
	if ($redirect)
		redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
	
}

?>