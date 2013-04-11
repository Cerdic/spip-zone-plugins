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

function action_supprimer_asso_vente_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_vente = $securiser_action();
	include_spip ('inc/association_comptabilite');
	association_supprimer_operation_comptable2($id_vente, 'pc_ventes');
	association_supprimer_operation_comptable2($id_vente, 'pc_frais_envoi');
	sql_delete('spip_asso_ventes', "id_vente=$id_vente");
}

?>