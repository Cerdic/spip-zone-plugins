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

function exec_groupes()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		association_onglets(_T('asso:gestion_groupes'));
		// notice
		echo _T('asso:aide_groupes');
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone('ajouter_un_groupe',  generer_url_ecrire('edit_groupe'), 'annonce.gif');
		$res .= association_icone('bouton_retour', generer_url_ecrire('adherents'), 'retour-24.png');
		echo bloc_des_raccourcis($res);
		debut_cadre_association('annonce.gif', 'tous_les_groupes');
		echo recuperer_fond('prive/contenu/voir_groupes', array ());
		fin_page_association();
	}
}

?>