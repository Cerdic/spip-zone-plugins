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
		$id_evenement = association_passeparam_id('evenement');
		onglets_association('synchroniser_asso_membres', 'activites');
		// INTRO : Rappel Infos Evenement
		$evenement = sql_fetsel('*', 'spip_evenements', "id_evenement=$id_evenement") ;
		$format = 'association_formater_'. (($evenement['horaire']=='oui')?'heure':'date');
		$infos['agenda:evenement_date_du'] = $format($evenement['date_debut'],'dtstart');
		$infos['agenda:evenement_date_au'] = $format($evenement['date_fin'],'dtend');
		$infos['agenda:evenement_lieu'] = '<span class="location">'.$evenement['lieu'].'</span>';
		echo '<div class="vevent">'. association_totauxinfos_intro('<span class="summary">'.$evenement['titre'].'</span>', 'evenement', $id_evenement, $infos, 'evenement') .'</div>';
		// TOTAUX : nombres d'inscrits par reponse
		echo association_totauxinfos_effectifs('inscriptions', array(
			'oui'=>array( 'agenda:label_reponse_jyparticipe', array('spip_evenements_participants', "id_evenement=$id_evenement AND reponse='oui' "), ),
			'nsp'=>array( 'agenda:label_reponse_jyparticipe_peutetre', array('spip_evenements_participants', "id_evenement=$id_evenement AND reponse='?' "), ),
			'non'=>array( 'agenda:label_reponse_jyparticipe_pas', array('spip_evenements_participants', "id_evenement=$id_evenement AND reponse='non' "), ),
			'etc'=>array( 'autres', array('spip_evenements_participants', "id_evenement=$id_evenement AND reponse NOT IN ('non', 'oui', '?') "), ),
		));
		// datation et raccourcis
		raccourcis_association(array('inscrits_activite', "id=$id_evenement"));
		debut_cadre_association('reload-32.png', 'options_synchronisation');
		echo recuperer_fond('prive/editer/synchroniser_asso_activites', array (
			'id_evenement' => $id_evenement,
		));
		fin_page_association();
	}
}

?>