<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_edit_destination()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_destination = intval(_request('id'));
		onglets_association('plan_comptable');
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