<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_edit_plan()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_plan = intval(_request('id'));
		onglets_association('plan_comptable');
		// Notice
		echo propre(_T('asso:edit_plan'));
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('euro-39.gif', 'edition_plan_comptable');
		echo recuperer_fond('prive/editer/editer_asso_plan', array (
			'id_plan' => $id_plan
		));
		fin_page_association();
	}
}

?>