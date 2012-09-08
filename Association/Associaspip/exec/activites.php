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
		echo '<form method="get" action="'. generer_url_ecrire('activites') .'">';
		echo "\n<input type='hidden' name='exec' value='activites' />";
		echo "\n<table width='100%' class='asso_tablo_filtres'><tr>";
		echo '<td id="filtre_annee">'. association_selectionner_annee($annee, 'evenements', 'debut') .'</td>';
#		echo '<td id="filtre_id">'. association_selectionner_id($id_evenement) .'</td>';
		if (test_plugin_actif('AGENDA')) { // le plugin "Agenda 2" peut associer des mots-cles aux evenements : les proposer comme critere de filtrage
			if ($id_mot) {
				$mc_sel = ', M.id_mot AS motact';
				$mc_join = ' LEFT JOIN spip_mots_evenements AS A ON  A.id_evenement=E.id_evenement LEFT JOIN spip_mots AS M ON A.id_mot=M.id_mot';
				//$mc_where = "AND (M.id_mot=$id_mot OR M.titre like '$mot' OR M.titre IS NULL)";
				$mc_where = "AND M.id_mot=$id_mot";
			} else {
				$mc_sel = $mc_join = $mc_where = '';
			}
			echo '<td id="filtre_mot">';
			echo '<select name="mot" onchange="form.submit()">';
			echo '<option value="">'._T('asso:entete_tous').'</option>';
			$query_groupes = sql_select('id_groupe, titre', 'spip_groupes_mots', "tables_liees LIKE '%evenements%'");
			while($data_groupes = sql_fetch($query_groupes)) {
				echo '<optgroup label="'.$data_groupes['titre'].'">';
				$query_mots = sql_select('id_mot, titre', 'spip_mots', 'id_groupe='.$data_groupes['id_groupe']);
				while($data_mots = sql_fetch($query_mots)) {
					echo '<option value="'.$data_mots['id_mot'].'"';
					if ($id_mot==$data_mots['id_mot']) {
						echo ' selected="selected"';
					}
					echo '> '.$data_mots['titre'].'</option>';
				}
				echo '</optgroup>';
			}
			echo '</select></td>';
		}
		echo '<noscript><td><input type="submit" value="'._T('asso:bouton_filtrer').'" /></td></noscript>';
		echo '</tr></table></form>';
		//TABLEAU
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_activites'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'. _T('asso:entete_id') .'</th>';
		echo '<th>'. _T('asso:entete_date') .'</th>';
		echo '<th>'. _T('asso:activite_entete_heure') .'</th>';
		echo '<th>'. _T('asso:entete_intitule') .'</th>';
		echo '<th>'. _T('asso:activite_entete_lieu') .'</th>';
		echo '<th>'. _T('asso:activite_entete_inscrits') .'</th>';
		echo '<th colspan="3" class="actions">'. _T('asso:entete_action') .'</th>';
		echo "</tr>\n</thead><tbody>";
		$max_par_page = 30;
		$debut = intval(_request('debut'));
		if (!$debut) {
			$debut = 0;
		}
		$query = sql_select('*, E.id_evenement, E.titre AS intitule'.$mc_sel, 'spip_evenements AS E'.$mc_join, "DATE_FORMAT(date_debut, '%Y')=$annee $mc_where", '', 'date_debut DESC', "$debut,$max_par_page");
		while ($data = sql_fetch($query)) {
			$inscrits = sql_fetsel('SUM(inscrits) AS total', 'spip_asso_activites', 'id_evenement='.$data['id_evenement']);
			echo '<tr class="'. ($inscrits['total']?'pair':'impair') . (($id_evenement==$data['id_evenement'])?' surligne':'') .'" id="'.$data['id_evenement'].'">';
			echo '<td class="integer">'.$data['id_evenement'].'</td>';
			echo '<td class="date">'. association_formater_date($data['date_debut'],'dtstart') .'</td>';
			echo '<td class="date">'. substr($data['date_debut'],10,6) .'</td>';
			echo '<td class="text">'.$data['intitule'].'</td>';
			echo '<td class="text">'.$data['lieu'].'</td>';
			echo '<td class="integer">'.$inscrits['total'].'</td>';
			echo '<td class="actions">'. association_bouton_faire('activite_bouton_modifier_article', 'edit-12.gif', 'articles', 'id_article='.$data['id_article']) . '</td>';
			echo '<td class="actions">'. association_bouton_faire('activite_bouton_ajouter_inscription', 'creer-12.gif', 'edit_activite', 'id_evenement='.$data['id_evenement']) . '</td>';
			echo '<td class="actions">'. association_bouton_faire('activite_bouton_voir_liste_inscriptions', 'voir-12.png', 'inscrits_activite', 'id='.$data['id_evenement']) . '</td>';
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		echo "\n<table width='100%'>\n";
		//SOUS-PAGINATION
		echo "<table width='100%' class='asso_tablo_filtres'><tr>\n<td align='left'>";
		$nombre_selection = sql_countsel('spip_evenements', "DATE_FORMAT(date_debut, '%Y')=$annee");
		$pages = ceil($nombre_selection/$max_par_page);
		if ($pages==1) {
			echo '';
		} else {
			for ($i=0; $i<$pages; $i++) {
				$position = $i*$max_par_page;
				if ($position==$debut) {
					echo ' <strong>'.$position.' </strong> ';
				} else {
					echo '<a href="'.generer_url_ecrire('activites','annee='.$annee.'&debut='.$position.'&imputation='.$imputation).'">'.$position.'</a>  ';
				}
			}
		}
		echo '</td></tr></table>';
		fin_page_association();
	}
}

?>