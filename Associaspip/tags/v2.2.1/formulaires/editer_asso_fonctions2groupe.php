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

function formulaires_editer_asso_fonctions2groupe_charger_dist($id_groupe=0) {
	$contexte['id_groupe'] = $id_groupe; // passer l'argument dans l'environnement
	$contexte['_action'] = array('editer_asso_fonctions', $id_groupe); // pour passer securiser action

	return $contexte;
}

function formulaires_editer_asso_fonctions2groupe_traiter($id_groupe=0) {
	$res = array();
	set_request('redirect'); // eviter la redirection forcee par l'action...
	if(_request('modifier')) {
		$action_membres = charger_fonction('editer_asso_fonctions', 'action');
	} elseif (_request('exclure')) {
		$action_membres = charger_fonction('supprimer_asso_fonctions', 'action');
	}
	$res['message_erreur'] = $action_membres($id_groupe);
	$res['message_ok'] = '';
	$id_groupe = intval($id_groupe);
	$res['redirect'] = generer_url_ecrire((($id_groupe>0 && $id_groupe<100)?'edit_groupe_autorisations':'edit_groupe'), 'id='.$id_groupe);
	return $res;
}

?>