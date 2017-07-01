<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_liens_ingredients_saisies_dist() {
	$saisies = array(
		array(
			'saisie' => 'ingredients',
			'options' => array(
				'nom' => 'ingredient',
				'label' => _T('ingredient:titre_ingredient'),
				'class' => 'chosen'
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'quantite',
				'label' => _T('ingredient:champ_titre_quantite')
			)
		)
	);

	return $saisies;
}

/*
 *	 Fonction de vérification, cela fonction avec un tableau d'erreur.
 *	 Le tableau est formater de la sorte:
 *	 if (!_request('NomErreur')) {
 *		 $erreurs['message_erreur'] = '';
 *		 $erreurs['NomErreur'] = '';
 *	 }
 *	 Pensez à utiliser _T('info_obligatoire'); pour les éléments obligatoire.
 */
function formulaires_liens_ingredients_verifier_dist() {
	$erreurs = array();

	return $erreurs;
}

function formulaires_liens_ingredients_traiter_dist() {
	//Traitement du formulaire.

	include_spip('action/editer_liens');

	objet_associer(
		array('id_ingredient' => _request('ingredient')),
		array('id_article' => _request('id_article')),
		array('quantite' => _request('quantite'))
	);

	// Donnée de retour.
	return array(
		'editable' => true,
		'message_ok' => _T('ingredient:message_ajoute_ok')
		.'<script type="text/javascript">$(function () {ajaxReload("liste-ingredient")})</script>'
	);
}
