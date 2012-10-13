<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_desactiver_adherents() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();
	$statut_courant = association_passeparam_statut('courant');
	$where = sql_in('id_auteur', association_recuperer_liste('id_auteurs', TRUE) );
	if($statut_courant==='sorti') {
		sql_updateq('spip_asso_membres', array("statut_interne" => 'prospect'), $where);
	} else {
		sql_updateq('spip_asso_membres', array("statut_interne" => 'sorti'), $where);
	}
}

?>