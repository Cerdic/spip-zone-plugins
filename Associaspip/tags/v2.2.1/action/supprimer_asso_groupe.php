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

function action_supprimer_asso_groupe_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_groupe= $securiser_action();

	sql_delete('spip_asso_fonctions', "id_groupe=$id_groupe"); // dereferencer d'abord tous les membres de ce groupe
	sql_delete('spip_asso_groupes', "id_groupe=$id_groupe"); // supprimer enfin le groupe
}

?>