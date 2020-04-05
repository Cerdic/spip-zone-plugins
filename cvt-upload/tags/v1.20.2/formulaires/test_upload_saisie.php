<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/cvtupload');
include_spip('inc/saisies');
function formulaires_test_upload_saisies(){
	static $saisies;
	if (!$saisies == null) {
		return $saisies;
	}
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
			'saisie'=>'fichiers',
			'options'=>array(
				'nom'=>'pdfs',
				'label'=>'Plusieurs fichiers PDF dans un même champ',
				'nb_fichiers'=>2,
				'obligatoire'=>'oui'
			), 
			'verifier' => array(
				'type'=>'fichiers',
				'options'=>array(
					'mime'=>'specifique',
					'mime_specifique'=>array('application/pdf')
				)
			)
		),
		array(
			'saisie'=>'fichiers',
			'options'=>array(
				'nom'=>'fichier_tout_mime', 
				'label'=>'Un fichier, n\'importe quel type MIME accepté par SPIP',
				'nb_fichiers'=>1
			),
			'verifier' => array(
				'type'=>'fichiers',
				'options' => array('mime' => 'tout_mime')
			)
		),
		array(
			'saisie' => 'fichiers',
			'options' => array(
				'nom' => 'fichier_image_web',
				'label' => 'Un fichier de type image web (jpg, png, gif)',
				'nb_fichiers' => 1
			), 
			'verifier' => array(
				'type'=>'fichiers',
				'options' => array('mime' => 'image_web')
			)
		),
		array(
			'saisie' => 'fichiers',
			'options' => array(
				'nom' => 'fichier_leger',
				'label' => 'Un fichier léger (≤ 10 kio)',
				'nb_fichiers' => 1
			), 
			'verifier' => array(
				'type' => 'fichiers',
				'options' => array('taille_max' => 10)
			)
		),
		array(
			'saisie' => 'fichiers',
			'options' => array(
				'nom' => 'image_web_pas_trop_grande',
				'label' => 'Une image web pas plus grande que 1024 px de largeur et 640 px de hauteur',
				'nb_fichiers' => 1
			),
			'verifier'=>array(
				'type'=>'fichiers',
				'options' => array(
					'mime' => 'image_web', 
					'dimension_max' => array(
						'largeur' => 1024,
						'hauteur' => 640
					)
				)
			)
		),
		array(
			'saisie' => 'fichiers',
			'options' => array(
				'nom' => 'image_web_pas_trop_grande_rotation',
				'label' => 'Une image web pas plus grande que 1024 px de largeur et 640 px de hauteur, ou l\'inverse',
				'nb_fichiers' => 1
			),
			'verifier' => array(
				'type'=>'fichiers',
				'options' => array(
					'mime' => 'image_web', 
					'dimension_max' => array(
						'largeur' => 1024,
						'hauteur' => 640,
						'autoriser_rotation' => True
					)
				)
			)
		)
	);
	return $saisies;
}
function formulaires_test_upload_saisie_charger(){
	$contexte = array(
		'mes_saisies' => formulaires_test_upload_saisies()
	);

	return $contexte;
}

function formulaires_test_upload_saisie_fichiers(){
	return array_keys(saisies_lister_avec_type(formulaires_test_upload_saisies(), 'fichiers'));
}

function formulaires_test_upload_saisie_verifier(){
	$erreurs = array();

	if (_request('tromperie'))
		$erreurs['tromperie'] = 'Il ne fallait rien remplir.';

	// Vérifier les autres saisies (de type fichiers)
	$saisies = formulaires_test_upload_saisies();
	$erreurs_par_fichier = array(); 
	$saisies_verifier = saisies_verifier($saisies,true,$erreurs_par_fichier);
	foreach ($saisies_verifier as $champ => $erreur) { // nettoyer $_FILES des fichiers problématiques
		cvtupload_nettoyer_files_selon_erreurs($champ, $erreurs_par_fichier[$champ]);
	}

	// fusionner avec nos précedentes erreurs
	$erreurs = array_merge($erreurs,$saisies_verifier);

	return $erreurs;
}

function formulaires_test_upload_saisie_traiter(){
	$retours = array('message_ok' => 'Il ne se passe rien.');
	
	$fichiers = _request('_fichiers');
	var_dump($_FILES);
	var_dump($fichiers);
	
	return $retours;
}

