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

function action_editer_membre_groupes() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$fonctions = association_recuperer_liste('fonctions', TRUE);

	foreach ($fonctions as $id_groupe => $fonction) {
		sql_updateq('spip_asso_fonctions', array(
			'fonction' => $fonction),
		"id_groupe=$id_groupe AND id_auteur=$id_auteur");
	}

	return;
}

?>