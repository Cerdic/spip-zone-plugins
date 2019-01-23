<?php
/***************************************************************************\
 * Plugin Nouvelle Version pour Spip 3.0
 * Licence GPL (c) 2011
 * Modération de la nouvelle version d'un article
 *
\***************************************************************************/

function formulaires_publiversion_article_confirm_charger_dist(){
	$valeurs = array();
	
	return $valeurs;
}

function formulaires_publiversion_article_confirm_verifier_dist($article,$article_orig){
	$erreurs = array();

	if (!$article || !$article_orig)
		$erreurs['message_erreur'] = 'Une erreur est survenue.';
		
	return $erreurs;
}

function formulaires_publiversion_article_confirm_traiter_dist($article,$article_orig,$newstatut='poubelle'){
	
	if(_request('confirmer')){
		include_spip('action/remplacer');
		/** AJOUT 20 JANVIER 2019 POUR OPERER LE SWITCH DES MOTS-CLEFS ET DOCUMENTS **/
		include_spip('action/dupliquer');
		/*****/

spip_log("ID ARTICLE CIBLE : $article");
spip_log("ID ARTICLE VERSION : $article_orig");

		/** AJOUT 20 JANVIER 2019 POUR OPERER LE SWITCH DES MOTS-CLEFS ET DOCUMENTS **/
		$mots_clefs_orig = lire_les_mots_clefs(intval($article_orig),'article');
		$mots_clefs_newversion = lire_les_mots_clefs(intval($article),'article');
		
		$documents_orig=lire_les_documents(intval($article_orig),'article');
		$documents_newversion=lire_les_documents(intval($article),'article');
		
		remettre_les_mots_clefs($mots_clefs_orig,intval($article),'article');
		remettre_les_mots_clefs($mots_clefs_newversion,intval($article_orig),'article');
		
		remettre_les_documents($documents_orig,intval($article),'article');
		remettre_les_documents($documents_newversion,intval($article_orig),'article');
		/*******/
		
		$remplacer_article = remplacer_article(intval($article),intval($article_orig),$newstatut);

		$message = array('message_ok'=>array(
		'message'=>_T('versioning:operation_executee'),
		'cible'=>$article_orig,
		'type_retour'=>_T('versioning:operation_retour_ok_article_publi')
		));			
	}
	if(_request('annuler')){
		$message = array('message_ok'=>array(
		'message'=>_T('versioning:operation_annulee'),
		'cible'=>$article,
		'type_retour'=>_T('versioning:operation_retour_ko_article')
		));			
	}

	return $message;
}
