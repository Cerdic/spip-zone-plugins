<?php

include_spip('breves_vers_articles_fonctions');

function formulaires_breves_vers_article_charger_dist(){
	$valeurs = array('id_test'=> BREVE_POUR_TEST,
		'rubrique_br'=> RUBRIQUE_DES_BREVES,
		'auteur_br'=> AUTEUR_DES_BREVES,
		'lance_conv'=>'',
		'modif_liens'=>'',
		'comment_rub' => 'unique',
		'statut_br' => 'idem'
		);
		

	return $valeurs;
}

function formulaires_breves_vers_article_verifier_dist(){
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

		if(_request('lance_conv') == 'test' && !_request('id_test'))
			$erreurs['id_test'] = 'Ce champ est obligatoire pour le test';
		
		// Vérification de l'existence de l'auteur (si renseigné)
	    if(_request('auteur_br')) {
			$auteur = sql_countsel("spip_auteurs", "id_auteur="._request('auteur_br'));
			if($auteur!=1) $erreurs['auteur_br'] = 'Auteur n°'._request('auteur_br').' inexistant';
		}
	}
	
	if(count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		
	if(!_request('lance_conv') && !_request('modif_liens'))
		$erreurs['message_erreur'] = 'Pas d\'action demandée !';

	return $erreurs;
}

function formulaires_breves_vers_article_traiter_dist(){	
	$msg = "";
	
	if(_request('comment_rub') == 'unique') {
		$choix_rub = _request('rubrique_br');
	} else {
		$choix_rub = false;
	}
	
	if(_request('lance_conv') == 'toutes') {
		if($les_breves = sql_select('id_breve', 'spip_breves')) {
			while($une_breve = sql_fetch($les_breves)) {
				$msg .= breves_vers_articles($une_breve['id_breve'], $choix_rub, _request('auteur_br'), _request('statut_br'));
				spip_log('breves_vers_article : conversion sur breve n'.$une_breve['id_breve']);
			}
		}
		else
			$msg = '<br>Erreur sur sql_select dans boucle sur les breves'.sql_error().'<br>';
		
		if(sql_count($les_breves)<1)
			$msg .= "Aucunes bréves trouvées";
		else
			$msg .= "Conversion des bréves terminées";
		
		spip_log('breves_vers_article : conversion des breves finies');
	}
	else if(_request('lance_conv') == 'test') {
	    // Test sur une breve
	    $msg = "Conversion de test sur la brève "._request('id_test')."<br>";
	    $msg .= breves_vers_articles(_request('id_test'), $choix_rub, _request('auteur_br'), _request('statut_br'));
	}

	if(_request('modif_liens')) {
		modif_liens();
		$msg .= "<br>Modification des liens vers les breves";
	}

//	if(!_request('modif_liens') && !_request('lance_conv'))
//		$msg = "Rien à faire ...";

	spip_log('breves_vers_article : fin traitement');
	return array('message_ok'=>$msg);
}

?>
