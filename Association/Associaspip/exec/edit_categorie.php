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

function exec_edit_categorie()
{
	if (!autoriser('associer', 'comptes')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id_categorie = intval(_request('id'));
		onglets_association('categories_de_cotisations', 'association');
		// INTRO : resume ressource
		$infos['entete_utilisee'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_membres', "categorie=$id_categorie"), ));
		echo association_totauxinfos_intro(sql_getfetsel('libelle', 'spip_asso_categories', "id_categorie=$id_categorie" ), 'categorie', $id_categorie, $infos );
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('calculatrice.gif', 'categories_de_cotisations');
		echo recuperer_fond('prive/editer/editer_asso_categories', array (
			'id_categorie' => $id_categorie
		));
		fin_page_association();
	}
}

?>