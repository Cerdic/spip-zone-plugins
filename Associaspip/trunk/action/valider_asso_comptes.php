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

function action_valider_asso_comptes_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();
	$where = sql_in('id_compte', association_recuperer_liste('definitif', TRUE) );
	sql_updateq('spip_asso_comptes', array('vu' => 1), $where);
}

?>