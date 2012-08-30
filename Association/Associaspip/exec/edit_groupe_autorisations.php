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

function exec_edit_groupe_autorisations()
{
	if (!autoriser('gerer_autorisations', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_groupe = intval(_request('id'));
		onglets_association('gerer_les_autorisations');
		// INFO
		$groupe = sql_fetsel('commentaires', 'spip_asso_groupes', "id_groupe=$id_groupe" );
		$infos['commentaires'] = $groupe['commentaires'];
		$infos['destination_entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_groupes_liaisons',"id_groupe=$id_groupe")) );
		echo association_totauxinfos_intro(_T("asso:groupe_".$id_groupe), 'groupe', $id_groupe, $infos );
		// datation et raccourcis
		icones_association(array('association_autorisations'));
		debut_cadre_association('annonce.gif', 'titre_editer_groupe');
		echo recuperer_fond('prive/editer/editer_asso_groupes', array (
			'id' => $id_groupe
		));
		fin_page_association();
	}
}
?>
