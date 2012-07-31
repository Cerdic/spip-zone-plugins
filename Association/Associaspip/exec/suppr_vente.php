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

function exec_suppr_vente()
{
	if (!autoriser('associer', 'ventes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_vente = intval(_request('id'));
		onglets_association('titre_onglet_ventes');
		// info
		echo totauxinfos_intro(sql_getfetsel("CONCAT(article,' -- ',acheteur) AS intitule", 'spip_asso_ventes', "id_vente=$id_vente" ), 'vente', $id_vente, $infos );
		// datation et raccourcis
		icones_association('');
		debut_cadre_association('ventes.gif', 'action_sur_les_ventes_associatives');
		echo bloc_confirmer_suppression('vente', $id_vente);
		fin_page_association();
	}
}

?>