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

function exec_activites()
{
	if (!autoriser('associer', 'activites')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_evenement = intval(_request('id'));
		if ($id_evenement) { // la presence de ce parametre interdit la prise en compte d'autres (a annuler donc si presents dans la requete)
			$annee = sql_getfetsel("DATE_FORMAT(date_debut, '%Y')",'spip_evenements', "id_evenement=$id_evenement"); // on recupere l'annee correspondante
		} else {
			$annee = intval(_request('annee')); // on recupere l'annee requetee
			$id_mot = intval(_request('mot')); // on recupere l'id du mot cle requete
			$id_evenement = ''; // ne pas afficher ce disgracieux '0'
		}
		if (!$annee) {
			$annee = date('Y'); // par defaut c'est l'annee courante
			$id_evenement = ''; // virer l'ID inexistant
		}
		onglets_association('titre_onglet_activite', 'activites');
		// TOTAUX : nombre d'activites de l'annee en cours repartis par mots-clefs
		// TOTAUX : nombre d'activites de l'annee en cours repartis par iscriptions
		$liste_effectifs = array();
		$liste_effectifs['pair'] = sql_count(sql_select('*, SUM(a.inscrits)', 'spip_asso_activites AS a INNER JOIN spip_evenements AS e ON a.id_evenement=e.id_evenement ', "DATE_FORMAT(e.date_debut, '%Y')=$annee",'a.id_evenement', '', '', "SUM(a.inscrits)>0"));
		$liste_effectifs['impair'] = sql_countsel('spip_asso_activites AS a LEFT JOIN spip_evenements AS e ON a.id_evenement=e.id_evenement', "DATE_FORMAT(e.date_debut, '%Y')=$annee",'a.id_evenement', "SUM(a.inscrits)=0");
		$liste_effectifs['impair'] = sql_countsel('spip_evenements', "DATE_FORMAT(date_debut, '%Y')=$annee")-$liste_effectifs['pair']; // le monde a l'envers... mais ca fonctionne
		echo association_totauxinfos_effectifs('activites', array(
			'pair'=>array( 'activites_avec_inscrits', $liste_effectifs['pair'], ),
			'impair'=>array( 'activites_sans_inscrits', $liste_effectifs['impair'], ),
		));
/*
		// STATS sur toutes les participations
		echo association_totauxinfos_stats('participations_par_personne_par_activite', 'activites', array('activite_entete_inscrits'=>'inscrits','entete_montant'=>'montant',), "DATE_FORMAT(date, '%Y')=$annee");
*/
		// TOTAUX : montants des participations durant l'annee en cours
		$data = sql_fetsel('SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses', 'spip_asso_comptes', "DATE_FORMAT('date', '%Y')=$annee AND imputation=".sql_quote($GLOBALS['association_metas']['pc_activites']) );
		echo association_totauxinfos_montants('activites', $data['somme_recettes'], $data['somme_depenses']);
		// datation et raccourci vers la gestion des evenements
		if ( test_plugin_actif('SIMPLECAL') ) { // gestion des evenements avec Simple Calendrier
			raccourcis_association(array(), array(
				'evenements' => array('simplecal-logo-16.png', 'evenement_tous'),
			) );
		} elseif ( test_plugin_actif('AGENDA') ) { // gestion des evenements avec Agenda 2
			raccourcis_association(array(), array(
				'evenements' => array('agenda-evenements-16.png', 'agenda_evenements'),
			) );
		} else { // pas de bloc de raccourcis
			echo association_date_du_jour();
			echo fin_boite_info(true);
		}
		debut_cadre_association('activites.gif','activite_titre_toutes_activites');
		// FILTRES
		if (test_plugin_actif('AGENDA')) { // le plugin "Agenda 2" peut associer des mots-cles aux evenements : les proposer comme critere de filtrage
			if ($id_mot) {
				$mc_sel = ', m.id_mot AS motact';
				$mc_join = ' LEFT JOIN spip_mots_evenements AS k ON  k.id_evenement=e.id_evenement LEFT JOIN spip_mots AS m ON k.id_mot=m.id_mot';
				//$mc_where = " AND (m.id_mot=$id_mot OR m.titre LIKE '$mot' OR m.titre IS NULL) ";
				$mc_where = "AND m.id_mot=$id_mot";
			} else {
				$mc_sel = $mc_join = $mc_where = '';
			}
			$filtre_motscles = '<select name="mot" onchange="form.submit()">';
			$filtre_motscles .= '<option value="">'._T('asso:entete_tous').'</option>';
			$query_groupes = sql_select('id_groupe, titre', 'spip_groupes_mots', "tables_liees LIKE '%evenements%'");
			while($data_groupes = sql_fetch($query_groupes)) {
				$filtre_motscles .= '<optgroup label="'.$data_groupes['titre'].'">';
				$query_mots = sql_select('id_mot, titre', 'spip_mots', 'id_groupe='.$data_groupes['id_groupe']);
				while($data_mots = sql_fetch($query_mots)) {
					$filtre_motscles .= '<option value="'.$data_mots['id_mot'].'"';
					if ($id_mot==$data_mots['id_mot']) {
						$filtre_motscles .= ' selected="selected"';
					}
					$filtre_motscles .= '> '.$data_mots['titre'].'</option>';
				}
				$filtre_motscles .= '</optgroup>';
			}
			$filtre_motscles .= '</select></td>';
		} else { // pas de mots cles...
			$filtre_motscles = '';
		}
		filtres_association(array(
			'annee' => array($annee, 'evenements', 'debut'),
#			'id' => $id_evenement,
		), 'activites', array(
			'mot' => $filtre_motscles,
		));
		//TABLEAU
		echo association_bloc_listehtml(
//			array('*, e.id_evenement, e.titre AS intitule'.$mc_sel, 'spip_evenements AS e'.$mc_join, "DATE_FORMAT(date_debut, '%Y')=$annee $mc_where", '', 'date_debut DESC', sql_asso1page() ), // requete
			array("e.id_evenement, e.date_debut, e.date_fin, e.titre  AS intitule, e.lieu,  COUNT(a.id_activite)+SUM(a.inscrits) as personnes, SUM(a.montant) as montants, CASE WHEN SUM(a.inscrits)=0 THEN 'sans' ELSE 'avec' END invites $mc_sel", "spip_evenements AS e LEFT JOIN spip_asso_activites AS a ON e.id_evenement=a.id_evenement $mc_join", "DATE_FORMAT(date_debut, '%Y')=$annee $mc_where", 'e.id_evenement', 'date_debut DESC, date_fin DESC', sql_asso1page() ), // requete
			array(
				'id_evenement' => array('asso:entete_id', 'entier'),
				'date_debut' => array('agenda:evenement_date_du', 'date', 'dtstart'),
				'date_fin' => array('agenda:evenement_date_au', 'date', 'dtend'),
				'intitule' => array('asso:entete_intitule', 'texte', '', '', 'summary'),
				'lieu' => array('agenda:evenement_lieu', 'texte', '', '', 'location'),
				'personnes' => array('asso:activite_entete_inscrits', 'entier'),
				'montants' => array('asso:entete_montant', 'prix'),
			), // entetes et formats des donnees
			array(
				array('act', 'activite_bouton_ajouter_inscription', 'creer-12.gif', 'edit_activite', 'id_evenement=$$'),
				array('act', 'activite_bouton_voir_liste_inscriptions', 'voir-12.png', 'inscrits_activite', 'id=$$'),
			), // boutons d'action
			'id_evenement', // champ portant la cle des lignes et des boutons
			array('sans'=>'pair vevent', 'avec vevent'=>'impair'), 'invites', $id_evenement
		);
		//SOUS-PAGINATION
		echo "<table width='100%' class='asso_tablo_filtres'><tr>\n";
		echo association_selectionner_souspage(array('spip_evenements', "DATE_FORMAT(date_debut, '%Y')=$annee"), 'activites', 'annee='.$annee );
		echo '</tr></table>';
		fin_page_association();
	}
}

?>