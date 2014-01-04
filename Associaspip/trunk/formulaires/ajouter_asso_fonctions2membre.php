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

function formulaires_ajouter_asso_fonctions2membre_charger_dist($id_auteur='') {
	$contexte['id_auteur'] = $id_auteur;
	$contexte['_action'] = array('ajouter_asso_fonction2membre', $id_auteur); // pour passer securiser action
	return $contexte;
}

function formulaires_ajouter_asso_fonctions2membre_traiter($id_auteur='') {
	$res = array();
	set_request('redirect'); // eviter la redirection forcee par l'action...
	$action_ajouter_membres = charger_fonction('ajouter_membre_groupes','action');
	$action_ajouter_membres($id_auteur);
	$res['message_ok'] = '';
	$res['redirect'] = generer_url_ecrire('edit_adherent', 'id='.$id_auteur);
	return $res;
}

?>