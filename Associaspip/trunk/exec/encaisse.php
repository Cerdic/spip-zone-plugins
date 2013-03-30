<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_encaisse() {
	if (!autoriser('voir_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('association_modules');
// initialisations
		$plan = sql_countsel('spip_asso_plan');
		$id_exercice = association_passeparam_exercice();
// traitements
		echo association_navigation_onglets('titre_onglet_comptes', 'comptes');
		// INTRO : rappel de l'exercicee affichee
		echo association_totauxinfos_intro('encaisse', '');
		// STATS recettes et depenses par comptes financiers (indique rapidement les comptes financiers avec les mouvements les plus importants --en montant !)
		$journaux = sql_allfetsel('journal, intitule', 'spip_asso_comptes RIGHT JOIN spip_asso_plan ON journal=code', "date_operation>=date_anterieure AND date_operation<=NOW()", "intitule DESC"); // on se permet sql_allfetsel car il n'y en a pas des masses a priori...
		foreach ($journaux as $financier) {
			echo association_totauxinfos_stats($financier['intitule'], 'comptes', array('bilan_recettes'=>'recette','bilan_depenses'=>'depense',), 'journal='.sql_quote($financier['journal']) .' AND date_operation>='. sql_quote($financier['date_anterieure']) .' AND date_operation<=NOW()');
		}
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			'informations_comptables' => array('grille-24.png', array('comptes', "$ids[type_periode]=$ids[id_periode]"), array('gerer_compta', 'association') ),
			'cpte_resultat_titre_general' => array('finances-24.png', array('compte_resultat', "exercice=$id_exercice") ),
			'cpte_bilan_titre_general' => array('finances-24.png', array('compte_bilan', "exercice=$id_exercice") ),
#			'annexe_titre_general' => array('finances-24.png', array('annexe', "exercice=$id_exercice") ),
		), 15);
		debut_cadre_association('finances-24.png', 'encaisse');
		$lesEcritures = array(); // initialiser le tableaux des ecritures a afficher
		// Recuperer les comptes financiers avec toutes les informations dont on aura besoin
		$encaisses = sql_select(
			'a_p.id_plan, a_p.code, a_p.intitule, a_p.date_anterieure, a_p.solde_anterieur, SUM(a_c.recette) AS recettes, SUM(a_c.depense) AS depenses, SUM(a_c.recette-a_c.depense) AS solde_actuel ', // select
			'spip_asso_comptes AS a_c INNER JOIN spip_asso_plan AS a_p ON a_c.journal=a_p.code', // from
			'a_p.classe='. sql_quote($GLOBALS['association_metas']['classe_banques']) .' AND LEFT(a_c.imputation,1)<>'. sql_quote($GLOBALS['association_metas']['classe_contributions_volontaires']) .' AND a_p.active=1 AND a_c.date_operation>=a_p.date_anterieure AND a_c.date_operation<=NOW() ', // where
			'a_c.journal', // group by
			'a_p.code', // order by
			'', // limit
			'' // having
		); // cette requete ne recupere que les comptes financiers utilises dans les journaux et on n'a donc pas les comptes dormants/inactifs...
		/* Completer le tableau des ecritures avec les informations recuperees */
		while ($val = sql_fetch($encaisses)) {
			$lesEcritures[$val['code']] = $val; // on recupere les informations de la requete
#			$lesEcritures[$val['code']]['solde_actuel'] = $val['recettes']-$val['depenses']; // on ajoute la donnee du solde des flux sur la periode
		}
		// Afficher les releves de situation des encaisses /!\ Tous les comptes financiers ne sont normalement pas concernes : idealement il aurait fallu configurer un groupe "caisse" (51xx) et un groupe "banque" (53xx) mais d'une part nous ignorons si d'autres systemes comptables n'utilisent pas plus de groupes et d'autre part (meme une association francaise) peut bien ne pas avoir les deux types de comptes...
		echo "\n<table width='100%' class='asso_tablo' id='asso_tablo_encaisse'>\n";
		echo "<tr>";
		echo "<th colspan='2'>&nbsp;</th>\n";
		echo '<th>'. _T('asso:avoir_initial') ."</th>\n";
		echo '<th>'. _T('asso:avoir_actuel') ."</th>\n";
		echo "</tr>\n";
		$total_actuel = $total_initial = 0;
		foreach($lesEcritures as $compteFinancier) {
			echo '<tr>';
			echo '<td class="text">'. $compteFinancier['code'] .' : '. $compteFinancier['intitule'] ."</td>\n";
			echo '<td class="date">'. association_formater_date($compteFinancier['date_anterieure'],'dtstart') ."</td>\n";
			echo '<td class="decimal">'. association_formater_prix($compteFinancier['solde_anterieur']) ."</td>\n";
			echo '<td class="decimal">'. association_formater_prix($compteFinancier['solde_anterieur']+$compteFinancier['solde_actuel']) ."</td>\n";
			echo "</tr>\n";
			$total_initial += $compteFinancier['solde_anterieur'];
			$total_actuel += $compteFinancier['solde_anterieur']+$compteFinancier['solde_actuel'];
		} // fin corps
		echo "<tr>";
		echo '<th  colspan="2" class="text">'. _T('asso:encaisse_total_general') ."</th>\n";
		echo '<th class="decimal">'. association_formater_prix($total_initial) ."</th>\n";
		echo '<th class="decimal">'. association_formater_prix($total_actuel) ."</th>\n";
		$solde_virementsinternes = sql_getfetsel('SUM(recette)-SUM(depense)', 'spip_asso_comptes', 'imputation='.sql_quote($GLOBALS['association_metas']['pc_intravirements']), 'imputation');
		if( $solde_virementsinternes!=0 ) { // desequilible du compte de virements internes (ceci ne devrait arriver que si l'operation n'est pas enregistree via ce plugin !) /!\ Attention a bien forcer la comparaison avec zero car '0.00' sera faux !
			echo '</tr><tr class="erreur"><td  colspan="3" class="message_erreur">'. _T('asso:erreur_equilibre_comptes58') .'</td><td class="decimal">'. association_formater_prix($solde_virementsinternes) .'</td></tr>';
		}
		echo "</tr>\n</table>\n";
		fin_page_association();
	}
}

?>