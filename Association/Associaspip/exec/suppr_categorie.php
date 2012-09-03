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


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip ('inc/navigation_modules');

function exec_suppr_categorie()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_categorie=intval(_request('id'));
		onglets_association('categories_de_cotisations');
		// INTRO : resume ressource
		$categorie = sql_fetsel('*', 'spip_asso_categories', "id_categorie=$id_categorie" );
		$infos['entete_code'] = $categorie['valeur'];
		$infos['entete_duree'] = association_formater_duree($categorie['duree'], 'M');
		$infos['entete_montant'] = association_formater_prix($categorie['cotisation']);
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_membres', "categorie=$id_categorie"), ));
		echo association_totauxinfos_intro($categorie['libelle'], 'categorie', $id_categorie, $infos );
		// datation et raccourcis
		raccourcis_association('categories');
		debut_cadre_association('calculatrice.gif', 'categories_de_cotisations');
		echo association_bloc_suppression('categorie', $id_categorie);
		fin_page_association();
	}
}

?>