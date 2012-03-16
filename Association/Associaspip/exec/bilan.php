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

function exec_bilan()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$plan = sql_countsel('spip_asso_plan');
		$exercice = intval(_request('exercice'));
		if(!$exercice){
			/* on recupere l'id_exercice dont la date "fin" est "la plus grande" */
			$exercice = sql_getfetsel('id_exercice','spip_asso_exercices','','','fin DESC');
			if(!$exercice)
				$exercice=0;
		}
		$exercice_data = sql_asso1ligne('exercice', $exercice);
		// recupere l'id_destination de la ou des destinations dans POST ou cree une entree a 0 dans le tableau
		if (!($ids_destination_bilan = _request('destination')))
			$ids_destination_bilan = array(0);
		association_onglets(_T('asso:titre_onglet_comptes'));
		// INTRO : rappel de l'exercicee affichee
		echo totauxinfos_intro($exercice_data['intitule'],'exercice',$exercice);
		if ($GLOBALS['association_metas']['destinations']=='on') {
			// cree un menu a choix multiple des destinations a inserer dans la boite info et recupere les intitule de toutes les destinations dans un tableau
			$select_destination = '';
			$intitule_destinations = array();
			$query = sql_select('id_destination, intitule', 'spip_asso_destination', '', '', 'intitule');
			while ($data = sql_fetch($query)) {
				$select_destination .= '<option value="'.$data['id_destination'].'"';
				if (!(array_search($data['id_destination'], $ids_destination_bilan)===FALSE))
					$select_destination .= ' selected="selected"';
				$select_destination .= '>'.$data['intitule'].'</option>';
				$intitule_destinations[$data['id_destination']] = $data['intitule'];
			}
			echo '<form method="post" action="'.generer_url_ecrire('bilan', "exercice=$exercice").'"><div>';
			echo '<select name ="destination[]" class="fondl" multiple>';
			echo '<option value="0"';
			if (!(array_search(0, $ids_destination_bilan)===FALSE))
				echo ' selected="selected"';
			echo '>'._T('asso:bilan_total').'</option><option disabled="disabled">--------</option>'.$select_destination;
			echo '</select>';
			echo '<p class="boutons"><input type="submit" value="Bilan" /></p>';
			echo '</div></form>';
		}
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone('cpte_resultat_titre_general',  generer_url_ecrire('compte_resultat', "exercice=$exercice"), 'finances.jpg')
		. association_icone('annexe_titre_general',  generer_url_ecrire('annexe', "exercice=$exercice"), 'finances.jpg')
		. association_icone('bouton_retour',  generer_url_ecrire('comptes', "exercice=$exercice"), 'retour-24.png');
		echo bloc_des_raccourcis($res);
		debut_cadre_association('finances.jpg', 'bilans_comptables', $exercice_data['intitule']);
		$clas_banque = $GLOBALS['association_metas']['classe_banques'];
		$clas_contrib_volontaire = $GLOBALS['association_metas']['classe_contributions_volontaires']; // une contribution benevole ne doit pas etre comptabilisee en charge/produit
		if ($plan) {
			$join = ' RIGHT JOIN spip_asso_plan ON imputation=code';
			$sel = ', code, intitule, classe';
			$where = ' date >= \''.exercice_date_debut($exercice).'\' AND date <= \''.exercice_date_fin($exercice).'\'';
			$having =  'classe <> \'' . sql_quote($clas_banque). '\' AND classe <> \'' .sql_quote($clas_contrib_volontaire) .'\'';
			$order = 'code';
		} else {
			$join = $sel = $where = $having = $order = '';
		}
		// on boucle sur le tableau des destinations en refaisant le fetch a chaque iteration
		foreach ($ids_destination_bilan as $id_destination) {
			$total_recettes = $total_depenses = $total_soldes = 0;
			//TABLEAU EXPLOITATION
			if ($id_destination!=0) {
				$intitule_destination_bilan = $intitule_destinations[$id_destination];
			} else {
				if ($GLOBALS['association_metas']['destinations']=='on') $intitule_destination_bilan = _T('asso:toutes_destination');
			}
			echo "\n<fieldset>";
			echo '<legend><strong>'. _T('asso:resultat_courant') . ' ' .$intitule_destination_bilan. '</strong></legend>';
			echo "<table width='100%' class='asso_tablo' id='asso_tablo_bilan_resultat'>\n";
			echo "<thead>\n<tr>";
			echo '<th colspan="2">&nbsp;</th>';
			echo '<th width="50">'. _T('asso:bilan_recettes') .'</th>';
			echo '<th width="50">'. _T('asso:bilan_depenses') .'</th>';
			echo '<th width="50">'. _T('asso:bilan_solde').'</th>';
			echo "</tr>\n</thead><tbody>";
			// si on fait le bilan sur toutes les destinations (quand aucune de selectionnee ou que destination n'est pas on)
			if ($id_destination==0) {
				$query = sql_select(
					"imputation, SUM(recette) AS recettes, SUM(depense) AS depenses, DATE_FORMAt(date, '%Y') AS annee $sel",
					"spip_asso_comptes $join",
					$where, $order, '', '', $having);
				while ($data = sql_fetch($query)) {
					$recettes = $data['recettes'];
					$depenses = $data['depenses'];
					$solde = $recettes-$depenses;
					echo '<tr>';
					echo '<td class="text">'. $data['code'] . '</td>';
					echo '<td class="text">'. $data['intitule'] .'</td>';
					echo '<td class="decimal">'.association_prixfr($recettes).'</td>';
					echo '<td class="decimal">'.association_prixfr($depenses).'</td>';
					echo '<td class="decimal">'.association_prixfr($solde).'</td>';
					echo "</tr>\n";
					$total_recettes += $recettes;
					$total_depenses += $depenses;
					$total_soldes += $solde;
				}
			} else { // on fait le bilan d'une seule destination
				$query = sql_select(
					"imputation, DATE_FORMAT(date, '%Y') AS annee,
					SUM(spip_asso_destination_op.recette) AS recettes,
					SUM(spip_asso_destination_op.depense) AS depenses,
					spip_asso_destination_op.id_destination $sel",
					"spip_asso_comptes LEFT JOIN spip_asso_destination_op ON spip_asso_destination_op.id_compte=spip_asso_comptes.id_compte $join",
					"spip_asso_destination_op.id_destination=$id_destination AND $where",
					$order, '', '', $having);
				while ($data = sql_fetch($query)) {
					$recettes = $data['recettes'];
					$depenses = $data['depenses'];
					$solde = $recettes-$depenses;
					echo '<tr>';
					echo '<td class="text">'. $data['code'] .'</td>';
					echo '<td class="text">'. $data['intitule'] .'</td>';
					echo '<td class="decimal">'.association_prixfr($recettes).'</td>';
					echo '<td class="decimal">'.association_prixfr($depenses).'</td>';
					echo '<td class="decimal">'.association_prixfr($solde).'</td>';
					echo "</tr>\n";
					$total_recettes += $recettes;
					$total_depenses += $depenses;
					$total_soldes += $solde;
				}
			}
			echo "</tbody><tfoot>\n<tr>";
			echo '<th colspan="2">'. _T('asso:resultat_courant') .'</th>';
			echo '<th class="decimal">'. association_prixfr($total_recettes) .'</th>';
			echo '<th class="decimal">'. association_prixfr($total_depenses) .'</th>';
			echo '<th class="decimal">'. association_prixfr($total_soldes) .'</th>';
			echo "</tr>\n</tfoot>\n</table>\n";
			echo '</fieldset>';
		}
		if ($plan)
			bilan_encaisse();
		fin_page_association();
	}
}

/* Dans la fonction suivante "bilan_encaisse($annee)" on dissocie la "lecture" et "l'affichage"
 * afin de pouvoir traiter et calculer des valeurs intermédiaires :
 * 1 - on ne comptabilise pour le terme "encaisse" que les sommes dont le journal est 53xx ou 51xx
 * 2 - si "imputation" vaut 58xx : c'est un virement interne et le solde du compte doit etre a zéro
 *		sinon il y a une erreur !!
 * 3 - si "imputation" vaut 86xx ou 87xx : c'est une contribution volontaire ... il est preferable que
 *		que les comptes 86 et 87 s'equilibrent. Faire apparaitre dans le "bilan" uniquement le cas ou il
 *		y a desequilibre !
 */

// TODO : le passage en exercice budgétaire - 1)date de fin de l'exercice 2)faire apparaitre l'avoir initial : qui est le solde à (debut -1) de l'exercice (report)

function bilan_encaisse()
{
	$lesEcritures = array();
	$laDateDuJour = date('Y-m-d'); # pour ne pas comptabiliser les opérations au delà d'aujourd'hui !!!!
	$laClasseBanque = $GLOBALS['association_metas']['classe_banques'];
	$laClasseContributionVolontaire = $GLOBALS['association_metas']['classe_contributions_volontaires'];
	$query = sql_select('*', 'spip_asso_plan', '(classe='.sql_quote($laClasseBanque).' OR classe='.sql_quote($laClasseContributionVolontaire).') AND active=1', '',  'code' );
	while ($val = sql_fetch($query)) {
		$code = $val['code'];
		/* on declare un tableau et on le rempli avec les donnees du compte */
		$lesEcritures[$code] = array();
		$lesEcritures[$code]['code'] = $val['code'];
		$lesEcritures[$code]['intitule'] = $val['intitule'];
		$lesEcritures[$code]['date_solde'] = $val['date_anterieure'];
		$lesEcritures[$code]['solde_anterieur'] = $val['solde_anterieur'];
		/* ne pas comptabiliser les opérations au delà d'aujourd'hui meme si il y a des echeances futures !!!! */
		$compte = sql_fetsel('SUM(recette) AS recettes, SUM(depense) AS depenses, date, imputation',
			'spip_asso_comptes',
			'date >= '.sql_quote($lesEcritures[$code]['date_solde']).' AND date <= '.sql_quote($laDateDuJour).' AND (journal = '.sql_quote($code).' OR imputation = '.sql_quote($code).')',
			'journal');
		if ($compte) {
			if(substr($compte['imputation'],0,1)===$GLOBALS['association_metas']['classe_contributions_volontaires']) {
				/* c'est une contribution volontaire du type 8xxx :
				 * c'est une dépense evaluee si 86xx ou recette évaluée si 87xx
				 * qui doit apparaître dans le compte de resultat
				 */
				$lesEcritures['86xx']['solde'] = $compte['depenses'];
				$lesEcritures['87xx']['solde'] = $compte['recettes'];
			} elseif (substr($compte['imputation'],0,1)===$GLOBALS['association_metas']['classe_banques']) {
				/* c'est un virement interne avec le code 58xx :
				 * le solde du compte doit être à zero sinon il y a erreur !
				 */
				 $lesEcritures['58xx']['solde'] = $compte['recettes']-$compte['depenses'];
			} else {
				/* c'est une recette ou une depense */
				$lesEcritures[$code]['solde'] = $compte['recettes']-$compte['depenses'];
			}
		}
	}
	echo "\n<fieldset>";
	echo '<legend><strong>' . _T('asso:encaisse') . '</strong></legend>';
	echo "<table width='100%' class='asso_tablo' id='asso_tablo_bilan_encaisse'>\n";
	echo "<thead>\n<tr>";
	echo '<th colspan="2">&nbsp;</th>';
	echo '<th>'. _T('asso:avoir_initial') .'</th>';
	echo '<th>'. _T('asso:avoir_actuel') .'</th>';
	echo "</tr>\n</thead><tbody>";
	$total_actuel = $total_initial = 0;
	foreach($lesEcritures as $compteFinancier) {
		/* c'est un compte banque 53xx ou un compte caisse 51xx */
		if(substr($compteFinancier['code'],0,2)==='51' OR substr($compteFinancier['code'],0,2)==='53') { //!! Parametrer !!! Pas de valeurs en dures sinon ca ne sera valable que pour la France
			echo '<tr>';
			echo '<td class="text">'. $compteFinancier['code'] .' : '. $compteFinancier['intitule'] .'</td>';
			echo '<td class="date">'. association_datefr($compteFinancier['date_solde'],'dtstart') .'</td>';
			echo '<td class="decimal">'. association_prixfr($compteFinancier['solde_anterieur']) .'</td>';
			echo '<td class="decimal">'. association_prixfr($compteFinancier['solde_anterieur']+$compteFinancier['solde']) .'</td>';
			$total_initial += $compteFinancier['solde_anterieur'];
			$total_actuel += $compteFinancier['solde_anterieur']+$compteFinancier['solde'];
		} else {
			if($compteFinancier['58xx']['solde']!=0) {
				$erreur58=TRUE;
				$messageErreur58 = _L("Attention : Virement interne non &eacute;quilibr&eacute; !");
			}
			if($compteFinancier['86xx']['solde']!=$compteFinancier['87xx']['solde']) {
				$erreur8687=TRUE;
				$messageErreur8687 = _L("Attention : Comptes 86xx et 87xx ne sont pas &eacute;quilibr&eacute;s !");
			}
		}
	}
	echo "</tr>\n</tbody><tfoot>\n<tr>";
	echo '<th  colspan="2" class="text">'. _T('asso:encaisse') .'</th>';
	echo '<th class="decimal">'. association_prixfr($total_initial) .'</th>';
	echo '<th class="decimal">'. association_prixfr($total_actuel) .'</th>';
	echo "</tr>\n</tfoot>\n</table>\n";
	if($erreur58){
		echo $messageErreur58;
	}
	if($erreur8687){
		echo $messageErreur8687;
	}
	echo '</fieldset>';
}

?>