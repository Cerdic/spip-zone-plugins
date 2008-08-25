<?php

include_spip('inc/panoramas');
include_spip('inc/panoramas_edit');
if (!include_spip('inc/autoriser'))
	include_spip('inc/autoriser_compat');


function Interactions_update($id_interaction){
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$id_visite = intval(_request('id_visite'));
	$id_lieu = intval(_request('id_lieu'));
	$x1 = intval(_request('x1'));
	$x2 = intval(_request('x2'));
	$y1 = intval(_request('y1'));
	$y2 = intval(_request('y2'));
	$id_image_fond = intval(_request('id_image_fond'));
	$type = _request('type');
	$x_lieu_cible = intval(_request('x_lieu_cible'));
	$id_article_cible = intval(_request('id_article_cible'));
	$id_rubrique_cible = intval(_request('id_rubrique_cible'));
	$id_jeu_cible = intval(_request('id_jeu_cible'));
	$id_lieu_cible = intval(_request('id_lieu_cible'));
	$id_document_cible = intval(_request('id_document_cible'));
	$id_visite_cible = intval(_request('id_visite_cible'));
	$url_cible = _request('url_cible');
	$id_objet = intval(_request('id_objet'));
	$id_personnage = intval(_request('id_personnage'));
	$id_objet_activation = intval(_request('id_objet_activation'));
	$id_jeu_activation = intval(_request('id_jeu_activation'));
	$id_lieu_activation = intval(_request('id_lieu_activation'));

	$id_personnage_survol = intval(_request('id_personnage_survol'));
	$texte_avant_activation = _request('texte_avant_activation');
	$texte_apres_activation = _request('texte_apres_activation');
	$id_audio_avant_activation = intval(_request('id_audio_avant_activation'));
	$id_audio_apres_activation = intval(_request('id_audio_apres_activation'));
	$images_transition = _request('images_transition');
	$images_transition_delai = intval(_request('images_transition_delai'));
	$id_film_transition = intval(_request('id_film_transition'));
	
	//
	// Modifications des donnees d'une interaction
	//

	// creation
	if ($id_interaction == 'new' && $titre) {
		spip_query("INSERT INTO spip_visites_virtuelles_interactions (titre) VALUES ("._q($titre).")");
		$id_interaction = spip_insert_id();
	}
	// maj
	if (intval($id_interaction) && $titre) {
		$query = "UPDATE spip_visites_virtuelles_interactions SET ".
			"titre="._q($titre).", ".
			"descriptif="._q($descriptif).", ".
			"x1="._q($x1).", ".
			"x2="._q($x2).", ".
			"y1="._q($y1).", ".
			"y2="._q($y2).", ".
			"id_image_fond="._q($id_image_fond).", ".
			"type="._q($type).", ".
			"x_lieu_cible="._q($x_lieu_cible).", ".
			"id_article_cible="._q($id_article_cible).", ".
			"id_lieu_cible="._q($id_lieu_cible).", ".
			"id_document_cible="._q($id_document_cible).", ".
			"id_visite_cible="._q($id_visite_cible).", ".
			"url_cible="._q($url_cible).", ".
			"id_objet="._q($id_objet).", ".
			"id_personnage="._q($id_personnage).", ".
			"id_objet_activation="._q($id_objet_activation).", ".
			"id_jeu_activation="._q($id_jeu_activation).", ".
			"id_lieu_activation="._q($id_lieu_activation).", ".
			"id_rubrique_cible="._q($id_rubrique_cible).", ".
			"id_jeu_cible="._q($id_jeu_cible).", ".
			"id_personnage_survol="._q($id_personnage_survol).", ".
			"texte_avant_activation="._q($texte_avant_activation).", ".
			"texte_apres_activation="._q($texte_apres_activation).", ".
			"id_audio_avant_activation="._q($id_audio_avant_activation).", ".
			"id_audio_apres_activation="._q($id_audio_apres_activation).", ".
			"images_transition="._q($images_transition).", ".
			"images_transition_delai="._q($images_transition_delai).", ".
			"id_film_transition="._q($id_film_transition).", ".
			"id_lieu="._q($id_lieu).", ".
			"id_visite="._q($id_visite).
		" WHERE id_interaction="._q($id_interaction);
		$result = spip_query($query);
	}
	// lecture
	$result = spip_query("SELECT * FROM spip_visites_virtuelles_interactions WHERE id_interaction="._q($id_interaction));
	if ($row = spip_fetch_array($result)) {
		$id_interaction = $row['id_interaction'];
		$titre = $row['titre'];
		
	}

	return array($id_interaction);
}


function action_interactions_edit(){
	
	global $auteur_session;
	$arg = _request('arg');
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = str_replace("&amp;","&",urldecode(_request('redirect')));
	if ($redirect==NULL) $redirect="";
	if (!include_spip("inc/securiser_action"))
		include_spip("inc/actions");
	if (verifier_action_auteur("interactions_edit-$arg",$hash,$id_auteur)==TRUE) {
		$arg=explode("-",$arg);
		$id_interaction = $arg[0];
		if ((intval($id_interaction) && autoriser('modifier','interaction',$id_interaction))
			|| (($id_interaction=='new') && (autoriser('creer','interaction'))) ) {
			list($id_interaction) = Interactions_update($id_interaction);
			if ($redirect) $redirect = parametre_url($redirect,"id_interaction",$id_interaction);
		}
	}
	if ($redirect)
		redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
	
}

?>