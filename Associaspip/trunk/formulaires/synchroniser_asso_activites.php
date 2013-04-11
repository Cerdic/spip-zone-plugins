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

function formulaires_synchroniser_asso_activites_charger_dist($id_evenement=0) {
	$contexte['id_evenement'] = $id_evenement; // passer le parametre par formulaire
	$contexte['_hidden'] .= "<input type='hidden' name='id_evenement' value='$id_evenement' />"; // passer le parametre par formulaire
	$contexte['_action'] = array('synchroniser_asso_activites',''); // pour passer securiser action

	return $contexte;
}

function formulaires_synchroniser_asso_activites_verifier_dist($id_evenement=0) {
	$erreurs = array();

	// pas de verification non plus

	return $erreurs;
}

function formulaires_synchroniser_asso_activites_traiter_dist($id_evenement=0) {
	$res = array();
	$synchro = charger_fonction('synchroniser_asso_activites','action');
	$nb_insertion = $synchro(); // la fonction action retourne le nombre d'insertion realisees
	if ($nb_insertion>1) {
		$nb_insertion .= _T('asso:membres_ajoutes');
	} else {
		$nb_insertion .= _T('asso:membre_ajoute');
	}
	$res['message_ok'] = $nb_insertion;

	return $res;
}

?>