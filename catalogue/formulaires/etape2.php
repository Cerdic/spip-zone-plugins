<?php
/**
 * Plugin catalogue pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

function formulaires_etape2_charger_dist(){

	$valeurs = array(
		'choix_formule'=>unserialize($env),
		'civilite' => '',
		'nom'=>'',
		'prenom' => '',
		'numero' => '',
		'voie' => '',
		'complement' => '',
		'code_postal' => '',
		'ville' => '',
		'pays' => '',
		'courriel' => '',
		'tel_bureau' => '',
		'tel_maison' => '',
		'tel_portable' => ''
	);
	return $valeurs;
}


function formulaires_etape2_verifier_dist(){
	
	$erreurs = array();
	
	// lister les champs obligatoires
	$champs_obligatoires = array(
		// nouveaux champs
		'nom'=>'',
		/*
		'prenom' => '',
		'courriel' => '',
		'voie' => '',
		'code_postal' => '',
		'ville' => '',
		'pays' => '',
		*/
	);
	
	// verifier la presence de tous les champs obligatoires
	foreach($champs_obligatoires as $obligatoire => $valeur){
		if (!_request($obligatoire)) $erreurs[$obligatoire] = '*Ce champ est obligatoire';
	}

	/**
	 * Verifier que le courriel saisi n'est pas deja dans la base.
	 * Le seul moyen de d�oublonnage est sur le champ email de la table spip_auteurs
	 * comparer ce courrier � tous les champs de spip_auteurs
	 * si on en trouve un qui correspond
	 * on indique � l'utilisateur "cet email est deja dans notre base, etes vous #NOM ?"
	 * si oui, on lui demande de se loguer et de v�rifier ses coordonn�es
	 * s'il ne peut pas se loguer (oubli du pass) on lui propose de changer de pass
	 * si non on l'invite � choisir un nouveau courriel
	 */
	
	include_spip('inc/validations');
	if (_request('courriel') AND !verif_email_apsulis(_request('courriel')))
		$erreurs['courriel'] = 'Cet email n\'est pas valide';	
	
	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		
	return $erreurs;
}

function formulaires_etape2_traiter_dist(){
	/**
	 * V�rifier que la personne n'est pas d�ja logu�e
	 */
	$message_ok = "<p>Merci pour ces informations; vous allez maintenant saisir vos pr�f�rences.</p>";
	return array('message_ok'=>$message_ok);
}


?>