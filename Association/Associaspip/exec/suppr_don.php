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

function exec_suppr_don()
{
	if (!autoriser('associer', 'dons')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_don = intval(_request('id'));
		$don = sql_fetsel('*', 'spip_asso_dons', "id_don=$id_don");
		if (!$don) {
			include_spip('inc/minipres');
			echo minipres(_T('zxml_inconnu_id') . $id_don);
		} else {
			onglets_association('titre_onglet_dons', 'dons');
			// info
			$infos['argent'] = association_formater_prix($don['argent']);
			$infos['colis'] = ($don['valeur'] ? '('.association_formater_prix($don['valeur']).')<br />' : '') .$don['colis'];
			$infos['contrepartie'] = $don['contrepartie'];
			association_totauxinfos_intro(association_calculer_lien_nomid($don['bienfaiteur'],$don['id_adherent']), 'don', $id_don, $infos );
			// datation et raccourcis
			raccourcis_association('');
			debut_cadre_association('dons-24.gif', 'action_sur_les_dons');
			echo association_bloc_suppression('don', $id_don);
			fin_page_association();
		}
	}
}

?>