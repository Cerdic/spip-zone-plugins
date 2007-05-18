<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_FORMULAIRE_SPIPICIOUS_AJAX ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_SPIPICIOUS_AJAX', array('id_article'));
}

/*
* La fonction statique retourne fait les verifications sur les variables r�up��s par collecte
* ici, $args[0] contient donc id_article.
*
* ensuite en renvoi les arguments en questions pour la balise dynamique.
* 
*/
function balise_FORMULAIRE_SPIPICIOUS_AJAX_stat($args, $filtres) {  
	// Pas d'id_article ? Erreur de squelette 
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_SPIPICIOUS_AJAX',
					'motif' => 'ARTICLES')), '');
	return $args;
}
/* la fonction dynamique fait les calculs et renvois le squelette qu'il faut afficher.
_request() cherche dans les valeurs post.
*/

function balise_FORMULAIRE_SPIPICIOUS_AJAX_dyn($id_article) {

	global $auteur_session;
	$auteur_nom = $auteur_session['nom'];
	$auteur_id = $auteur_session['id_auteur'];

	include_spip('spipicious_fonctions');
	$msg = "** testing **";

	//recuperation des variables utiles
	$tags = _request('tags');  // FIXME traitement encodage ? 
	//$id_article = _request('id_article');

	$id_groupe_tags = _request('select_groupe');
	$type_groupe_tags = _request('type_groupe');

	// action ?
	if (_request('add_tags')&& $auteur_id)
	spip_query("DELETE FROM spip_spipicious WHERE id_auteur='$auteur_id' AND id_article='$id_article' "); // on efface les anciens triplets de cet auteur sur cet article 

	if ($tags && $auteur_id) {
		$tableau_tags = explode(",",$tags);
		if (is_array($tableau_tags)) {
			$position = 0;
			$tag_analysed = array();
		foreach ($tableau_tags as $k=>$tag) {
			$tag = trim($tag);

			if ($tag!="" && !in_array($tag,$tag_analysed)) {   //  EXTRA verifier si ce tag n'est pas blackliste  

			// doit on creer un nouveau tag ?
			$result = spip_query("SELECT * FROM spip_mots AS mots WHERE titre='$tag' AND id_groupe=$id_groupe_tags");

			if (spip_num_rows($result) == 0) { // creation tag
				spip_log('mots inexistant');
				$sql = "INSERT INTO spip_mots (titre,id_groupe,type,idx) VALUES(".spip_abstract_quote(corriger_caracteres($tag)).",$id_groupe_tags,'$type_groupe_tags', 'oui')"; // FIXME encodage caract�e ?
				$result = spip_query($sql);
				$id_tag = spip_insert_id();
			} else {  // on recupere l'id du tag
				spip_log('mots existant');
				while($row=spip_fetch_array($result)){
					$id_tag = $row['id_mot'];
				}
			}

			// on lie le mot au couple article (uniquement si pas deja fait)
			$result = spip_query("SELECT id_mot FROM spip_mots_articles WHERE id_mot=$id_tag AND id_article=$id_article");
			if (spip_num_rows($result) == 0) {
				spip_query("INSERT INTO spip_mots_articles(id_mot,id_article) VALUES('$id_tag','$id_article')");
			}

			// auteur identifie:  on enregistre le couple (mot / article / auteur) ds la table spipicious
			// FIXME verifier si table installe
			spip_query("INSERT INTO spip_spipicious(id_mot,id_auteur,id_article,position) VALUES('$id_tag','$auteur_id','$id_article','$position')");
			$position++;
			}
		$tag_analysed[] = $tag;
		}
		}
	}

	spipicious_maintenance_nuage_article($id_article);

	// retour sur le formulaire
	include_spip ("inc/invalideur");
	suivre_invalideur("1");
	return header("Location: ".$_SERVER['HTTP_REFERER']);
}

?>