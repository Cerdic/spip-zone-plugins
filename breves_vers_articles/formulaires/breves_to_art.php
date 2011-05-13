<?php
/*
 * Plugin Facteur
 * (c) 2009-2010 Collectif SPIP
 * Distribue sous licence GPL
 *
 */

include_spip('inc/breves2art');

function formulaires_breves_to_art_charger_dist(){

	$valeurs = array('id_test'=> BREVE_POUR_TEST,
		'rubrique_br'=> RUBRIQUE_DES_BREVES,
		'auteur_br'=> AUTEUR_DES_BREVES,
		'lance_conv'=>'',
		'modif_liens'=>''
		);

	return $valeurs;
}

function formulaires_breves_to_art_verifier_dist(){
	$erreurs = array();
	
	if(_request('lance_conv')) {
		// verifier que les champs obligatoires sont bien l� :
		foreach(array('rubrique_br') as $obligatoire)
			if(!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';
			
		// V�rification de l'existence de la rubrique
	    $rub = sql_countsel("spip_rubriques", "id_rubrique="._request('rubrique_br'));
		if($rub!=1) $erreurs['rubrique_br'] = 'Rubrique n�'._request('rubrique_br').' inexistante';

		if( (_request('lance_conv') != 'test') && (_request('lance_conv') != 'toutes') )
			$erreurs['lance_conv'] = 'Param�tre incorrect ... : '._request('lance_conv');

		if(_request('lance_conv') == 'test' && !_request('id_test')) $erreurs['id_test'] = 'Ce champ est obligatoire pour le test';
		
		// V�rification de l'existence de l'auteur (si renseign�)
	    if(_request('auteur_br')) {
			$auteur = sql_countsel("spip_auteurs", "id_auteur="._request('auteur_br'));
			if($auteur!=1) $erreurs['auteur_br'] = 'Auteur n�'._request('auteur_br').' inexistant';
		}
	}
	
	if(count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		
	if(!_request('lance_conv') && !_request('modif_liens'))
		$erreurs['message_erreur'] = 'Pas d\'action demand�e !';

	return $erreurs;
}

function formulaires_breves_to_art_traiter_dist(){	
	$msg = "";
	if(_request('lance_conv') == 'toutes') {
		if($les_breves = sql_select('id_breve', 'spip_breves')) {
			while($une_breve = sql_fetch($les_breves)) {
				breve2art($une_breve['id_breve'], _request('rubrique_br'), _request('auteur_br'));
			}
		}
		else
			echo 'Erreur sur sql_select dans boucle sur les breves'.sql_error();
		
		if(sql_count($les_breves)<1)
			$msg = "Aucunes br�ves trouv�es";
		else
			$msg = "Conversion des br�ves termin�es";
	}
	else if(_request('lance_conv') == 'test') {
	    // Test sur une breve
	    $msg = "Conversion de test sur la br�ve "._request('id_test')."<br>";
	    breve2art(_request('id_test'), _request('rubrique_br'), _request('auteur_br'));
	}

	if(_request('modif_liens')) {
		modif_liens();
		$msg .= "<br>Modification des liens vers les breves";
	}

	if(!_request('modif_liens') && !_request('lance_conv'))
		$msg = "Rien � faire ...";

	return array('message_ok'=>$msg);
}
?>
