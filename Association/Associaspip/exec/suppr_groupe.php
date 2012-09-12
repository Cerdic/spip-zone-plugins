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

function exec_suppr_groupe()
{
	if (!autoriser('editer_groupes', 'association', $id_groupe)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_groupe = intval(_request('id'));
		onglets_association('gestion_groupes', 'adherents');
		// INFO
		$groupe = sql_fetsel('*', 'spip_asso_groupes', "id_groupe=$id_groupe" );
		$infos['ordre_affichage_groupe'] = $groupe['affichage'];
		$infos['commentaires'] = $groupe['commentaires'];
		$infos['destination_entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_groupes_liaisons',"id_groupe=$id_groupe")) );
		echo association_totauxinfos_intro($groupe['nom'], 'groupe', $id_groupe, $infos );
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('annonce.gif', 'suppression_de_groupe');
		echo association_bloc_suppression('groupe', $id_groupe);
		fin_page_association();
	}
}

?>