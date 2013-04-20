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


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_bilan(){

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$plan = sql_countsel('spip_asso_plan');

		if (!($annee = _request('annee')))
		{
			$annee = date('Y');
			$url_bilan = generer_url_ecrire('bilan');
		}
		else
		{
			$url_bilan = generer_url_ecrire('bilan', "annee=$annee");
		}

		// recupere l'id_destination de la ou des destinations dans POST ou cree une entree a 0 dans le tableau
		if (!($ids_destination_bilan = _request('destination'))) $ids_destination_bilan = array(0);

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(propre(_T('asso:titre_gestion_pour_association')), "", _DIR_PLUGIN_ASSOCIATION_ICONES.'finances.jpg','rien.gif');
		association_onglets(_T('asso:titre_onglet_comptes'));

		echo debut_gauche("",true);

		echo debut_boite_info(true);
		echo association_date_du_jour();

		if ($GLOBALS['association_metas']['destinations']=="on")
		{
			// cree un menu a choix multiple des destinations a inserer dans la boite info et recupere les intitule de toutes les destinations dans un tableau
			$select_destination = '';
			$intitule_destinations = array();
			$query = sql_select("id_destination, intitule", 'spip_asso_destination', "", "",  "intitule" );
			while ($data = sql_fetch($query)) {
				$select_destination .= "<option value='".$data['id_destination']."'";
				if (!(array_search($data['id_destination'], $ids_destination_bilan) === FALSE)) $select_destination .= " selected='selected'";
				$select_destination .=">".$data['intitule']."</option>";
				$intitule_destinations[$data['id_destination']] = $data['intitule'];
			}

			echo '<form method="post" action="'.$url_bilan.'"><div>';
			echo '<select name ="destination[]" class="fondl" multiple>';
			echo '<option value="0"';
			if (!(array_search(0, $ids_destination_bilan) === FALSE)) echo ' selected="selected"';
			echo '>Total</option><option disabled="disabled">--------</option>'.$select_destination;
			echo '</select>';
			echo '<input type="submit" value="Bilan" />';
			echo '</div></form>';
		}
		echo fin_boite_info(true);
		echo debut_droite("",true);

		debut_cadre_relief(_DIR_PLUGIN_ASSOCIATION_ICONES."finances.jpg", false, "", '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .propre( _T('asso:bilans_comptables')));



		$clas=$GLOBALS['association_metas']['classe_banques'];

		if ($plan) {
			$join = " RIGHT JOIN spip_asso_plan ON imputation=code";
			$sel = ", code, intitule, classe";
			$having =  " AND classe <> " . sql_quote($clas);
			$order = "code,";
		} else $join = $sel = $having = $order = '';



		// on boucle sur le tableau des destinations en refaisant le fetch a chaque iteration
		foreach ($ids_destination_bilan as $id_destination) {

			$total_recettes=$total_depenses=$total_soldes=0;
			//TABLEAU EXPLOITATION
			if ($id_destination != 0) {
				$intutile_destination_bilan = $intitule_destinations[$id_destination];
			}
			else {
				if ($GLOBALS['association_metas']['destinations']=="on") $intutile_destination_bilan = _T('asso:toutes_destination');
			}

			echo "\n<fieldset>";
			echo '<legend><strong>', _T('asso:resultat_courant') . ' ' . $annee. ' ' .$intutile_destination_bilan. '</strong></legend>';
			echo "\n<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			echo "<tr style='background-color: #DBE1C5;'>\n";
			echo '<td><strong>&nbsp;</strong></td>';
			echo "<td style='text-align:center;'><strong>\n" . 'Recettes</strong></td>';
			echo "<td style='text-align:center;'><strong>\n" . _T('asso:depenses') . '</strong></td>';
			echo "<td style='text-align:center;'><strong>\n" . 'Solde</strong></td>';
			echo "</tr>\n";

			// si on fait le bilan sur toutes les destinations (ou que destination n'est pas on)
			if ($id_destination == 0) {
				$query = sql_select("imputation, sum( recette ) AS recettes, sum( depense ) AS depenses, date_format( date, '%Y' ) AS annee$sel", "spip_asso_comptes$join", "", "${order}annee", "annee DESC", '',  "annee=$annee$having");
				while ($data = sql_fetch($query)) {
					$recettes=$data['recettes'];
					$depenses=$data['depenses'];
					$soldes=$recettes - $depenses;
					echo '<tr style="background-color: #EEEEEE;">';
					echo "<td class='arial11 border1'>\n".$data['intitule'].'</td>';
					echo '<td class="arial11 border1" style="text-align:right;">'.number_format($recettes, 2, ',', ' ').'</td>';
					echo '<td class="arial11 border1" style="text-align:right;">'.number_format($depenses, 2, ',', ' ').'</td>';
					echo '<td class="arial11 border1" style="text-align:right;">'.number_format($soldes, 2, ',', ' ').'</td>';
					echo '</tr>';
					$total_recettes += $recettes;
					$total_depenses += $depenses;
					$total_soldes += $soldes;
				}
			}
			else // on fait le bilan d'une seule destination
			{
				$query = sql_select("imputation, date_format( date, '%Y' ) AS annee, sum( spip_asso_destination_op.recette ) AS recettes, sum( spip_asso_destination_op.depense ) AS depenses, spip_asso_destination_op.id_destination$sel", "spip_asso_comptes LEFT JOIN spip_asso_destination_op ON spip_asso_destination_op.id_compte=spip_asso_comptes.id_compte$join", "spip_asso_destination_op.id_destination=$id_destination", "${order}annee", "annee DESC", '',  "annee=$annee$having");
				while ($data = sql_fetch($query)) {
					$recettes=$data['recettes'];
					$depenses=$data['depenses'];
					$soldes=$recettes - $depenses;
					echo '<tr style="background-color: #EEEEEE;">';
					echo "<td class='arial11 border1'>\n".$data['intitule'].'</td>';
					echo '<td class="arial11 border1" style="text-align:right;">'.number_format($recettes, 2, ',', ' ').'</td>';
					echo '<td class="arial11 border1" style="text-align:right;">'.number_format($depenses, 2, ',', ' ').'</td>';
					echo '<td class="arial11 border1" style="text-align:right;">'.number_format($soldes, 2, ',', ' ').'</td>';
					echo '</tr>';
					$total_recettes += $recettes;
					$total_depenses += $depenses;
					$total_soldes += $soldes;
				}
			}
			$total_recettes=number_format($total_recettes, 2, ',', ' ');
			$total_depenses=number_format($total_depenses, 2, ',', ' ');
			$total_soldes=number_format($total_soldes, 2, ',', ' ');
			echo '<tr style="background-color: #EEEEEE;">';
			echo "\n<td class='arial11 border1' style='color: #9F1C30;'><strong>" . _T('asso:resultat_courant') . '</strong></td>';
			echo "\n<td class='arial11 border1' style='text-align:right;color: #9F1C30;'><strong>".$total_recettes.'</strong></td>';
			echo "\n<td class='arial11 border1' style='text-align:right;color: #9F1C30;'><strong>".$total_depenses.'</strong></td>';
			echo "\n<td class='arial11 border1' style='text-align:right;color: #9F1C30;'><strong>".$total_soldes.'</strong></td></tr>';
			echo '</table>';
			echo '</fieldset>';
		}

		if ($plan) bilan_encaisse($annee);
		fin_cadre_relief();
		echo fin_page_association();
	}
}

function bilan_encaisse($annee)
{
	$total_actuel=$total_initial=0;
	echo "\n<fieldset>";
	echo '<legend><strong>' . _T('asso:encaisse') . '</strong></legend>';
	echo "\n<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
	echo "<tr style='background-color: #DBE1C5;'>\n";
	echo "\n<td><strong>&nbsp;</strong></td>";
	echo '<td style="text-align:center;" colspan="2"><strong>', _T('asso:avoir_initial') . "</strong></td>\n";
	echo "<td style='text-align:center;'><strong>\n" . _T('asso:avoir_actuel') . "</strong></td>\n";
	echo '</tr>';
	$clas=$GLOBALS['association_metas']['classe_banques'];
	$query = sql_select('*', 'spip_asso_plan', "classe='$clas'", '',  "code" );

	while ($banque = sql_fetch($query)) {
		$date_solde=$banque['date_anterieure'];
		$journal=$banque['code'];
		$solde=$banque['solde_anterieur'];
		$total_initial += $solde;
		echo '<tr style="background-color: #EEEEEE;">';
		echo "\n<td class='arial11 border1'>".$banque['intitule'];
		echo "\n<td class='arial11 border1' style='text-align:right;'>".association_datefr($date_solde).'</td>';
		echo "\n<td class='arial11 border1' style='text-align:right;'>".number_format($solde, 2, ',', ' ').'</td>';

		$compte = sql_fetsel("sum( recette ) AS recettes, sum( depense ) AS depenses, date", "spip_asso_comptes", "date >= '$date_solde' AND journal = '$journal'", 'journal');

		if ($compte)
			$solde += ($compte['recettes'] -$compte['depenses']);
		echo "\n<td class='arial11 border1' style='text-align:right;'>".number_format($solde, 2, ',', ' ').'</tr>';
		$total_actuel += $solde;
	}

	$total_initial=number_format($total_initial, 2, ',', ' ');
	$total_actuel=number_format($total_actuel, 2, ',', ' ');
	echo '<tr style="background-color: #EEEEEE;">';
	echo "\n<td class='arial11 border1' style='color: #9F1C30;'><strong>" . _T('asso:encaisse') . "</strong></td>\n";
	echo '<td class="arial11 border1" style="text-align:right;color: #9F1C30;"><strong>&nbsp;</strong></td>';
	echo '<td class="arial11 border1" style="text-align:right;color: #9F1C30;"><strong>'.$total_initial.'</strong></td>';
	echo '<td class="arial11 border1" style="text-align:right;color: #9F1C30;"><strong>'.$total_actuel.'</strong></td></tr>';
	echo '</table>';
	echo '</fieldset>';
}
?>
