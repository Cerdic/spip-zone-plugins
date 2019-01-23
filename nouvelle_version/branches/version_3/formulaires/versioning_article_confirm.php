<?php
/***************************************************************************\
 * Plugin Nouvelle Version pour Spip 3.0
 * Licence GPL (c) 2011
 * Modération de la nouvelle version d'un article
 *
\***************************************************************************/

function formulaires_versioning_article_confirm_charger_dist(){
	$valeurs = array();
	
	return $valeurs;
}

function formulaires_versioning_article_confirm_verifier_dist($article){
	$erreurs = array();

	if (!$article)
		$erreurs['message_erreur'] = 'Une erreur est survenue.';
		
	return $erreurs;
}

function formulaires_versioning_article_confirm_traiter_dist($article){
	
	if(_request('confirmer')){
		include_spip('action/dupliquer');

		// On duplique l article
		$champs = array('id_rubrique');
		$where = array( 
		'id_article='.$article
		);
		$res = sql_select($champs, "spip_articles", $where);
		$r = sql_fetch($res);
		$rubrique = $r['id_rubrique'];
spip_log("ID RUBRIQUE : $rubrique");
spip_log("ID ARTICLE : $article");
		$nouvel_article = dupliquer_article(intval($article),intval($rubrique));

		$message = array('message_ok'=>array(
		'message'=>_T('versioning:operation_executee'),
		'cible'=>$nouvel_article,
		'type_retour'=>_T('versioning:operation_retour_ok_article')
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
