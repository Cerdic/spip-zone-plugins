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
include_spip ('inc/association_comptabilite');

function exec_edit_don()
{
	if (!autoriser('associer', 'dons')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id_don = intval(_request('id'));
		onglets_association('titre_onglet_dons');
		// INTRO : resume don
		echo association_totauxinfos_intro('', 'don', $id_don);
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('dons-24.gif', 'dons_titre_mise_a_jour');
		echo recuperer_fond('prive/editer/editer_asso_dons', array (
			'id_don' => $id_don
		));
		fin_page_association();
	}
}

?>
