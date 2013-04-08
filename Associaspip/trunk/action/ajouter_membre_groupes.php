<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
    return;

function action_ajouter_membre_groupes() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$id_groupes = association_recuperer_liste('id_groupes', TRUE);

	$insert_data = array();
	foreach ($id_groupes as $id_groupe) {
		$insert_data[] = array('id_groupe' => $id_groupe, 'id_auteur' => $id_auteur);
	}
	if (count($insert_data)) {
		sql_insertq_multi('spip_asso_fonctions', $insert_data);
	}

	return;
}

?>