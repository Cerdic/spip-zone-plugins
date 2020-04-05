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
 * @param array $users tableau des abonnes a la liste
 */
function formulaires_supprimer_abonnes_saisies($liste=0, $users=''){
	$mes_saisies = array(
		array(
			'saisie' => 'hidden',
			'options' => array(
				'nom' => 'liste',
				'defaut' => $liste
			)
		),
		array(
			'saisie' => 'checkbox',
			'options' => array(
				'nom' => 'email',
				'obligatoire' => 'oui',
				'defaut' => '',
				'label' => _T('gestionml:label_supprimer_mails'),
				'datas' => array_combine($users,$users)
			)
		)
	);
	return $mes_saisies;
}

/**
 * Fonction de traitement du formulaire
 *
 */
function formulaires_supprimer_abonnes_traiter_dist(){
	include_spip('inc/gestionml_api');
	return gestionml_api_supprimer_emails(_request('liste'),_request('email')) ;
}

?>
