<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_ajouter_paragraphe_charger($titre,$id_rubrique='',$statut='',$id_auteur='') {
	
	// Construction du tableau $valeurs
	$valeurs = array(
		'titre' 		=> '',
		'id_rubrique' 	=> '',
		'statut' 		=> '',
		'id_auteur'		=> ''
	);
	
	return $valeurs;
}

function formulaires_ajouter_paragraphe_verifier() {

	$erreurs = array();
	// verifier que au moins les champs titre et id_rubrique soient bien la :
	foreach(array('id_rubrique','titre') as $obligatoire)
		if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';

	// s'il y a des erreurs...
	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		
	return $erreurs;
}

function formulaires_ajouter_paragraphe_traiter() {
	
	$res = array();

	// si le statut n'est pas spécifié, statut publié
	if (!$valeurs['statut'] || $valeurs['statut']=='' )
		$valeurs['statut'] = 'publie';
	
	// si l'auteur n'est pas spécifié, on prend l'id du visiteur
	if ($GLOBALS['visiteur_session']['id_auteur']) 
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];

	$id_rubrique = _request('id_rubrique');
	$titre = _request('titre');
	$statut = _request('statut');
	$date = date('Y-m-d h:i:s');

	// on ajoute l'article
	if ($id_article = sql_insertq("spip_articles", array(
		"id_rubrique" => $id_rubrique,
		"statut" => $statut,
		"titre" => $titre,
		"date" => $date)
	)) $res['message_ok'] = "Enregistrement article N&deg;".$id_article." r&eacute;ussi !";

	// on ajoute l'auteur en tant qu'auteur de l'article
	if ($id_auteur) {
		if (sql_insertq("spip_auteurs_articles", array(
			"id_auteur" => $id_auteur,
			"id_article" => $id_article)
		)) $res['message_ok'] .= "<br />Enregistrement auteur r&eacute;ussi !";
	}
	
	// et puis on s'en va guillerets...
	return $res;
	
}

?>