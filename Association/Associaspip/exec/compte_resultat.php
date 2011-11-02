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


if (!defined("_ECRIRE_INC_VERSION"))
	return;

include_spip('inc/presentation');
include_spip('inc/navigation_modules');

function exec_compte_resultat() {

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	}
	else {
		$plan = sql_countsel('spip_asso_plan');

		if (!($annee = _request('annee'))) {
			$annee = date('Y');
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association'), "", _DIR_PLUGIN_ASSOCIATION_ICONES . 'finances.jpg', 'rien.gif');
		association_onglets(_T('asso:titre_onglet_comptes'));

		echo debut_gauche("", true);

		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);

		$url_bilan = generer_url_ecrire('bilan', "annee=$annee");
		$url_annexe = generer_url_ecrire('annexe', "annee=$annee");
		$res = association_icone(_T('asso:bilan') . " $annee", $url_bilan, 'finances.jpg')
			. association_icone(_T('asso:annexe_titre_general') . " $annee", $url_annexe, 'finances.jpg');
		echo bloc_des_raccourcis($res);

		echo debut_droite("", true);

		debut_cadre_relief(_DIR_PLUGIN_ASSOCIATION_ICONES . "finances.jpg", false, "", '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . propre(_T('asso:cpte_resultat_titre_general') . ' - ' . $annee));
		
		if ($plan) {
			$join = " RIGHT JOIN spip_asso_plan ON imputation=code";
			$sel = ", code, intitule, classe";
			$having = " AND classe = ";
			$order = "code,";
		}
		else {
			$join = $sel = $having = $order = '';
		}

		$var = @serialize(array($annee, $join, $sel, $having, $order));

		echo "<table border='0' cellpadding='2' cellspacing='6' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>";
		echo "<tr style='background-color: #DBE1C5;'><td>";
		$depenses = compte_resultat_charges_produits($var, intval($GLOBALS['association_metas']['classe_charges']));
		echo "</td></tr>";
		echo "<tr style='background-color: #DBE1C5;'><td>";
		$recettes = compte_resultat_charges_produits($var, intval($GLOBALS['association_metas']['classe_produits']));
		echo "</td></tr>";
		echo "<tr style='background-color: #DBE1C5;'><td>";
		compte_resultat_benefice_perte($recettes, $depenses);
		echo "</td></tr>";
		echo "<tr style='background-color: #DBE1C5;'><td>";
		compte_resultat_benevolat($var, intval($GLOBALS['association_metas']['classe_contributions_volontaires']));
		echo "</td></tr></table>";

		/* si plan on peut exporter en pdf, cs, xml, ..... */
		if($plan){
			echo "<br /><table cellpadding='2' cellspacing='6' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>";
			echo "<tr style='background-color: #DBE1C5;'>";
			echo "<td style='text-align:right;'>"._T('asso:cpte_resultat_mode_exportation')."</td>";
			foreach(array('pdf','csv','xml') as $type) { // exports possibles
				$h = generer_url_ecrire('export_compte_resultat', "type=$type&var=$var");
				echo "<td style='text-align:center;'><a href='$h'><strong>".ucfirst($type)."</strong></td>";
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
	$annee = $tableau[0];$join = $tableau[1];$sel = $tableau[2];$having = $tableau[3];$order = $tableau[4];
	echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>";
	echo "<tr style='background-color: #DBE1C5;'>";
	echo "<td width='10'><strong>&nbsp;</strong></td>";
	echo "<td width='30'><strong>&nbsp;</strong></td>";
	echo "<td><strong>" . (($class == $GLOBALS['association_metas']['classe_charges']) ? _T('asso:cpte_resultat_titre_charges') : _T('asso:cpte_resultat_titre_produits')) . "</strong></td>";
	echo "<td width='80'><strong>&nbsp;</strong></td>";
	echo "</tr>";
	$quoi = (($class == $GLOBALS['association_metas']['classe_charges']) ? ("sum(depense) AS valeurs") : ("sum(recette) AS valeurs"));
	$query = sql_select("imputation, " . $quoi . ", date_format(date, '%Y') AS annee$sel",
			"spip_asso_comptes$join",
			"",
			$order . "annee",
			"code ASC",
			'',
			"annee=$annee$having$class");

	$total = 0;
	$chapitre = '';
	$i = 0;
	while ($data = sql_fetch($query)) {
		echo '<tr style="background-color: #EEEEEE;">';
		$valeurs = $data['valeurs'];
		$new_chapitre = substr($data['code'], 0, 2);
		if ($chapitre != $new_chapitre) {
			echo "<td class='arial11 border1'>" . $new_chapitre . '</td>';
			echo "<td colspan='3' class='arial11 border1'>" . association_plan_comptable_complet($new_chapitre) . '</td>';
			$chapitre = $new_chapitre;
			echo '</tr><tr style="background-color: #EEEEEE;">';
		}
		echo "<td>&nbsp;</td>";
		echo "<td class='arial11 border1'>" . $data['code'] . '</td>';
		echo "<td class='arial11 border1'>" . $data['intitule'] . '</td>';
		echo '<td class="arial11 border1" style="text-align:right;">' . number_format($valeurs, 2, ',', ' ') . '</td>';
		echo '</tr>';
		$total += $valeurs;
	}

	echo '<tr style="background-color: #EEEEEE;">';
	echo "<td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td class='arial11 border1' style='text-align:right;color: #9F1C30;'><strong>" . (($class == '6') ? _T('asso:cpte_resultat_total_charges') : _T('asso:cpte_resultat_total_produits')) . '</strong></td>';
	echo "<td class='arial11 border1' style='text-align:right;color: #9F1C30;'><strong>" . number_format($total, 2, ',', ' ') . '</strong></td>';
	echo "</tr>";
	echo '</table>';

	return $total;
}

function compte_resultat_benefice_perte($recettes, $depenses) {
	echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>";
	echo "<tr style='background-color: #DBE1C5;'>";
	echo "<td width='30'><strong>&nbsp;</strong></td>";
	$res = $recettes - $depenses;
	echo "<td class='arial11 border1' style='text-align:right;color: #9F1C30;'><strong>" . (($res < 0) ? _T('asso:cpte_resultat_perte') : _T('asso:cpte_resultat_benefice')) . "</strong></td>";
	echo "<td width='80' class='arial11 border1' style='text-align:right;color: #9F1C30;'><strong>" . number_format($res, 2, ',', ' ') . "</strong></td>";

	echo "</tr></table>";
}

function compte_resultat_benevolat($var, $class) {
	$tableau = @unserialize($var);
	$annee = $tableau[0];$join = $tableau[1];$sel = $tableau[2];$having = $tableau[3];$order = $tableau[4];
	echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>";
	echo "<tr style='background-color: #DBE1C5;'>";
	echo "<td width='10'><strong>&nbsp;</strong></td>";
	echo "<td width='30'><strong>&nbsp;</strong></td>";
	echo '<td><strong>' . _T('asso:cpte_resultat_titre_benevolat') . '</strong></td>';
	echo "<td width='80' style='text-align:right;'><strong>"._T('asso:cpte_resultat_recette_evaluee')."</strong></td>";
	echo "<td width='80' style='text-align:right;'><strong>"._T('asso:cpte_resultat_depense_evaluee')."</strong></td>";
	$query = sql_select("imputation, sum(recette) AS recettes, sum(depense) AS depenses, date_format(date, '%Y') AS annee$sel",
			"spip_asso_comptes$join",
			"",
			$order . "annee",
			"code ASC",
			'',
			"annee=$annee$having$class");
	$chapitre = '';
	$total_recettes = $total_depenses = 0;
	while ($data = sql_fetch($query)) {
		$recettes = $data['recettes'];
		$depenses = $data['depenses'];
		echo '<tr style="background-color: #EEEEEE;">';
		$new_chapitre = substr($data['code'], 0, 2);
		if ($chapitre != $new_chapitre) {
			echo "<td class='arial11 border1'>" . $new_chapitre . '</td>';
			echo "<td colspan='4' class='arial11 border1'>" . association_plan_comptable_complet($new_chapitre) . '</td>';
			$chapitre = $new_chapitre;
			echo '</tr><tr style="background-color: #EEEEEE;">';
		}
		echo "<td>&nbsp;</td>";		
		echo "<td class='arial11 border1'>" . $data['code'] . '</td>';
		echo "<td class='arial11 border1'>" . $data['intitule'] . '</td>';
		echo '<td class="arial11 border1" style="text-align:right;">' . number_format($recettes, 2, ',', ' ') . '</td>';
		echo '<td class="arial11 border1" style="text-align:right;">' . number_format($depenses, 2, ',', ' ') . '</td>';
		echo '</tr>';
		$total_recettes += $recettes;
		$total_depenses += $depenses;
	}

	$total_recettes = number_format($total_recettes, 2, ',', ' ');
	$total_depenses = number_format($total_depenses, 2, ',', ' ');

	echo '<tr style="background-color: #EEEEEE;">';
	echo "<td class='arial11 border1' style='text-align:right;color: #9F1C30;' colspan='3'><strong>" . _T('asso:resultat_courant') . '</strong></td>';
	echo "<td class='arial11 border1' style='text-align:right;color: #9F1C30;'><strong>" . $total_recettes . '</strong></td>';
	echo "<td class='arial11 border1' style='text-align:right;color: #9F1C30;'><strong>" . $total_depenses . '</strong></td>';
	echo "</tr>";
	echo '</table>';
}

?>
