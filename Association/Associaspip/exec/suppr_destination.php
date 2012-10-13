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

function exec_suppr_destination() {
	if (!autoriser('configurer_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		list($id_destination, $destination) = association_passeparam_id('destination', 'asso_destination');
		onglets_association('plan_comptable', 'association');
		// INTRO :
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_destination_op',"id_destination=$id_destination")) );
		echo association_totauxinfos_intro($destination['intitule'], 'destination', $id_destination, $infos );
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('euro-39.gif', 'suppression_de_destination');
		echo association_bloc_suppression('destination', $id_destination);
		fin_page_association();
	}
}

?>