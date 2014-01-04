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

function formulaires_ajouter_asso_fonctions2groupe_charger_dist($id_groupe='') {
	$contexte['id_groupe'] = $id_groupe;
	$contexte['_action'] = array('ajouter_asso_fonction2groupe', $id_groupe); // pour passer securiser action
	return $contexte;
}

function formulaires_ajouter_asso_fonctions2groupe_traiter($id_groupe='') {
	$res = array();
	set_request('redirect'); // eviter la redirection forcee par l'action...
	$action_ajouter_membres = charger_fonction('ajouter_fonctions2groupe','action');
	$action_ajouter_membres($id_groupe);
	$res['message_ok'] = '';
	$id_groupe = intval($id_groupe);
	if ($id_groupe>0 && $id_groupe<100) {
		$res['redirect'] = generer_url_ecrire('edit_groupe_autorisations', 'id='.$id_groupe);
	} else {
		$res['redirect'] = generer_url_ecrire('edit_groupe', 'id='.$id_groupe);
	}
	return $res;
}

?>