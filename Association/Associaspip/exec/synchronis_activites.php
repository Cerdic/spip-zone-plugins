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

function exec_synchronis_activites()
{
	if (!autoriser('associer', 'activites')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id_evenement = intval(_request('id'));
		onglets_association('synchroniser_asso_membres');
		// Notice
		echo propre(_T('asso:synchroniser_note'));
		// datation et raccourcis
		icones_association('');
		debut_cadre_association('reload-32.png', 'options_synchronisation');
		echo recuperer_fond('prive/editer/synchroniser_asso_activites', array (
			'id_evenement' => $id_evenement,
		));
		fin_page_association();
	}
}

?>