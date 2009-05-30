<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_annee_charger_dist($type, $id_objet){
	
	$valeurs = array(
		'annee'=>date('Y'),
		'id_objet'=>$id_objet,
		'type'=>$type,
		'editable'=>true // forcer l'etat editable du formulaire
	);
	return $valeurs; // retourner simplement les valeurs
}

function formulaires_annee_verifier_dist(){
	$erreurs = array();
	if (!_request('annee'))
		$erreurs['annee'] = 'Ce champ est obligatoire';
	if (_request('annee')<1900 OR _request('annee')>2100)
		$erreurs['annee'] = 'Soyez raisonables...';
	if (count($erreurs))
		$erreurs['message_erreur'] = 'Veuillez recommencer !';
	return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_annee_traiter_dist(){
	// rien a faire en bdd pour ce formulaire
	$message = 'merci';
	//return $message; // retourner simplement un message, le formulaire ne sera pas resoumis
	return array(true,$message); // forcer l'etat editable du formulaire et retourner le message
}

?>
