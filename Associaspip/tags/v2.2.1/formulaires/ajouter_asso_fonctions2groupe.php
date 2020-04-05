<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;
include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_ajouter_asso_fonctions2groupe_charger_dist($id_groupe=0) {
	$contexte['id_groupe'] = $id_groupe; // passer l'argument dans l'environnement
	$contexte['_action'] = array('editer_asso_fonctions', $id_groupe); // pour passer securiser action
	return $contexte;
}

function formulaires_ajouter_asso_fonctions2groupe_traiter($id_groupe=0) {
	$res = array();
	set_request('redirect'); // eviter la redirection forcee par l'action...
	$action_ajouter_membres = charger_fonction('editer_asso_fonctions', 'action');
	$res['message_erreur'] = $action_ajouter_membres($id_groupe);
	$res['message_ok'] = '';
	$id_groupe = intval($id_groupe);
	$res['redirect'] = generer_url_ecrire((($id_groupe>0 && $id_groupe<100)?'edit_groupe_autorisations':'edit_groupe'), 'id='.$id_groupe);
	return $res;
}

?>