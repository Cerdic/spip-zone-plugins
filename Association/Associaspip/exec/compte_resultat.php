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

function exec_compte_resultat() {

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$plan = sql_countsel('spip_asso_plan');
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
		$url_bilan = generer_url_ecrire('bilan', "exercice=$exercice");
		$url_annexe = generer_url_ecrire('annexe', "exercice=$exercice");
		$res = association_icone(_T('asso:bilan'), generer_url_ecrire('bilan', "exercice=$exercice"), 'finances.jpg');
		$res .= association_icone(_T('asso:annexe_titre_general'), generer_url_ecrire('annexe', "exercice=$exercice"), 'finances.jpg');
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
		echo "<table border='0' cellpadding='2' cellspacing='6' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>";
		echo "<tr style='background-color: #DBE1C5;'><td>";
		$depenses = compte_resultat_charges_produits($var, intval($GLOBALS['association_metas']['classe_charges']));
		echo '</td></tr>';
		echo "<tr style='background-color: #DBE1C5;'><td>";
		$recettes = compte_resultat_charges_produits($var, intval($GLOBALS['association_metas']['classe_produits']));
		echo '</td></tr>';
		echo "<tr style='background-color: #DBE1C5;'><td>";
		compte_resultat_benefice_perte($recettes, $depenses);
		echo '</td></tr>';
		echo "<tr style='background-color: #DBE1C5;'><td>";
		compte_resultat_benevolat($var, intval($GLOBALS['association_metas']['classe_contributions_volontaires']));
		echo "</td></tr></table>";
		/* si plan on peut exporter en pdf, cs, xml, ..... */
		if($plan){
			echo "<br /><table cellpadding='2' cellspacing='6' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>";
			echo "<tr style='background-color: #DBE1C5;'>";
			echo "<td style='text-align:right;'>"._T('asso:cpte_resultat_mode_exportation')."</td>";
			if (test_plugin_actif('FPDF')) { // export en PDF
				echo "<td style='text-align:center;'><a href='".generer_url_ecrire('export_compte_resultat_pdf', "var=$var")."'><strong>PDF</strong></td>";
			}
			foreach(array('pdf','csv','xml') as $type) { // autres exports possibles
				$h = generer_url_ecrire('export_compte_resultat_'.$type, "var=$var");
				echo "<td style='text-align:center;'><a href='$h'><strong>".strtoupper($type)."</strong></td>";
			}
			echo '</tr></table>';
		}
		fin_cadre_relief();
		echo fin_page_association();
	}
}

function compte_resultat_charges_produits($var, $class) {
	include_spip('inc/association_plan_comptable');
	$tableau = @unserialize($var);
	$exercice = $tableau[0];$join = $tableau[1];$sel = $tableau[2];$where=$tableau[3];$having = $tableau[4];$order = $tableau[5];
	echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>";
	echo "<tr style='background-color: #DBE1C5;'>";
	echo "<td width='10'><strong>&nbsp;</strong></td>";
	echo "<td width='30'><strong>&nbsp;</strong></td>";
	echo "<td><strong>" . (($class==$GLOBALS['association_metas']['classe_charges']) ? _T('asso:cpte_resultat_titre_charges') : _T('asso:cpte_resultat_titre_produits')) . "</strong></td>";
	echo "<td width='80'><strong>&nbsp;</strong></td>";
	echo "</tr>";
	$quoi = (($class==$GLOBALS['association_metas']['classe_charges']) ? 'SUM(depense) AS valeurs' : 'SUM(recette) AS valeurs');
	$query = sql_select(
		"imputation, $quoi, date_format(date, '%Y') AS annee $sel",
		"spip_asso_comptes $join",
		$where, $order, 'code ASC', '', $having.$class);
	$total = 0;
	$chapitre = '';
	$i = 0;
	while ($data = sql_fetch($query)) {
		echo '<tr style="background-color: #EEEEEE;">';
		$valeurs = $data['valeurs'];
		$new_chapitre = substr($data['code'], 0, 2);
		if ($chapitre!=$new_chapitre) {
			echo "<td class='arial11 border1'>" . $new_chapitre . '</td>';
			echo "<td colspan='3' class='arial11 border1'>" . association_plan_comptable_complet($new_chapitre) . '</td>';
			$chapitre = $new_chapitre;
			echo '</tr><tr style="background-color: #EEEEEE;">';
		}
		echo "<td>&nbsp;</td>";
		echo '<td class="text">'. $data['code'] .'</td>';
		echo '<td class="text">'. $data['intitule'] .'</td>';
		echo '<td class="decimal">'.association_nbrefr($valeurs) .'</td>';
		echo '</tr>';
		$total += $valeurs;
	}
	echo '<tr style="background-color: #EEEEEE;">';
	echo "<td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td class='arial11 border1' style='text-align:right;color: #9F1C30;'><strong>" . (($class==$GLOBALS['association_metas']['classe_charges']) ? _T('asso:cpte_resultat_total_charges') : _T('asso:cpte_resultat_total_produits')) . '</strong></td>';
	echo '<th class="decimal">'. association_nbrefr($total) . '</th>';
	echo "</tr>";
	echo '</table>';
	return $total;
}

function compte_resultat_benefice_perte($recettes, $depenses) {
	echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>";
	echo "<tr style='background-color: #DBE1C5;'>";
	echo "<td width='30'><strong>&nbsp;</strong></td>";
	$res = $recettes-$depenses;
	echo "<td class='arial11 border1' style='text-align:right;color: #9F1C30;'><strong>" . (($res<0) ? _T('asso:cpte_resultat_perte') : _T('asso:cpte_resultat_benefice')) . "</strong></td>";
	echo '<td width="80" class="decimal">'. association_nbrefr($res) .'</td>';
	echo "</tr></table>";
}

function compte_resultat_benevolat($var, $class) {
	$tableau = @unserialize($var);
	$exercice = $tableau[0];$join = $tableau[1];$sel = $tableau[2];$where=$tableau[3];$having = $tableau[4];$order = $tableau[5];
	echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>";
	echo "<tr style='background-color: #DBE1C5;'>";
	echo "<td width='10'><strong>&nbsp;</strong></td>";
	echo "<td width='30'><strong>&nbsp;</strong></td>";
	echo '<td><strong>' . _T('asso:cpte_resultat_titre_benevolat') . '</strong></td>';
	echo "<td width='80' style='text-align:right;'><strong>"._T('asso:cpte_resultat_recette_evaluee')."</strong></td>";
	echo "<td width='80' style='text-align:right;'><strong>"._T('asso:cpte_resultat_depense_evaluee')."</strong></td>";
	$query = sql_select(
		"imputation, SUM(recette) AS recettes, SUM(depense) AS depenses, date_format(date, '%Y') AS annee $sel",
		"spip_asso_comptes $join",
		$where, $order, 'code ASC', '', $having.$class);
	$chapitre = '';
	$total_recettes = $total_depenses = 0;
	while ($data = sql_fetch($query)) {
		$recettes = $data['recettes'];
		$depenses = $data['depenses'];
		echo '<tr style="background-color: #EEEEEE;">';
		$new_chapitre = substr($data['code'], 0, 2);
		if ($chapitre!=$new_chapitre) {
			echo "<td class='arial11 border1'>" . $new_chapitre . '</td>';
			echo "<td colspan='4' class='arial11 border1'>" . association_plan_comptable_complet($new_chapitre) . '</td>';
			$chapitre = $new_chapitre;
			echo '</tr><tr style="background-color: #EEEEEE;">';
		}
		echo '<td>&nbsp;</td>';
		echo '<td class="text">'. $data['code'] .'</td>';
		echo '<td class="text">'. $data['intitule'] .'</td>';
		echo '<td class="decimal">'. association_nbrefr($recettes) .'</td>';
		echo '<td class="decimal">'. association_nbrefr($depenses) .'</td>';
		echo '</tr>';
		$total_recettes += $recettes;
		$total_depenses += $depenses;
	}
	$total_recettes = association_nbrefr($total_recettes);
	$total_depenses = association_nbrefr($total_depenses);
	echo '<tr style="background-color: #EEEEEE;">';
	echo '<th class="decimal" colspan="3">'. _T('asso:resultat_courant') .'</th>';
	echo '<th class="decimal">'. $total_recettes .'</th>';
	echo '<th class="decimal">'. $total_depenses .'</th>';
	echo "</tr>";
	echo '</table>';
}

?>