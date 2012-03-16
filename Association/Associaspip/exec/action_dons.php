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

function exec_action_dons()
{
	if (!autoriser('associer', 'dons')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_don = intval(_request('id'));
		$data = sql_fetsel('*', 'spip_asso_dons', "id_don=$id_don");
		if (!$data) {
			include_spip('inc/minipres');
			echo minipres(_T('zxml_inconnu_id') . $id_don);
		} else {
			association_onglets(_T('asso:titre_onglet_dons'));
			// info
			$don = sql_fetsel('*', 'spip_asso_dons', "id_don=$id_don");
			$infos['argent'] = association_prixfr($don['argent']);
			$infos['colis'] = ($don['valeur'] ? '('.association_prixfr($don['valeur'])')<br />' : '') .$don['colis'];
			$onfos['contrepartie'] = $don['contrepartie'];
			totauxinfos_intro(association_calculer_lien_nomid($don['bienfaiteur'],$don['id_adherent']), 'don', $id_don, $infos );
			// datation
			echo association_date_du_jour();
			echo fin_boite_info(true);
			echo association_retour();
			debut_cadre_association('dons.gif', 'action_sur_les_dons');
			echo bloc_confirmer_suppression('don', $id_don);
			fin_page_association();
		}
	}
}

?>