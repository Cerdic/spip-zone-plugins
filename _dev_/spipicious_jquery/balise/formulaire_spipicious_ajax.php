<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_FORMULAIRE_SPIPICIOUS_AJAX ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_SPIPICIOUS_AJAX', array('id_article'));
}

function balise_FORMULAIRE_SPIPICIOUS_AJAX_stat($args, $filtres) {  
	// Pas d'id_article ? Erreur de squelette 
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_SPIPICIOUS_AJAX',
					'motif' => 'ARTICLES')), '');
	return $args;
}

function balise_FORMULAIRE_SPIPICIOUS_AJAX_dyn($id_article ) {
	$autorise = lire_config('spipicious/people');
	
	if (!$GLOBALS["auteur_session"] OR !in_array($GLOBALS['auteur_session']['statut'],$autorise)) {
		return '';
	} else {
		$auteur_nom = $GLOBALS['auteur_session']['nom'];
		$auteur_id = $GLOBALS['auteur_session']['email'];
		$auteur_statut = $GLOBALS['auteur_session']['statut'];
	}
	
	include_spip('spipicious_fonctions');

	//recuperation des variables utiles
	$tags = _request('tags');
	$id_groupe_tags = _request('select_groupe');
	$type_groupe_tags = _request('type_groupe');
	$add_tags = _request('add_tags');
	
	$autorise = lire_config('spipicious/people');
	
	if ($add_tags && $auteur_id){
	
	spip_query("DELETE FROM spip_spipicious WHERE id_auteur='$auteur_id' AND id_article='$id_article' "); // on efface les anciens triplets de cet auteur sur cet article 

	if ($tags && $auteur_id) {
		$tableau_tags = explode(",",$tags);
		if (is_array($tableau_tags)) {
			$position = 0;
			$tag_analysed = array();
		foreach ($tableau_tags as $k=>$tag) {
			$tag = trim($tag);

			if ($tag!="" && !in_array($tag,$tag_analysed)) {

			// doit on creer un nouveau tag ?
			$result = spip_query("SELECT * FROM spip_mots AS mots WHERE titre='$tag' AND id_groupe=$id_groupe_tags");

			if (spip_num_rows($result) == 0) { // creation tag
				$exist = 'mot inexistant';
				$sql = "INSERT INTO spip_mots (titre,id_groupe,type,idx) VALUES(".spip_abstract_quote(corriger_caracteres($tag)).",$id_groupe_tags,'$type_groupe_tags', 'oui')"; // FIXME encodage caractere ?
				$result = spip_query($sql);
				$id_tag = spip_insert_id();
			} else {  // on recupere l'id du tag
				$exist = 'mot existant';
				$log = spip_log($exist,'spipicious');
				while($row=spip_fetch_array($result)){
					$id_tag = $row['id_mot'];
					spip_log($id_tag,'spipicious');
				}
			}
			// on lie le mot au couple article (uniquement si pas deja fait)
			$result = spip_query("SELECT id_mot FROM spip_mots_articles WHERE id_mot=$id_tag AND id_article=$id_article");
			if (spip_num_rows($result) == 0) {
				spip_query("INSERT INTO spip_mots_articles(id_mot,id_article) VALUES('$id_tag','$id_article')");
			}
			
			spip_query("INSERT INTO spip_spipicious(id_mot,id_auteur,id_article,position) VALUES('$id_tag','$auteur_id','$id_article','$position')");
			$position++;
			}
		$tag_analysed[] = $tag;
		}
		}
		}
		include_spip ("inc/invalideur");
		suivre_invalideur("1");
		return header("Location: ".$_SERVER['HTTP_REFERER']);
	}
	else{
		return array('formulaires/formulaire_spipicious_ajax', 0,
			array('id' => $id_article,
				'auteur_nom' => $auteur_nom,
				'auteur_id' => $auteur_id,
		));
	}
	
}

?>