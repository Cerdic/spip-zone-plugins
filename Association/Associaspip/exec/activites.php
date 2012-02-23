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

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_activites(){

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'activites')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_mot = intval(_request('id_mot'));
		$annee = intval(_request('annee'));
		if(!$annee){
			$annee = date('Y');
		}
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets(_T('asso:titre_onglet_activite'));
		echo debut_gauche('',true);
		echo debut_boite_info(true);
		// TOTAUX : nombre d'activites de l'annee en cours repartis par mots-clefs
		// TOTAUX : nombre d'activites de l'annee en cours repartis par iscriptions
		$liste_libelles = $liste_effectifs = array();
		$liste_libelles['pair'] = _T('asso:activites_avec_inscrits');
		$liste_effectifs['pair'] = sql_countsel('spip_asso_activites AS a INNER JOIN spip_evenements AS e ON a.id_evenement=e.id_evenement ', "DATE_FORMAT(e.date_debut, '%Y')=$annee AND a.inscrits<>0)");
		$liste_libelles['impair'] = _T('asso:activites_sans_inscrits');
		$liste_effectifs['impair'] = sql_countsel('spip_asso_activites AS a INNER JOIN spip_evenements AS e ON a.id_evenement=e.id_evenement ', "DATE_FORMAT(e.date_debut, '%Y')=$annee AND a.inscrits=0)");
		echo totauxinfos_effectifs('activites', $liste_libelles, $liste_effectifs);
		// TOTAUX : montants des participations durant l'annee en cours
		$data = sql_fetsel('SUM(recette) AS somme_recettes, SUM(depense) AS somme_depenses', 'spip_asso_comptes', "DATE_FORMAT('date', '%Y')=$annee AND imputation=".sql_quote($GLOBALS['association_metas']['pc_activites']) );
		echo totauxinfos_sommes(_T('asso:activites'), $data['somme_recettes'], $data['somme_depenses']);
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		echo debut_droite('',true);
		echo debut_cadre_relief('', false, '', $titre = _T('asso:activite_titre_toutes_activites'));
		// FILTRES
		echo "<table class='asso_tablo_filtres'><tr>\n<td width='40%'><p class='pagination'>";
		$query = sql_select("DATE_FORMAT(date_debut, '%Y') AS annee", 'spip_evenements', '', 'annee', 'annee');
		while ($data = sql_fetch($query)) {
			if ($data['annee']==$annee) {
				echo ' <strong>'.$data['annee'].'</strong> ';
			} else {
				echo ' <a href="'. generer_url_ecrire('activites','annee='.$data['annee'].($id_mot?'&id_mot='.$id_mot:'')).'">'.$data['annee'].'</a> ';
			}
		}
		if (test_plugin_actif('AGENDA')) { /* le plugin "Agenda 2" peut associer des mots-cles aux evenements */
			if ($id_mot) {
				$mc_sel = ', M.id_mot AS motact';
				$mc_join = ' LEFT JOIN spip_mots_evenements AS A ON  A.id_evenement=E.id_evenement LEFT JOIN spip_mots AS M ON A.id_mot=M.id_mot';
				//$mc_where = "AND (M.id_mot=$id_mot OR M.titre like '$mot' OR M.titre IS NULL)";
				$mc_where = "AND M.id_mot=$id_mot";
			} else {
				$mc_sel = $mc_join = $mc_where = '';
			}
			echo "</p></td><td width='60%' class='formulaire'>";
			echo '<form method="get"><div>';
			echo '<input type="hidden" name="exec" value="activites" />';
			echo '<input type="hidden" name="annee" value="'.$annee.'" />';
			echo '<select name="id_mot" onchange="form.submit()">';
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
			echo '</select><noscript><input type="submit" value="'._T('lister').'" /></noscript></div></form></td>';
		} else { /* le plugin "Agenda Simple" par contre n'associee pas directement les mots-cles aux evenements */
			echo '</p></td>';
		}
		echo "</tr></table>\n";
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
			echo '<tr class="'.($inscrits['total']?'pair':'impair').'">';
			echo '<td class="integer">'.$data['id_evenement'].'</td>';
			echo '<td class="date">'. association_datefr($data['date_debut'],'dtstart') .'</td>';
			echo '<td class="date">'. substr($data['date_debut'],10,6) .'</td>';
			echo '<td class="text">'.$data['intitule'].'</td>';
			echo '<td class="text">'.$data['lieu'].'</td>';
			echo '<td class="integer">'.$inscrits['total'].'</td>';
			echo '<td class="actions">'. association_bouton('activite_bouton_modifier_article', 'edit-12.gif', 'articles', 'id_article='.$data['id_article']) . '</td>';
			echo '<td class="actions">'. association_bouton('activite_bouton_ajouter_inscription', 'creer-12.gif', 'edit_activite', 'id_evenement='.$data['id_evenement']) . '</td>';
			echo '<td class="actions">'. association_bouton('activite_bouton_voir_liste_inscriptions', 'voir-12.png', 'voir_activites', 'id='.$data['id_evenement']) . '</td>';
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		echo "\n<table width='100%'>\n";
		//SOUS-PAGINATION
		echo "<table class='asso_tablo_filtres'><tr>\n<td width='40%'><p class='pagination'>";
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
		echo '</p></td></tr></table>';
		echo fin_cadre_relief();
		echo fin_page_association();
	}
}

?>