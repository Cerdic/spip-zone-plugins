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
 */
function formulaires_choix_secteur_saisies(){

	$mes_saisies = array(
		array(
			'saisie' => 'secteur',
			'options' => array(
				'nom' => 'secteur',
				'option_intro' => _T('info_tout_site'),
				'label' => _T('bilancontrib:label_id_secteur'),
				'explication' => _T('bilancontrib:explication_id_secteur')
			)
		)
	);
	return $mes_saisies;
}

/**
 * Fonction de chargement des valeurs par defaut des champs du formulaire
 *
 */
function formulaires_choix_secteur_charger_dist(){
	$valeurs = array();
	$valeurs['editable'] = true; 
	$valeurs['secteur'] = _request('secteur');
	return $valeurs;
}
/**
 * Fonction de traitement du formulaire
 *
 */
function formulaires_choix_secteur_traiter_dist(){
   $message = array();
	$message['editable'] = true;
	$message['redirect'] = parametre_url(self(),'secteur',_request('secteur'));

   return $message;

}
?>
