<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_saisies_cvt_saisies_dist() {
	include_spip('inc/saisies');
	
	$saisies = array(
		'options' => array(
			'texte_submit' => 'Pouet !',
			'etapes_activer' => true,
			'etapes_suivant' => 'Suivant pouet',
			'etapes_precedent' => 'Précédent pouet',
			'etapes_navigation' => 'on',
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'persos',
				'label' => 'Informations personnelles',
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'nom',
						'label' => 'Nom'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'email',
						'obligatoire' => 'oui',
						'label' => 'E-mail'
					),
					'verifier' => array(
						'type' => 'email'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'a_supprimer',
						'label' => 'Un champ à supprimer'
					)
				),
			),
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'out',
				'label_case' => 'Un champ à l’extérieur des groupes'
			),
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'ecrire',
				'label' => 'Des choses à dire',
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'sujet',
						'label' => 'Sujet'
					)
				),
				array(
					'saisie' => 'textarea',
					'options' => array(
						'nom' => 'message',
						'obligatoire' => 'oui',
						'label' => 'Un message',
						'conteneur_class' => 'pleine_largeur',
					),
					'verifier' => array(
						'type' => 'taille',
						'options' => array('min' => 10)
					)
				),
			),
		),
	);
	
	$chemin = saisies_chercher($saisies, 'a_supprimer', true);
	$saisies = saisies_supprimer($saisies, $chemin);
	$saisies = saisies_dupliquer($saisies, 'message');
	$saisies = saisies_deplacer($saisies, 'email', 'nom');
	//var_dump($saisies);
	
	return $saisies;
}

function formulaires_saisies_cvt_charger() {
	$contexte = array(
		'saisies_texte_submit' => 'Prout !',
	);
	
	return $contexte;
}
