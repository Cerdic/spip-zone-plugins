<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


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
	
	// Vérifier que la saisie PDFs ne contient que des PDF
	$verifier = charger_fonction('verifier', 'inc', true);
	$options = array(
		'mime'=>'specifique',
		'mime_specifique'=>array('application/pdf')
	);
	$erreurs_par_fichier = array();
	$erreur = $verifier($_FILES['pdfs'], 'fichiers', $options,$erreurs_par_fichier);
	if ($erreur!=''){
		$erreurs['pdfs'] = $erreur;
		foreach ($erreurs_par_fichier as $cle => $valeur){
			foreach ($_FILES['pdfs'] as $propriete => $valeur_propriete){
				unset($_FILES['pdfs'][$propriete][$cle]);//effacer le fichier problématique dans $_FILES
			}
		}
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
