<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  ajouté en 08/2011 par Marcel BOLLA ... à partir de bilan.php           *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/presentation');
include_spip('inc/navigation_modules');

function exec_compte_resultat()
{
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$plan = sql_countsel('spip_asso_plan','active=1');
		$exercice= intval(_request('exercice'));
		if(!$exercice){
			/* on recupere l'id_exercice dont la date "fin" est "la plus grande" */
			$exercice = sql_getfetsel("id_exercice","spip_asso_exercices", '', '',"fin DESC");
			if(!$exercice) $exercice=0;
		}
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association'), "", _DIR_PLUGIN_ASSOCIATION_ICONES . 'finances.jpg', 'rien.gif');
		association_onglets(_T('asso:titre_onglet_comptes'));
		echo debut_gauche('', true);
		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone(_T('asso:bilan'), generer_url_ecrire('bilan', "exercice=$exercice"), 'finances.jpg');
		$res .= association_icone(_T('asso:annexe_titre_general'), generer_url_ecrire('annexe', "exercice=$exercice"), 'finances.jpg');
		$res .= association_icone(_T('asso:bouton_retour'),  generer_url_ecrire('comptes', "exercice=$exercice"), 'retour-24.png');
		echo bloc_des_raccourcis($res);
		echo debut_droite('', true);
		debut_cadre_relief(_DIR_PLUGIN_ASSOCIATION_ICONES . 'finances.jpg', false, '', '&nbsp;'.propre( _T('asso:cpte_resultat_titre_general').' : '.exercice_intitule($exercice)));
		if ($plan) {
			$join = ' RIGHT JOIN spip_asso_plan ON imputation=code';
			$sel = ', code, intitule, classe';
			$where = ' date >= \''.exercice_date_debut($exercice).'\' AND date <= \''.exercice_date_fin($exercice).'\'';
			$having = 'classe = ';
			$order = 'code';
		} else {
			$join = $sel = $where = $having = $order = '';
		}
		$var = @serialize(array($exercice, $join, $sel, $where, $having, $order));
#		echo "<table width='100%' class='asso_tablo' id='asso_tablo_compte_resultat'>\n";
#		echo '<tr><td>';
		$depenses = compte_resultat_charges_produits($var, intval($GLOBALS['association_metas']['classe_charges']));
#		echo '</td></tr>';
#		echo '<tr><td>';
		$recettes = compte_resultat_charges_produits($var, intval($GLOBALS['association_metas']['classe_produits']));
#		echo '</td></tr>';
#		echo '<tr><td>';
		compte_resultat_benefice_perte($recettes, $depenses);
#		echo '</td></tr>';
#		echo '<tr><td>';
		compte_resultat_benevolat($var, intval($GLOBALS['association_metas']['classe_contributions_volontaires']));
#		echo '</td></tr></table>';
		/* si plan on peut exporter en pdf, cs, xml, ..... */
		if($plan){
			echo "<br /><table width='100%' class='asso_tablo' cellspacing='6' id='asso_tablo_exports'>\n";
			echo '<tbody><tr>';
			echo '<td>'. _T('asso:cpte_resultat_mode_exportation') .'</td>';
			if (test_plugin_actif('FPDF')) { // export en PDF
				echo "<td class='action'><a href='".generer_url_ecrire('export_compte_resultat_pdf', "var=$var")."'><strong>PDF</strong></td>";
			}
			foreach(array('csv','xml') as $type) { // autres exports possibles
				echo "<td style='text-align:center;'><a href='". generer_url_ecrire('export_compte_resultat_'.$type, "var=$var") ."'><strong>". strtoupper($type) ."</strong></td>";
			}
			echo '</tr></tbody></table>';
		}
		fin_cadre_relief();
		echo fin_page_association();
	}
}

function compte_resultat_charges_produits($var, $class) {
	include_spip('inc/association_plan_comptable');
	$tableau = @unserialize($var);
	$exercice = $tableau[0];
	$join = $tableau[1];
	$sel = $tableau[2];
	$where = $tableau[3];
	$having = $tableau[4];
	$order = $tableau[5];
	$id_tableau = (($class==$GLOBALS['association_metas']['classe_charges']) ? 'charges' : 'produits');
	echo "<table width='100%' class='asso_tablo' id='asso_tablo_bilan_$id_tableau'>\n";
	echo "<thead>\n<tr>";
	echo '<th width="10">&nbsp;</td>';
	echo '<th width="30">&nbsp;</td>';
	echo '<th>'. (($class==$GLOBALS['association_metas']['classe_charges']) ? _T('asso:cpte_resultat_titre_charges') : _T('asso:cpte_resultat_titre_produits')) .'</th>';
	echo '<th width="80">&nbsp;</td>';
	echo "</tr>\n</thead><tbody>";
	$quoi = (($class==$GLOBALS['association_metas']['classe_charges']) ? 'SUM(depense) AS valeurs' : 'SUM(recette) AS valeurs');
	$query = sql_select(
		"imputation, $quoi, DATE_FORMAT(date, '%Y') AS annee $sel",
		"spip_asso_comptes $join",
		$where, $order, 'code ASC', '', $having.$class);
	$total = 0;
	$chapitre = '';
	$i = 0;
	while ($data = sql_fetch($query)) {
		echo '<tr>';
		$valeurs = $data['valeurs'];
		$new_chapitre = substr($data['code'], 0, 2);
		if ($chapitre!=$new_chapitre) {
			echo '<td class="text">'. $new_chapitre . '</td>';
			echo '<td colspan="3" class="text">'. association_plan_comptable_complet($new_chapitre) .'</td>';//!! verifier d'abord que ce n'est pas defini dans le plan comptable de l'usager avant d'appeler cette fonction, et penser a traduire les intitules d'autre part car c'est assez surprenant quand on sort de France
			$chapitre = $new_chapitre;
			echo "</tr>\n<tr>";
		}
		echo "<td>&nbsp;</td>";
		echo '<td class="text">'. $data['code'] .'</td>';
		echo '<td class="text">'. $data['intitule'] .'</td>';
		echo '<td class="decimal">'. association_nbrefr($valeurs) .'</td>';
		echo "</tr>\n";
		$total += $valeurs;
	}
	echo "</tbody><tfoot>\n<tr>";
	echo '<td>&nbsp;</td><td>&nbsp;</td>';
	echo '<th class="text">'. (($class==$GLOBALS['association_metas']['classe_charges']) ? _T('asso:cpte_resultat_total_charges') : _T('asso:cpte_resultat_total_produits')) .'</th>';
	echo '<th class="decimal">'. association_nbrefr($total) . '</th>';
	echo "</tr>\n</tfoot>\n</table>\n";
	return $total;
}

function compte_resultat_benefice_perte($recettes, $depenses) {
	echo "<table width='100%' class='asso_tablo' id='asso_tablo_bilan_solde'>\n";
	echo "<tfoot>\n<tr>";
	echo '<th width="30">&nbsp;</td>';
	$res = $recettes-$depenses;
	echo '<th class="text">'. (($res<0) ? _T('asso:cpte_resultat_perte') : _T('asso:cpte_resultat_benefice')) .'</th>';
	echo '<th width="80" class="decimal">'. association_nbrefr($res) .'</th>';
	echo "</tr></tfoot></table>";
}

function compte_resultat_benevolat($var, $class) {
	$tableau = @unserialize($var);
	$exercice = $tableau[0];$join = $tableau[1];$sel = $tableau[2];$where=$tableau[3];$having = $tableau[4];$order = $tableau[5];
	echo "<table width='100%' class='asso_tablo' id='asso_tablo_bilan_benevolat'>\n";
	echo "<thead>\n<tr>";
	echo '<th width="10">&nbsp;</th>';
	echo '<th width="30">&nbsp;</th>';
	echo '<th>'. _T('asso:cpte_resultat_titre_benevolat') . '</th>';
	echo '<th width="80">'. _T('asso:cpte_resultat_recette_evaluee') .'</th>';
	echo '<th width="80">'. _T('asso:cpte_resultat_depense_evaluee') .'</th>';
	$query = sql_select(
		"imputation, SUM(recette) AS recettes, SUM(depense) AS depenses, DATE_FORMAT(date, '%Y') AS annee $sel",
		"spip_asso_comptes $join",
		$where, $order, 'code ASC', '', $having.$class);
	$chapitre = '';
	$total_recettes = $total_depenses = 0;
	while ($data = sql_fetch($query)) {
		echo '<tr>';
		$new_chapitre = substr($data['code'], 0, 2);
		if ($chapitre!=$new_chapitre) {
			echo '<td class="text">' . $new_chapitre . '</td>';
			echo '<td colspan="4" class="text">'. association_plan_comptable_complet($new_chapitre) . '</td>'; //!! meme remarque...
			$chapitre = $new_chapitre;
			echo "</tr>\n";
		}
		echo '<td>&nbsp;</td>';
		echo '<td class="text">'. $data['code'] .'</td>';
		echo '<td class="text">'. $data['intitule'] .'</td>';
		echo '<td class="decimal">'. association_nbrefr($data['recettes']) .'</td>';
		echo '<td class="decimal">'. association_nbrefr($data['depenses']) .'</td>';
		echo '</tr>';
		$total_recettes += $data['recettes'];
		$total_depenses += $data['depenses'];
	}
	echo "</tbody><tfoot>\n<tr>";
	echo '<th width="10">&nbsp;</td>';
	echo '<th width="30">&nbsp;</td>';
	echo '<th class="decimal">'. _T('asso:resultat_courant') .'</th>';
	echo '<th class="decimal">'. association_nbrefr($total_recettes) .'</th>';
	echo '<th class="decimal">'. association_nbrefr($total_depenses) .'</th>';
	echo "</tr>\n</tfoot>\n</table>\n";
}

?>