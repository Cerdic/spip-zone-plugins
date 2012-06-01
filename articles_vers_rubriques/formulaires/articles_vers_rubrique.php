<?php

include_spip('articles_vers_rubriques_fonctions');

function formulaires_articles_vers_rubrique_charger_dist(){
	$valeurs = array('_id_test'=> ARTICLE_POUR_TEST,
		'rubrique_br'=> RUBRIQUE_DES_ARTICLES,
		'auteur_admin'=> AUTEUR_DES_ARTICLES,
		'lance_conv'=>'',
		'modif_liens'=>'',
		'comment_rub' => 'origine',
		'statut_br' => 'prop'
		);
		

	return $valeurs;
}

function formulaires_articles_vers_rubrique_verifier_dist(){
	$erreurs = array();
	
	if(_request('lance_conv')) {
		// verifier que les champs obligatoires sont bien là :
		if(!_request('comment_rub'))
			$erreurs['comment_rub'] = 'Ce champ est obligatoire';
		if(!_request('statut_br'))
			$erreurs['statut_br'] = 'Ce champ est obligatoire';
		if(_request('comment_rub') == 'unique') {
			if(!_request('rubrique_br')) $erreurs['rubrique_br'] = 'Ce champ est obligatoire';

			// Vérification de l'existence de la rubrique
			$rub = sql_countsel("spip_rubriques", "id_rubrique="._request('rubrique_br'));
			if($rub!=1)
				$erreurs['rubrique_br'] = 'Rubrique n°'._request('rubrique_br').' inexistante';
		}

		if( (_request('lance_conv') != 'test') && (_request('lance_conv') != 'toutes') )
			$erreurs['lance_conv'] = 'Paramétre incorrect ... : '._request('lance_conv');

		if(_request('lance_conv') == 'test' && !_request('_id_test'))
			$erreurs['_id_test'] = 'Ce champ est obligatoire pour le test';

	}
	
	if(count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		
	if(!_request('lance_conv') && !_request('modif_liens'))
		$erreurs['message_erreur'] = 'Pas d\'action demandée !';

	return $erreurs;
}

function formulaires_articles_vers_rubrique_traiter_dist(){	
	$msg = "";
	//echo "Conversion de test  la article "._request('_id_test')."<br>";exit;
	if(_request('comment_rub') == 'unique') {
		$choix_rub = _request('rubrique_br');
	} else {
		$choix_rub = false;
	}
	if(_request('auteur_admin')) {
		$auteur_admin = true;
	} else {
		$auteur_admin = false;
	}
	if(_request('modif_liens')) {
		$modif_liens=true;
	} else {
		$modif_liens=false;
	}	
	
	    // conversion des articles
	    $msg = "Conversion sur les articles "._request('_id_test')."<br>";
		$msg .= articles_vers_rubriques(_request('_id_test'), $choix_rub, $auteur_admin, _request('statut_br'),$modif_liens);

//	if(!_request('modif_liens') && !_request('lance_conv'))
//		$msg = "Rien à faire ...";

	spip_log('articles_vers_rubrique : fin traitement');
	return array('message_ok'=>$msg);
}

?>
