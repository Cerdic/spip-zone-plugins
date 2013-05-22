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

function formulaires_synchroniser_asso_groupe_charger_dist($id_groupe=0) {
	$contexte['id_groupe'] = $id_groupe; // passer le parametre par formulaire
	$contexte['_hidden'] .= "<input type='hidden' name='id_groupe' value='$id_groupe' />"; // passer le parametre par formulaire
	$contexte['_action'] = array('synchroniser_asso_groupe',''); // pour passer securiser action

	return $contexte;
}

function formulaires_synchroniser_asso_groupe_verifier_dist($id_groupe=0) {
	$erreurs = array();

	if ( !in_array(_request('dir'), array('imp','exp')) )
		$erreurs['dir'] = _T('perso:choix_invalide');

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_synchroniser_asso_groupe_traiter_dist($id_groupe=0) {
	$res = array();
	$synchro = charger_fonction('synchroniser_asso_groupes','action');
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