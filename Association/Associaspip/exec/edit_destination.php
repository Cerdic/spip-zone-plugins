<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_edit_destination()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		$id_destination = association_passeparam_id('destination');
		onglets_association('plan_comptable', 'association');
		// INTRO :
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_destination_op',"id_destination=$id_destination")) );
		echo association_totauxinfos_intro(sql_getfetsel('intitule','spip_asso_destination',"id_destination=$id_destination"), 'destination', $id_destination, $infos );
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('euro-39.gif', 'destination_nav_ajouter');
		echo recuperer_fond('prive/editer/editer_asso_destinations', array (
			'id_destination' => $id_destination,
		));
		fin_page_association();
	}
}

?>