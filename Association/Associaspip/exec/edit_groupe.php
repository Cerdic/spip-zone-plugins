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

function exec_edit_groupe()
{
	if (!autoriser('associer', 'groupes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_groupe = intval(_request('id'));
		association_onglets(_T('asso:gestion_groupes'));
		// INFO
		$groupe = sql_fetsel('*', 'spip_asso_groupes', "id_groupe=$id_groupe" );
		$infos['ordre_affichage_groupe'] = $groupe['affichage'];
		$infos['commentaires'] = $groupe['commentaires'];
		$infos['destination_entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_groupes_liaisons',"id_groupe=$id_groupe")) );
		echo totauxinfos_intro($groupe['nom'], 'groupe', $id_groupe, $infos );
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		echo association_retour(generer_url_ecrire('groupes'));
		debut_cadre_association('annonce.gif', 'titre_editer_groupe');
		echo recuperer_fond('prive/editer/editer_asso_groupes', array (
			'id' => $id_groupe
		));
		fin_page_association();
	}
}
?>
