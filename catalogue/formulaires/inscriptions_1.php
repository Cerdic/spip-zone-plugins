<?php
/**
 * Formulaire d'Inscription Ardesi
 * Apsulis (http://demo.apsulis.com) - XDjuj
 * Septembre 2009
 *
 */

function formulaires_inscriptions_1_charger_dist(){
	$valeurs = array(
		'civilite'=>'',
		'nom'=>'',
		'prenom'=>'',
		'fonction'=>'',
		'organisme'=>'',
		'adresse1'=>'',
		'adresse2'=>'',
		'cp'=>'',
		'ville'=>'',
		'telephone'=>'',
		'telephone_portable'=>'',
		'email'=>'',
		'site_internet'=>''
	);
	
	return $valeurs;
}


function formulaires_inscriptions_1_verifier_dist(){
	include_spip('inc/validations');
	$erreurs = array();
	
	/* VERIF SUR LES CHAMPS OBLIGATOIRES */
	$champs_obligatoires = array(
		'civilite'=>'',
		'nom'=>'',
		'prenom'=>'',
		'fonction'=>'',
		'organisme'=>'',
		'adresse1'=>'',
		'cp'=>'',
		'ville'=>'',
		'telephone'=>'',
		'email'=>''
	);
	foreach($champs_obligatoires as $obligatoire => $valeur){
		if (!_request($obligatoire)) $erreurs[$obligatoire] = '*Ce champ est obligatoire';
	}

	/* AUTRES VERIFS SUR CHAMPS OBLIGATOIRES*/
	// Verifier que l'adresse est valide et non déjà utilisée
	$email = _request('email');
	if(!$erreurs['email'] && !verif_email_apsulis($email)) $erreurs['email_nonvalide'] = '*Email non valide';

	// Vérifier le code postal
	// $cp = _request('cp');
	// if(!$erreurs['cp'] && !verif_cp($cp)) $erreurs['cp_nonvalide'] = '*Code postal non valide (Ex. : 75013)';

	// Vérifier la ville
	$ville = _request('ville');
	if(!$erreurs['ville'] && !verif_ville($ville)) $erreurs['ville_nonvalide'] = '*Une ville ne comporte pas de chiffres...';

	// Vérifier le téléphone et le téléphone pro
	$tel = ereg_replace("\.|/|-| ",'',_request('telephone'));
	$telp = ereg_replace("\.|/|-| ",'',_request('telephone_portable'));
	if(!$erreurs['telephone'] && !verif_tel($tel)) $erreurs['tel_nonvalide'] = '*Téléphone non valide (10 chiffres)';
	if(_request('telephone_portable') && !verif_tel($telp)) $erreurs['telport_nonvalide'] = '*Téléphone non valide (10 chiffres)';

	// Vérifier le nom et le prenom
	$nom = _request('nom');
	$prenom = _request('prenom');
	if(!$erreurs['nom'] && !verif_nom($nom)) $erreurs['nom_nonvalide'] = '*Les chiffres ne sont pas acceptés.';
	if(!$erreurs['prenom'] && !verif_nom($prenom)) $erreurs['prenom_nonvalide'] = '*Les chiffres ne sont pas acceptés.';

	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		
	return $erreurs;
}

function formulaires_inscriptions_1_traiter_dist(){	
	$message_ok = "<p>Partie 1 ok, on passe à la deux</p>";
	return array('message_ok'=>$message_ok);
}

?>