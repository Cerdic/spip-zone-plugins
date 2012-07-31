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


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip ('inc/navigation_modules');

function exec_suppr_activite()
{
	if (!autoriser('associer', 'activites')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_activite = intval(_request('id'));
		$activite = sql_fetsel('*', 'spip_asso_dons', "id_don=$id_don");
		if (!$activite) {
			include_spip('inc/minipres');
			echo minipres(_T('zxml_inconnu_id') . $id_don);
		} else {
			onglets_association('titre_onglet_activite');
			// info
			$infos['evenement'] = sql_getfetsel('titre', 'spip_evenements', 'id_evenement='.intval($activite['id_evenement']) );
			$infos['date'] = association_datefr($activite['date_inscription']);
			$infos['activite_entete_inscrits'] = association_prixfr($activite['inscrits']);
			$infos['entete_montant'] = association_prixfr($activite['montant']);
			totauxinfos_intro(association_calculer_lien_nomid($activite['nom'],$activite['id_adherent']), 'activite', $id_activite, $infos );
			// datation et raccourcis
			icones_association('');
			debut_cadre_association('activites.gif', 'activite_titre_inscriptions_activites');
			echo bloc_confirmer_suppression('activite', $id_activite);
			fin_page_association();
		}
	}
}

?>