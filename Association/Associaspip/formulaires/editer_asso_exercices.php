<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_asso_exercices_charger_dist($id_exercice='') {
	$contexte = formulaires_editer_objet_charger('asso_exercices', $id_exercice, '', '',  generer_url_ecrire('exercices'), '');

	return $contexte;
}

function formulaires_editer_asso_exercices_verifier_dist($id_exercice) {
	$erreurs = array();

	if ($erreur = association_verifier_date('date_debut') )
		$erreurs['date_debut'] = $erreur;
	if ($erreur = association_verifier_date('date_fin') )
		$erreurs['date_fin'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_exercices_traiter_dist($id_exercice) {
	return formulaires_editer_objet_traiter('asso_exercices', $id_exercice, '', '',  generer_url_ecrire('exercices'), '');
}

?>