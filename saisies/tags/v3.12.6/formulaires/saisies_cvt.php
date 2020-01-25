<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_saisies_cvt_saisies_dist() {
	include_spip('inc/saisies');
	
	$saisies = array(
		'options' => array(
			'texte_submit' => 'Prout !',
			'activer_etapes' => true,
		),
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
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'message',
				'obligatoire' => 'oui',
				'label' => 'Un message'
			),
			'verifier' => array(
				'type' => 'taille',
				'options' => array('min' => 10)
			)
		)
	);
	
	$chemin = saisies_chercher($saisies, 'a_supprimer', true);
	$saisies = saisies_supprimer($saisies, $chemin);
	$saisies = saisies_dupliquer($saisies, 'message');
	$saisies = saisies_deplacer($saisies, 'email', 'nom');
	var_dump($saisies);
	
	return $saisies;
}

function formulaires_saisies_cvt_charger() {
	$contexte = array(
		'saisies_texte_submit' => 'Caca !',
	);
	
	return $contexte;
}
