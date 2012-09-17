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
		$id_activite = association_passeparam_id('activite');
		$activite = sql_fetsel('*', 'spip_asso_activites', "id_activite=$id_activite");
		if ( !$activite ) {
			include_spip('inc/minipres');
			echo minipres(_T('zxml_inconnu_id') . $id_activite);
		} else {
			onglets_association('titre_onglet_activite', 'activites');
			// INTRO : Rappel Infos Evenement & Participant
			$evenement = sql_fetsel('*', 'spip_evenements', 'id_evenement='.$activite['id_evenement']);
			$infos['evenement'] = $evenement['titre'];
			$format = 'association_formater_'. (($evenement['horaire']=='oui')?'heure':'date');
			$infos['agenda:evenement_date_du'] = $format($evenement['date_debut'],'dtstart');
			$infos['agenda:evenement_date_au'] = $format($evenement['date_fin'],'dtend');
			$infos['agenda:evenement_lieu'] = $evenement['lieu'];
			$infos[''] = typo('----'); // separateur
			$infos['nom'] = association_formater_idnom($activite['id_adherent'], $activite['nom'], '');
//			$infos['date'] = association_formater_date($activite['date_inscription']);
			$infos['date'] = association_formater_date($activite['date_paiement']);
			$infos['activite_entete_inscrits'] = association_formater_nombre($activite['inscrits'], 0);
			$infos['entete_montant'] = association_formater_prix($activite['montant'], 'fees');
			association_totauxinfos_intro('', 'activite', $id_activite, $infos );
			// datation et raccourcis
			raccourcis_association('');
			debut_cadre_association('activites.gif', 'activite_titre_inscriptions_activites');
			echo association_bloc_suppression('activite', $id_activite);
			fin_page_association();
		}
	}
}

?>