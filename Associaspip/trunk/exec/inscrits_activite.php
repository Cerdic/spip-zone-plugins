<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_inscrits_activite() {
	if (!autoriser('voir_inscriptions', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		$id_evenement = association_passeparam_id('evenement');
		onglets_association('titre_onglet_activite', 'activites');

		if ( test_plugin_actif('agenda')) {
			list($id_periode, $critere_periode) = association_passeparam_periode('debut', 'evenements', $id_evenement);
			$evenement = sql_fetsel('*', 'spip_evenements', "id_evenement=$id_evenement");
			$statut = association_passeparam_statut();
			// INTRO : Rappel Infos Evenement
			$format = 'association_formater_'. (($evenement['horaire']=='oui')?'heure':'date');
			$infos['agenda:evenement_date_du'] = $format($evenement['date_debut'],'dtstart');
			$infos['agenda:evenement_date_au'] = $format($evenement['date_fin'],'dtend');
			$infos['agenda:evenement_lieu'] = '<span class="location">'.$evenement['lieu'].'</span>';
			echo '<div class="vevent">'. association_totauxinfos_intro('<span class="summary">'.$evenement['titre'].'</span>', 'evenement', $id_evenement, $infos, 'evenement') .'</div>';
		// TOTAUX : nombres d'inscriptions par etat de paiement
			echo association_totauxinfos_effectifs('participations', array(
			'valide' => array( 'asso:activite_entete_validees', array('spip_asso_activites', "id_evenement=$id_evenement AND date_paiement<date_inscription "), ),
			'pair' => array( 'asso:activite_entete_impayees', array('spip_asso_activites', "id_evenement=$id_evenement AND NOT date_paiement<date_inscription "), ),
										       ));
		// STATS sur les participations a cette activite (nombre de place et montant paye)
			echo association_totauxinfos_stats('participations', 'activites', array('entete_quantite'=>'quantite','entete_montant'=>'prix_unitaire',), "id_evenement=$id_evenement");
		// TOTAUX : montants des participations
			echo association_totauxinfos_montants('participations', array('SUM(prix_unitaire) AS encaisse', 'spip_asso_activites', "id_evenement=$id_evenement " ), NULL);
		// datation et raccourcis
			$res['activite_bouton_modifier_article'] = array('edit-12.gif', array('articles', 'id_article='.$evenement['id_article']));
			$res['activite_bouton_ajouter_inscription'] = array('panier_in.gif', array('edit_activite', "id_evenement=$id_evenement"));
		}
		if ( test_plugin_actif('FPDF') && sql_countsel('spip_asso_activites', "id_evenement=$id_evenement", 'id_auteur') ) { // PDF des inscrits
			$res['activite_bouton_imprimer_inscriptions'] = array('print-24.png', array('pdf_activite', "id=$id_evenement"));
		}
		if ( test_plugin_actif('AGENDA') && sql_countsel('spip_evenements_participants', "id_evenement=$id_evenement", 'id_auteur') ) { // inscrits via le formulaire d'Agenda2
			$res['activite_bouton_synchroniser_inscriptions'] = array('reload-32.png', array('synchronis_activites', "id=$id_evenement"));
		}
		echo association_navigation_raccourcis(generer_url_ecrire('activites',($GLOBALS['association_metas']['exercices']?'exercice':'annee')."=$id_periode"), $res);
		debut_cadre_association('activites.gif', 'activite_titre_inscriptions_activites');
		// FILTRES
		$filtre_statut = '<select name="statut" onchange="form.submit()">';
		$filtre_statut .= '<option value="">' ._T('asso:entete_tous') .'</option>';
		$filtre_statut .= '<option value="1"'. (intval($statut)>0?' selected="selected"':'') .'>'. _T('asso:activite_entete_validees') .'</option>';
		$filtre_statut .= '<option value="-1"'. (intval($statut)<0?' selected="selected"':'') .'>'. _T('asso:activite_entete_impayees') .'</option>';
		$filtre_statut .= '</select>';
		echo association_bloc_filtres(array(
//			'periode' => array($id_periode, 'asso_activites', 'inscription'),
#			'id' => $id_activite,
		), 'inscrits_activite', array(
			'statut' => $filtre_statut,
			0 => '<input type="hidden" name="id" value="'.$id_evenement.'" />',
		));
	// TABLEAU
		if ($statut) { // restriction de la selection
			$critereSupplementaire = ' AND '. ($statut>0?"date_paiement<date_inscription ":"date_paiement>=date_inscription ");
		}
		echo association_bloc_listehtml2('asso_activites',
			sql_select("*, CASE date_paiement WHEN '0000-00-00' THEN 0 ELSE 1 END AS statut_paiement ", 'spip_asso_activites', "id_evenement=$id_evenement $critereSupplementaire ", '', 'id_activite DESC'), // requete
			array(
				'id_activite' => array('asso:entete_id', 'entier'),
				'date_inscription' => array('asso:entete_date', 'date', ''),
//				'date_paiement' => array('asso:entete_date', 'date', ''),
				'id_auteur' => array('asso:entete_nom', 'idnom', array('spip_asso_activites', 'nom', 'id_auteur'), 'membre'),
				'quantite' => array('asso:entete_quantite', 'entier'),
				'prix_unitaire' => array('asso:entete_montant', 'prix', 'fees'),
				'commentaire' => array('asso:entete_commentaire', 'texte', 'propre'),
			), // entetes et formats des donnees
			autoriser('editer_inscriptions', 'association') ? array(
				array('suppr', 'activite', 'id=$$'),
				array('paye', 'edit_activite', 'id=$$'),
			) : array(), // boutons d'action
			'id_activite', // champ portant la cle des lignes et des boutons
			array('pair', 'valide'), 'statut_paiement'
		);
		fin_page_association();
	}
}

?>
