<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de generation des saisies du formulaire
 *
 * @param string $liste liste sur laquelle intervenir
 */
function formulaires_ajouter_abonne_saisies($liste=0){
	$mes_saisies = array(
		array(
			'saisie' => 'hidden',
			'options' => array(
				'nom' => 'liste',
				'defaut' => $liste
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'email',
				'obligatoire' => 'oui',
				'defaut' => '',
				'valeur' => '',
				'size' => 20,
				'label' => _T('gestionml:label_ajouter_mail')
			),
			'verifier' => array(
				'type' => 'email'
			)
		)
	);
	return $mes_saisies;
}

/**
 * Fonction de chargement du formulaire
 *
 * @param string $liste liste sur laquelle intervenir
 */
function formulaires_ajouter_abonne_charger_dist($liste){
	$valeurs = array('email'=>'','liste'=>$liste);
   return $valeurs;
}


/**
 * Fonction de traitement du formulaire
 *
 */
function formulaires_ajouter_abonne_traiter_dist(){
	include_spip('inc/gestionml_api');
	return gestionml_api_ajouter_email(_request('liste'),_request('email')) ;
}


?>
