<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Version HTML de la synthese des Comptes de Bilan
function exec_bilan() {
	if (!autoriser('voir_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('association_modules');
		include_spip ('inc/association_comptabilite');
		$plan = sql_countsel('spip_asso_plan');
		$ids = association_passeparam_compta();
		if ( !($ids_destinations = _request('destinations')) ) // recuperer l'id_destination de la ou des destinations
			$ids_destinations = array(0); // ...ou creer une entree a 0 dans le tableau
		include_spip('inc/association_comptabilite');
		echo association_navigation_onglets('titre_onglet_comptes', 'comptes');
		// INTRO : rappel de l'exercicee affichee
		$infos['exercice_entete_debut'] = association_formater_date($ids['debut_periode'], 'dtstart');
		$infos['exercice_entete_fin'] = association_formater_date($ids['fin_periode'], 'dtend');
		echo association_totauxinfos_intro($ids['titre_periode'], 'exercice', $ids['id_periode'], $infos);
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			array('informations_comptables', 'grille-24.png', array('comptes', "$ids[type_periode]=$ids[id_periode]"), array('voir_compta', 'association') ),
			array('cpte_resultat_titre_general', 'finances-24.png', array('compte_resultat', "$ids[type_periode]=$ids[id_periode]"), array('voir_compta', 'association') ),
#			array('annexe_titre_general', 'finances-24.png', array('compte_annexe', "$ids[type_periode]=$ids[id_periode]"), array('voir_compta', 'association') ),
			array('encaisse', 'finances-24.png', array('encaisse', "$ids[type_periode]=$ids[id_periode]"), array('voir_compta', 'association') ),
		), 16);
		// on cree les intitule de toutes les destinations dans un tableau
		$intitule_destinations = array();
		$destinations = sql_allfetsel('id_destination, intitule', 'spip_asso_destination', '', '', 'intitule'); // on recupere tout dans un tableau : il ne devrait pas y en avoir des masses...
		foreach ($destinations as $d) { // on veut plutot un tableau des intitules de toutes les destinations, donc une association id_destination=>intitule
			$intitule_destinations[$d['id_destination']] = $d['intitule'];
		}
		if ($GLOBALS['association_metas']['destinations']) { // on affiche une liste de choix de destinations
			echo debut_cadre_enfonce('',TRUE);
			echo '<h3>'. _T('plugins_vue_liste') .'</h3>';
			echo association_selectionner_destinations($ids_destinations, 'bilan&'."$ids[type_periode]=$ids[id_periode]", '<p class="boutons"><input type="submit" value="'. _T('asso:compte_resultat') .'" /></p>', FALSE); // selecteur de destinations
			echo fin_cadre_enfonce(TRUE);
		}
		debut_cadre_association('finances-24.png', 'resultat_courant');
		// Filtres
		echo association_bloc_filtres(array(
			'periode' => array($ids['id_periode'], 'asso_comptes', 'operation'),
			'destinations' => array($ids_destinations, 'bilan&'."$ids[type_periode]=$ids[id_periode]", '', TRUE),
		), 'bilan');
		if ($plan) {
			$join = ' RIGHT JOIN spip_asso_plan ON imputation=code';
			$sel = ', code, intitule, classe';
			$where = " date_operation>='$ids[debut_periode]' AND date_operation<='$ids[fin_operation]' ";
			$having =  "classe NOT IN (". sql_quote($GLOBALS['association_metas']['classe_banques']). ',' .sql_quote($GLOBALS['association_metas']['classe_contributions_volontaires']) . ',' .sql_quote($GLOBALS['association_metas']['classe_charges']) . ',' .sql_quote($GLOBALS['association_metas']['classe_produits']) . ')';
			$order = 'code';
		} else {
			$join = $sel = $where = $having = $order = '';
		}
		$classes = array(
			sql_quote($GLOBALS['association_metas']['classe_charges']),
			sql_quote($GLOBALS['association_metas']['classe_produits']),
		);
		foreach ($ids_destinations as $id_destination) { // on boucle sur le tableau des destinations en refaisant le fetch a chaque iteration
			// TABLEAU EXPLOITATION
			echo debut_cadre_relief('', TRUE, '', ($id_destination ? $intitule_destinations[$id_destination] : ($GLOBALS['association_metas']['destinations']?_T('asso:toutes_destination'):'') ) );
			association_liste_totaux_comptes_classes($classes, 'cpte_resultat', 0, $ids['id_periode'], $id_destination);
			if(autoriser('exporter_compta', 'association') && !$id_destination) { // on peut exporter : pdf, csv, xml, ...
			  echo "<div class='action'>\n",  _T('asso:cpte_resultat_mode_exportation');
			  if (test_plugin_actif('FPDF')) { // impression en PDF
			    echo "<a href='".generer_action_auteur('pdf_comptesresultat', 0) ."'>PDF</a> ";
			  }
			  export_compte(array('id_periode' => 0, 'type_periode' => 'annee'), 'x', false) ;
			  echo "\n</div>";
			}
			echo fin_cadre_relief(TRUE);
		}
//		bilan_encaisse();
		fin_page_association();
	}
}

/* Dans la fonction suivante on dissocie la "lecture" et "l'affichage"
 * afin de pouvoir traiter et calculer des valeurs intermédiaires :
 * 1 - on ne comptabilise pour le terme "encaisse" que les sommes dont le journal est 53xx ou 51xx
 * 2 - si "imputation" vaut 58xx : c'est un virement interne et le solde du compte doit etre a zéro
 *		sinon il y a une erreur !!
 * 3 - si "imputation" vaut 86xx ou 87xx : c'est une contribution volontaire ... il est preferable que
 *		que les comptes 86 et 87 s'equilibrent. Faire apparaitre dans le "bilan" uniquement le cas ou il
 *		y a desequilibre !
 */
function bilan_encaisse() {
	$lesEcritures = array();
	$lesEcritures['_58xx']['solde'] = $lesEcritures['_86xx']['solde'] = $lesEcritures['_87xx']['solde'] = 0;
	$query = sql_select('*', 'spip_asso_plan', '(classe='.sql_quote($GLOBALS['association_metas']['classe_banques']).' OR classe='.sql_quote($GLOBALS['association_metas']['classe_contributions_volontaires']).') AND active=1', '',  'code' );
	while ($val = sql_fetch($query)) {
		$lesEcritures[$val['code']] = array(
			'code' => $val['code'],
			'intitule' => $val['intitule'],
			'date_solde' => $val['date_anterieure'],
			'solde_anterieur' => $val['solde_anterieur'],
			'id_plan' => $val['id_plan'],
		); // on declare un tableau et on le rempli avec les donnees du compte
		$compte = sql_fetsel('SUM(recette) AS recettes, SUM(depense) AS depenses, date, imputation',
			'spip_asso_comptes',
			'date>='. sql_quote($lesEcritures[$val['code']]['date_solde']).' AND date<=NOW() AND (journal='. sql_quote($val['code']) .' OR imputation='. sql_quote($val['code']) .')', // ne pas comptabiliser les opérations au delà d'aujourd'hui meme si il y a des echeances futures !!!!
			'journal');
		if ($compte) {
			if(substr($compte['imputation'],0,1)===$GLOBALS['association_metas']['classe_contributions_volontaires']) { // c'est une contribution volontaire du type 8xxx : c'est une dépense evaluee si 86xx ou recette évaluée si 87xx qui doit apparaître dans le compte de resultat
				$lesEcritures['_86xx']['solde'] += $compte['depenses'];
				$lesEcritures['_87xx']['solde'] += $compte['recettes'];
			} else { // c'est une recette ou une depense
				$lesEcritures[$code]['solde'] = $compte['recettes']-$compte['depenses'];
				if (substr($compte['imputation'],0,1)===$GLOBALS['association_metas']['classe_banques']) // c'est un virement interne avec le code 58xx : le solde du compte doit être à zero sinon il y a erreur !
					$lesEcritures['_58xx']['solde'] += $compte['recettes']-$compte['depenses'];
			}
		}
	}
	echo debut_cadre_relief('', TRUE, '', _T('asso:encaisse') );
	echo "<table width='100%' class='asso_tablo' id='asso_tablo_bilan_encaisse'>\n";
	echo '<tr class="row_first">';
	echo '<th colspan="2">&nbsp;</th>';
	echo '<th>'. _T('asso:avoir_initial') .'</th>';
	echo '<th>'. _T('asso:avoir_actuel') .'</th>';
	echo "</tr>\n";
	$total_actuel = $total_initial = 0;
	foreach($lesEcritures as $compteFinancier) {
		if( substr($compteFinancier['code'],0,1)==$GLOBALS['association_metas']['classe_banques'] ) { //!\ Tous les comptes financiers ne sont normalement pas concernes : idealement il aurait fallu configurer un groupe "caisse" (51xx) et un groupe "banque" (53xx) mais d'une part nous ignorons si d'autres systemes comptables n'utilisent pas plus de groupes et d'autre part (meme une association francaise) peut bien ne pas avoir les deux types de comptes...
			echo '<tr id="'.$compteFinancier['id_plan'].'">';
			echo '<td class="text">'. $compteFinancier['code'] .' : '. $compteFinancier['intitule'] .'</td>';
			echo '<td class="date">'. association_formater_date($compteFinancier['date_anterieure'],'dtstart') .'</td>';
			echo '<td class="decimal">'. association_formater_prix($compteFinancier['solde_anterieur']) .'</td>';
			echo '<td class="decimal">'. association_formater_prix($compteFinancier['solde_anterieur']+$compteFinancier['solde']) .'</td>';
			$total_initial += $compteFinancier['solde_anterieur'];
			$total_actuel += $compteFinancier['solde_anterieur']+$compteFinancier['solde'];
		} else {
			if($compteFinancier['_86xx']['solde']!=$compteFinancier['_87xx']['solde']) {
				$erreur8687=TRUE;
			}
		}
	}
	echo "</tr>\n<tr class='row_last'>";
	echo '<th  colspan="2" class="text">'. _T('asso:encaisse') .'</th>';
	echo '<th class="decimal">'. association_formater_prix($total_initial) .'</th>';
	echo '<th class="decimal">'. association_formater_prix($total_actuel) .'</th>';
	if( $compteFinancier['_58xx']['solde']!=0 ) {
		echo '<td  colspan="4" class="erreur">'. _T('asso:erreur_equilibre_comptes58') .'</td>';
	}
	if( $compteFinancier['_86xx']['solde']!=$compteFinancier['_87xx']['solde'] ) {
		echo '<td  colspan="4" class="erreur">'. _T('asso:erreur_equilibre_comptes8687') .'</td>';
	}
	echo "</tr>\n</table>\n";
	echo fin_cadre_relief(TRUE);
}

?>
