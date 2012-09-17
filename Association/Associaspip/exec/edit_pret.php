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

function exec_edit_pret()
{
	$id_pret = association_passeparam_id('pret');
	if (!autoriser('associer', 'activites')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		onglets_association('titre_onglet_prets', 'ressources');
		if ($id_pret) { // modifier
			$id_ressource = sql_getfetsel('id_ressource', 'spip_asso_prets', "id_pret=$id_pret");
		} else { // ajouter
			$id_ressource = intval(_request('id_ressource'));
		}
		// INTRO : resume ressource
		$infos['ressource_pretee'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_prets', "id_ressource=$id_ressource"), ));
		echo association_totauxinfos_intro(sql_getfetsel('intitule', 'spip_asso_ressources', "id_ressource=$id_ressource" ), 'ressource', $id_ressource, $infos );
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association(($id_pret?'edit-12.gif':'creer-12.gif'), 'prets_titre_edition_prets');
		echo recuperer_fond('prive/editer/editer_asso_prets', array (
			'id_ressource' => $id_ressource,
			'id_pret' => $id_pret,
		));
		fin_page_association();
	}
}

?>