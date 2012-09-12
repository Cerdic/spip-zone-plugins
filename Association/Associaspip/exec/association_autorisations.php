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

function exec_association_autorisations()
{
	if (!autoriser('gerer_autorisations', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association('gerer_les_autorisations', 'association');
		// notice
		echo _T('asso:aide_gerer_autorisations');
		// datation et raccourcis
		raccourcis_association('association');
		debut_cadre_association('annonce.gif', 'les_groupes_dacces');
		echo recuperer_fond('prive/contenu/voir_groupes_autorisations', array ());
		fin_page_association();
	}
}

?>
