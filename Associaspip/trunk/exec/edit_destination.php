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

function exec_edit_destination() {
	sinon_interdire_acces(autoriser('gerer_compta', 'association'));
	include_spip ('association_modules');
/// INITIALISATIONS
	$id_destination = association_passeparam_id('destination');
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('plan_comptable', 'association');
/// AFFICHAGES_LATERAUX : INTRO : info destination
	$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_destination_op',"id_destination=$id_destination")) );
	echo association_tablinfos_intro(sql_getfetsel('intitule','spip_asso_destination',"id_destination=$id_destination"), 'destination', $id_destination, $infos );
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('destination_comptable', 'grille-24.png', array('destination_comptable', "id=$id_destination"), array('gerer_compta', 'association') ),
	) );
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('euro-39.gif', 'destination_nav_ajouter');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
	echo recuperer_fond('prive/editer/editer_asso_destination', array (
		'id_destination' => $id_destination,
	));
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>