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
include_spip ('inc/association_comptabilite');

function exec_edit_vente()
{
	if (!autoriser('associer', 'ventes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_vente = intval(_request('id'));
		association_onglets(_T('asso:titre_onglet_ventes'));
		// info
		echo totauxinfos_intro('', 'vente', $id_vente);
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		echo association_retour();
		debut_cadre_association('ventes.gif', 'ressources_titre_mise_a_jour');
		echo recuperer_fond('prive/editer/editer_asso_ventes', array (
			'id_vente' => $id_vente
		));
		fin_page_association();
	}
}

?>