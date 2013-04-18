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

function formulaires_ajouter_membres_groupe_charger_dist($id_groupe='') {
	$contexte['id_groupe'] = $id_groupe;
	$contexte['_action'] = array("ajouter_membres_groupe", $id_groupe); // pour passer securiser action
	return $contexte;
}

function formulaires_ajouter_membres_groupe_traiter($id_groupe='') {
	// partie de code grandement inspiree du code de formulaires_editer_objet_traiter dans ecrire/inc/editer.php
	$res = array();
	// eviter la redirection forcee par l'action...
	set_request('redirect');
	$action_ajouter_membres = charger_fonction('ajouter_membres_groupe','action');
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