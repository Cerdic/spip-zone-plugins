<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/cvtupload');

function formulaires_test_upload_saisie_charger(){
	$saisies = array(
		array(
			'saisie'=>'input',
			'options'=>array(
				'nom'=>'tromperie',
				'label'=>'Si c\'est rempli, on se trompe',
				'defaut'=>_request('tromperie')
			)
		),
		array(
			'saisie'=>'Fichiers',
			'options'=>array(
				'nom'=>'pdfs',
				'label'=>'Plusieurs fichiers PDF dans un même champ',
				'nb_fichiers'=>2
			)
		)
	);
	$contexte = array(
		'mes_saisies' => $saisies
	);
	
	return $contexte;
}

function formulaires_test_upload_saisie_fichiers(){
	return array('pdfs');
}

function formulaires_test_upload_saisie_verifier(){
	$erreurs = array();
	
	if (_request('tromperie'))
		$erreurs['tromperie'] = 'Il ne fallait rien remplir.';
	
	$verifier = charger_fonction('verifier', 'inc', true);
	
	// Vérifier que la saisie PDFs ne contient que des PDF
	$options = array(
		'mime'=>'specifique',
		'mime_specifique'=>array('application/pdf')
	);
	$erreurs_par_fichier = array();
	if ($erreur = $verifier($_FILES['pdfs'], 'fichiers', $options,$erreurs_par_fichier)){
		$erreurs['pdfs'] = $erreur;
		cvtupload_nettoyer_files_selon_erreurs('pdfs',$erreurs_par_fichier);
	}	
	return $erreurs;
}

function formulaires_test_upload_saisie_traiter(){
	$retours = array('message_ok' => 'Il ne se passe rien.');
	
	$fichiers = _request('_fichiers');
	var_dump($_FILES);
	var_dump($fichiers);
	
	return $retours;
}

?>
