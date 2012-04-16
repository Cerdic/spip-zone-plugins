<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_encaisse()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$plan = sql_countsel('spip_asso_plan');
		$exercice = intval(_request('exercice'));
		if(!$exercice){ // on recupere l'id_exercice dont la date "fin" est "la plus grande"
			$exercice = sql_getfetsel('id_exercice','spip_asso_exercices','','','fin DESC');
			if(!$exercice)
				$exercice = 0;
		}
		onglets_association('titre_onglet_comptes');
		// INTRO : rappel de l'exercicee affichee
		echo totauxinfos_intro('encaisse', '');
		// STATS recettes et depenses par comptes financiers (indique rapidement les comptes financiers avec les mouvements les plus importants --en montant !)
		$journaux = sql_allfetsel('journal, intitule', 'spip_asso_comptes RIGHT JOIN spip_asso_plan ON journal=code', "date>=date_anterieure AND date<=NOW()", "intitule DESC"); // on se permet sql_allfetsel car il n'y en a pas des masses a priori...
		foreach ($journaux as $financier) {
			echo totauxinfos_stats($financier['intitule'], 'comptes', array('bilan_recettes'=>'recette','bilan_depenses'=>'depense',), 'journal='.sql_quote($financier['journal']) .' AND date>='. sql_quote($financier['date_anterieure']) .' AND date<=NOW()');
		}
		// datation et raccourcis
		icones_association(array('comptes', "exercice=$exercice"), array(
			'cpte_resultat_titre_general' => array('finances-24.png', 'compte_resultat', "exercice=$exercice"),
			'cpte_bilan_titre_general' => array('finances-24.png', 'bilan', "exercice=$exercice"),
#			'annexe_titre_general' => array('finances-24.png', 'annexe', "exercice=$exercice"),
		));
		debut_cadre_association('finances-24.png', 'encaisse');
		/* initialiser le tableaux des ecritures a afficher  avec les comptes de controle*/
		$lesEcritures = array(
			'_58xx' => array('code'=>'_58xx', 'intitule'=>_T('asso:virement_interne') ),
#			'_86xx' => array('code'=>'_86xx', 'intitule'=>_T('asso:cpte_resultat_recette_evaluee') ),
#			'_87xx' => array('code'=>'_87xx', 'intitule'=>_T('asso:cpte_resultat_depense_evaluee') ),
		); // renseigner "code" (et optionnellement "intitule") pour debogage...
		$lesEcritures['_58xx']['solde'] = $lesEcritures['_86xx']['solde'] = $lesEcritures['_87xx']['solde'] = 0; // initialiser "solde" a "0" pour (de)crementer plus loin...
		/* Recuperer les comptes financiers avec toutes les informations dont on aura besoin */
		$encaisses = sql_select(
			'a_p.id_plan, a_p.code, a_p.intitule, a_p.date_anterieure, a_p.solde_anterieur, SUM(a_c.recette) AS recettes, SUM(a_c.depense) AS depenses, a_c.date, a_c.imputation ', // select
			'spip_asso_comptes AS a_c INNER JOIN spip_asso_plan AS a_p ON a_c.journal=a_p.code', // from
			'a_p.classe='. sql_quote($GLOBALS['association_metas']['classe_banques']) .' AND LEFT(a_c.imputation,1)<>'. sql_quote($GLOBALS['association_metas']['classe_contributions_volontaires']) .' AND a_p.active=1 AND a_c.date>=a_p.date_anterieure AND a_c.date<=NOW() ', // where
			'a_c.journal', // group by
			'a_p.code', // order by
			'', // limit
			'' // having
		); // cette requete ne recupere que les comptes financiers utilises dans les journaux et on n'a donc pas les comptes dormants/inactifs...
		/* Completer le tableau des ecritures avec les informations recuperees en faisant les controles necessaires au passage */
		while ($val = sql_fetch($encaisses)) {
			$lesEcritures[$val['code']] = $val; // on recupere les informations de la requete
			$lesEcritures[$val['code']]['solde'] = $val['recettes']-$val['depenses']; // on ajoute la donnee du solde des flux sur la periode
			if ($val['imputation']==$GLOBALS['association_metas']['pc_intravirements']) {
				$lesEcritures['_58xx']['solde'] += $val['recettes']-$val['depenses']; // virement interne : le solde du compte doit être à zero sinon il y a erreur !
//				$lesEcritures[$val['code']]['solde'] = $val['depenses']-$val['recettes']; // attention au sens ?
			}
		}
		/* Afficher les releves de situation des encaisses /!\ Tous les comptes financiers ne sont normalement pas concernes : idealement il aurait fallu configurer un groupe "caisse" (51xx) et un groupe "banque" (53xx) mais d'une part nous ignorons si d'autres systemes comptables n'utilisent pas plus de groupes et d'autre part (meme une association francaise) peut bien ne pas avoir les deux types de comptes... */
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_encaisse'>\n";
		echo "<thead>\n<tr>";
		echo '<th colspan="2">&nbsp;</th>';
		echo '<th>'. _T('asso:avoir_initial') .'</th>';
		echo '<th>'. _T('asso:avoir_actuel') .'</th>';
		echo "</tr>\n</thead><tbody>";
		$total_actuel = $total_initial = 0;
		foreach($lesEcritures as $compteFinancier) {
			if($compteFinancier['code']) { // compte avec un "code" defini, donc defini dans le plan comptable ou active plus haut pour debogage...
				echo '<tr id="'.$compteFinancier['id_plan'].'">';
				echo '<td class="text">'. $compteFinancier['code'] .' : '. $compteFinancier['intitule'] .'</td>';
				echo '<td class="date">'. association_datefr($compteFinancier['date_anterieure'],'dtstart') .'</td>';
				echo '<td class="decimal">'. association_prixfr($compteFinancier['solde_anterieur']) .'</td>';
				echo '<td class="decimal">'. association_prixfr($compteFinancier['solde_anterieur']+$compteFinancier['solde']) .'</td>';
				$total_initial += $compteFinancier['solde_anterieur'];
				$total_actuel += $compteFinancier['solde_anterieur']+$compteFinancier['solde'];
			}
		} // fin corps
		echo "</tr>\n</tbody><tfoot>\n<tr>";
		echo '<th  colspan="2" class="text">'. _T('asso:encaisse') .'</th>';
		echo '<th class="decimal">'. association_prixfr($total_initial) .'</th>';
		echo '<th class="decimal">'. association_prixfr($total_actuel) .'</th>';
		if( $compteFinancier['_58xx']['solde'] ){ // desequilible du compte de virements internes (ceci ne devrait arriver que si l'operation n'est pas enregistree via ce plugin !)
			echo '<tr class="erreur"><td  colspan="4" class="message_erreur">'. _T('asso:erreur_equilibre_comptes58') .'</td></tr>';
		}
		echo "</tr>\n</tfoot>\n</table>\n";

		fin_page_association();
	}
}

?>