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

function formulaires_editer_asso_destination_charger_dist($id_destination=0) {
//	$contexte = formulaires_editer_objet_charger('asso_destination', $id_destination, '', '',  generer_url_ecrire('destination'), ''); // ne fonctionne pas ...parce-que la table n'est pas au pluriel ! (va savoir pourquoi)
	$contexte = sql_fetsel('*', 'spip_asso_destination', "id_destination='$id_destination' ");
	$contexte['_action'] = array('editer_asso_destinations', $id_destination);
	$contexte['retour'] = generer_url_ecrire('destination');

	return $contexte;
}

function formulaires_editer_asso_destination_verifier_dist($id_destination=9) {
	$erreurs = array();

	// formulaire tres simple : rien de particulier a verifier

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_destination_traiter_dist($id_destination=0) {
	return formulaires_editer_objet_traiter('asso_destination', $id_destination, '', '',  generer_url_ecrire('destination'), '');
}

?>