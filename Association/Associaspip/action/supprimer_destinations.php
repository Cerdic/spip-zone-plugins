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

function action_supprimer_destinations() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_destination= $securiser_action();
	sql_delete('spip_asso_destination', "id_destination=$id_destination");
}

?>