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
				'nom'=>'plusieurs',
				'label'=>'Plusieurs fichiers dans un même champ',
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
	return array('plusieurs');
}

function formulaires_test_upload_saisie_verifier(){
	$erreurs = array();
	
	if (_request('tromperie'))
		$erreurs['tromperie'] = 'Il ne fallait rien remplir.';

	// options pour vérifier les images
	// si les options ne sont pas renseignées, la vérification se base sur
	// _IMG_MAX_SIZE, _IMG_MAX_WIDTH, _IMG_MAX_HEIGHT
	$verifier = charger_fonction('verifier', 'inc', true);
	$options = array(
		'taille_max' => 250, // en Ko
		'largeur_max' => 800, // en px
		'hauteur_max' => 600, // en px
	);

	// vérifier le champ image unique
	if ($erreur = $verifier($_FILES['image'], 'image_upload', $options)) {
		// renvoyer l'erreur dans le formulaire
		$erreurs['image'] = $erreur;
		// supprimer le fichier en erreur dans _FILES
		unset($_FILES['image']);
	}

	// vérifier le champ images multiples
	$erreurs_fichiers = array();
	if ($erreur = $verifier($_FILES['plusieurs_images'], 'image_upload_multiple', $options, $erreurs_fichiers)) {
		// renvoyer l'erreur dans le formulaire
		$erreurs['plusieurs_images'] = $erreur;
		// supprimer les fichiers en erreur dans _FILES
		foreach ($erreurs_fichiers as $id_file => $erreur) {
			foreach ($_FILES['plusieurs_images'] as $key => $val) {
				unset($_FILES['plusieurs_images'][$key][$id_file]);
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
