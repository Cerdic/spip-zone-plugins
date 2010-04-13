<?php

/**
 * Plugin Quiz pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

function formulaires_ajouter_question_charger_dist() {
	$valeurs = array(
		'id_article'=>''
	);

	return $valeurs;
}


function formulaires_ajouter_question_verifier_dist() {
	$erreurs = array();
	
	// on vérifie juste la présence d'un id_article
	if (!_request('id_article'))
			$erreurs['id_article'] = _T('quiz:erreur_article_manquant');
	
	if (count($erreurs))
		$erreurs['message_erreur'] = _T('quiz:question_ajoutee_erreur');
	
	return $erreurs;
}

function formulaires_ajouter_question_traiter_dist() {
	if ( sql_insertq('spip_questions', array('id_article'=>_request('id_article'))) )
		return array('message_ok'=> _T('quiz:question_ajoutee_ok'));
	else
		return array('message_erreur'=>_T('quiz:question_ajoutee_erreur'));

}




?> 