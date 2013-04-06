<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_suppr_destination() {
	$r = association_controle_id('destination', 'asso_destination', 'gerer_compta');
	if ($r) {
		list($id_destination, $destination) = $r;
		exec_suppr_destination_args($id_destination, $destination);
	}
}

function exec_suppr_destination_args($id_destination, $destination) {
	include_spip ('association_modules');
	echo association_navigation_onglets('plan_comptable', 'association');
	// INTRO :
	$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_destination_op',"id_destination=$id_destination")) );
	echo association_totauxinfos_intro($destination['intitule'], 'destination', $id_destination, $infos );
	// datation et raccourcis
	echo association_navigation_raccourcis(array(
		array('destination_comptable', 'grille-24.png', array('destination_comptable', "id=$id_destination"), array('gerer_compta', 'association') ),
	) );
	debut_cadre_association('euro-39.gif', 'suppression_de_destination');
	echo association_bloc_suppression('destination', $id_destination,'destination');
	fin_page_association();
}

?>