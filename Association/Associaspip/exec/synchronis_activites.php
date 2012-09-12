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

function exec_synchronis_activites()
{
	if (!autoriser('associer', 'activites')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id_evenement = intval(_request('id'));
		onglets_association('synchroniser_asso_membres', 'activites');
		// INTRO : Rappel Infos Evenement
		$evenement = sql_fetsel('*', 'spip_evenements', "id_evenement=$id_evenement") ;
		$infos['evenement_date_du'] = association_formater_date($evenement['date_debut'],'dtstart').' '.substr($data['date_debut'],10,6);
		$infos['evenement_date_au'] = association_formater_date($evenement['date_fin'],'dtend').' '.substr($data['date_debut'],10,6);
		$infos['evenement_lieu'] = $evenement['lieu'];
		echo association_totauxinfos_intro($evenement['titre'], 'evenement', $id_evenement, $infos, 'agenda');
		// TOTAUX : nombres d'inscrits par etat de paiement
		$liste_libelles = $liste_effectifs = array();
		$liste_libelles['oui'] = _T('agenda:label_reponse_jyparticipe');
		$liste_libelles['nsp'] = _T('agenda:label_reponse_jyparticipe_peutetre');
		$liste_libelles['non'] = _T('agenda:label_reponse_jyparticipe_pas');
		$liste_effectifs['oui'] = sql_getfetsel('COUNT(*)', 'spip_evenements_participants', "id_evenement=$id_evenement AND reponse='oui' ");
		$liste_effectifs['non'] = sql_getfetsel('COUNT(*)', 'spip_evenements_participants', "id_evenement=$id_evenement AND reponse='non' ");
		$liste_effectifs['nsp'] = sql_getfetsel('COUNT(*)', 'spip_evenements_participants', "id_evenement=$id_evenement AND reponse='?' ");
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('reload-32.png', 'options_synchronisation');
		echo recuperer_fond('prive/editer/synchroniser_asso_activites', array (
			'id_evenement' => $id_evenement,
		));
		fin_page_association();
	}
}

?>