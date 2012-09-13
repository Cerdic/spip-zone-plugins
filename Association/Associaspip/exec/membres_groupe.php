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

function exec_membres_groupe()
{
	$id_groupe = intval(_request('id'));
	if (!autoriser('voir_groupes', 'association', $id_groupe)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association(($id_groupe>=100)?'gestion_groupes':'gerer_les_autorisations', ($id_groupe>=100?'adherents':'association') );
		// INFO
		$groupe = sql_fetsel('*', 'spip_asso_groupes', "id_groupe=$id_groupe" );
		if ($id_groupe>=100) {
			$infos['ordre_affichage_groupe'] = $groupe['affichage'];
		}
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_groupes_liaisons',"id_groupe=$id_groupe")) );
		echo association_totauxinfos_intro(($id_groupe<100)?_T("asso:groupe_".$id_groupe):$groupe['nom'], 'groupe', $id_groupe, $infos );
		// datation et raccourcis
		raccourcis_association('', array(
			'editer_groupe' => array('edit-24.gif', array(($id_groupe<100?'edit_groupe_autorisations':'edit_groupe'), "id=$id_groupe" ) ),
		) );
		debut_cadre_association('annonce.gif', 'groupe_membres');
		echo recuperer_fond('prive/contenu/voir_membres_groupe', array(
			'id_groupe' => $id_groupe
		));
		fin_page_association();
	}
}

?>