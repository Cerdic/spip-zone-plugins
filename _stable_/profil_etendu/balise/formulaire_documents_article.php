<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');

// Balise independante du contexte

function balise_FORMULAIRE_DOCUMENTS_ARTICLE ($p) {

	return calculer_balise_dynamique($p, 'FORMULAIRE_DOCUMENTS_ARTICLE', array('id_article','id_rubrique','statut'));
}

function balise_FORMULAIRE_DOCUMENTS_ARTICLE_stat($args, $filtres) {
	// Pas d'id_rubrique ? Erreur de squelette
	if (!$args[0] && !$args[1])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_AJOUTER_DOCUMENT_ARTICLE',
					'motif' => 'ARTICLES')), '');
	if ($args[3])
	    return (array($args[3],$args[4],$args[5]));
	else
	    return (array($args[0],$args[1],$args[2]));
}

function balise_FORMULAIRE_DOCUMENTS_ARTICLE_dyn($id_article,$id_rubrique,$statut_article) {
	if(lire_meta("documents_article")!='oui') 
		return '';
	if (!$GLOBALS["auteur_session"]) 
		return '';
//		return array('formulaires/formulaire_documents_article', 0,array('id_article' => $id_article));
	
	$auteur_statut=$GLOBALS["auteur_session"]["statut"];
	$id_auteur_session = $GLOBALS['auteur_session']['id_auteur'];
	
	$query = "SELECT * FROM spip_auteurs_articles WHERE id_article=".$id_article." AND id_auteur=".$id_auteur_session;
	$result_auteur = spip_query($query);
	$flag_auteur = (spip_num_rows($result_auteur) > 0);

	global $connect_toutes_rubriques,$connect_id_rubrique,$connect_id_auteur;
	$connect_id_auteur=$GLOBALS['auteur_session']['id_auteur'];
	include_spip("inc/auth");
	auth_rubrique();
	
	$flag_modif= (acces_restreint_rubrique($id_rubrique)
	OR ($flag_auteur AND ($statut_article == 'prepa' OR $statut_article == 'prop' OR $statut_article == 'poubelle')));
	
	if ($flag_modif){
		include_ecrire('inc_rubriques.php3');
		
		return array('formulaires/formulaire_portfolio_article', 0,
			array(
					'id_article' => $id_article,
					'show_docs' => safehtml($_REQUEST['show_docs']),
					'id_auteur_session' => $id_auteur_session,
					'redirect_url' => str_replace('&amp;','&',self())
			));
	}
	else
		return array('formulaires/formulaire_documents_article', 0,array('id_article' => $id_article));
	
}

?>
