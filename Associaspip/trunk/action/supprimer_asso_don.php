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

function action_supprimer_asso_don_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_don = $securiser_action();
	include_spip ('inc/association_comptabilite');
	comptabilite_operation_supprimer(comptabilite_reference_operation('pc_dons', $id_don));
	comptabilite_operation_supprimer(comptabilite_reference_operation('pc_colis', $id_don));
	sql_delete('spip_asso_dons', "id_don=$id_don");
}

?>