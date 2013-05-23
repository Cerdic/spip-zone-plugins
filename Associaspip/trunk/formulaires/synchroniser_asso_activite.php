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
include_spip('inc/autoriser');

function formulaires_synchroniser_asso_activite_charger_dist($id_evenement=0) {
	$contexte['id_evenement'] = $id_evenement; // passer l'argument dans l'environnement
	$contexte['_hidden'] .= "<input type='hidden' name='id_evenement' value='$id_evenement' />"; // transmettre le parametre
	$contexte['_action'] = array('synchroniser_asso_activite', ''); // pour passer securiser action
	if ( !_request('dir') ) // pas de direction selectionnee :
		$contexte['dir'] = 'imp'; // preselectionner l'import.

	return $contexte;
}

function formulaires_synchroniser_asso_activite_verifier_dist($id_evenement=0) {
	$erreurs = array();

	// pas de verification non plus

	return $erreurs;
}

function formulaires_synchroniser_asso_activite_traiter_dist($id_evenement=0) {
	$res = array();
	$synchro = charger_fonction('synchroniser_asso_activite','action');
	$nb_insertion = $synchro(); // la fonction action retourne le nombre d'insertion realisees
	if ($nb_insertion>1) {
		$res['message_ok'] = _T('asso:membres_ajoutes');
	} else {
		$res['message_ok'] = _T('asso:membre_ajoute');
	}
//	$res['message_ok'] = _T('spip:info_fini');

	return $res;
}

?>