<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');
	
	function exec_bilan(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$total_actuel=$total_initial=$total_recettes=$total_depenses=$total_soldes=0;
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(propre(_T('Gestion pour  Association')), "", _DIR_PLUGIN_ASSOCIATION_ICONES.'finances.jpg','rien.gif');
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		
		echo debut_droite("",true);
		
		debut_cadre_relief(  _DIR_PLUGIN_ASSOCIATION_ICONES."finances.jpg", false, "", $titre =propre( _T('Bilans comptables')));
		
		if (!($annee = _request('annee'))) $annee = date('Y');
		$class= "impair";
		
		//TABLEAU EXPLOITATION
		echo '<fieldset>';
		echo '<legend>R&eacute;sultat courant '.$annee.'</strong></legend>';
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td><strong>&nbsp;</strong></td>';
		echo '<td style="text-align:center;"><strong>Recettes</strong></td>';
		echo '<td style="text-align:center;"><strong>D&eacute;penses</strong></td>';
		echo '<td style="text-align:center;"><strong>Solde</strong></td>';
		echo '</tr>';
		$ac=lire_config('association/classe_banques'); 
		$query = sql_select("imputation, sum( recette ) AS recettes, sum( depense ) AS depenses, date_format( date, '%Y' ) AS annee, code, intitule, classe", 'spip_asso_comptes RIGHT JOIN spip_asso_plan ON imputation=code', '', 'code, annee', "annee DESC", '',  "annee = $annee AND classe <> $annee");
		
		while ($data = sql_fetch($query)) {
			$recettes=$data['recettes'];
			$depenses=$data['depenses']; 
			$soldes=$recettes - $depenses;
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['intitule'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($recettes, 2, ',', ' ').'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($depenses, 2, ',', ' ').'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($soldes, 2, ',', ' ').'</td>';
			echo '</tr>';
			$total_recettes += $recettes;	
			$total_depenses += $depenses;	
		$total_soldes += $soldes;	
		}
		$total_recettes=number_format($total_recettes, 2, ',', ' '); 
		$total_depenses=number_format($total_depenses, 2, ',', ' '); 
		$total_soldes=number_format($total_soldes, 2, ',', ' '); 
		echo '<tr style="background-color: #EEEEEE;">';
		echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;color:#9F1C30;"><strong>R&eacute;sultat courant</strong></td>';
		echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;color:#9F1C30;"><strong>'.$total_recettes.'</strong></td>'; 
		echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;color:#9F1C30;"><strong>'.$total_depenses.'</strong></td>';
		echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;color:#9F1C30;"><strong>'.$total_soldes.'</strong></td></tr>'; 
		echo '</tr>';
		echo '</table>';
		echo '</fieldset>';
		
		//TABLEAU ENCAISSE
		echo '<fieldset>';
		echo '<legend>Encaisse '.$annee.'</strong></legend>';
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td><strong>&nbsp;</strong></td>';
		echo '<td style="text-align:center;" colspan="2"><strong>Avoir initial</strong></td>';
		echo '<td style="text-align:center;"><strong>Avoir actuel</strong></td>';
		echo '</tr>';
		$clas=lire_config('association/classe_banques');
		$query = sql_select('*', 'spip_asso_plan', "classe='$clas'", '',  "code" );
		
		while ($banque = sql_fetch($query)) {
			$date_solde=$banque['date_anterieure'];
			$journal=$banque['code'];
			$solde=$banque['solde_anterieur'];
			$total_initial += $solde;
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$banque['intitule']; 
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.association_datefr($date_solde).'</td>'; 
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($solde, 2, ',', ' ').'</td>'; 
			
			$compte = sql_fetsel("sum( recette ) AS recettes, sum( depense ) AS depenses, date", "spip_asso_comptes", "date > '$date_solde' AND journal = '$journal'", $journal);
			
			if ($compte)
				$solde += ($compte['recettes'] -$compte['depenses']);
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($solde, 2, ',', ' ').'</tr>';
			$total_actuel += $solde;		

		}
		
		$total_initial=number_format($total_initial, 2, ',', ' '); 
		$total_actuel=number_format($total_actuel, 2, ',', ' '); 
		echo '<tr style="background-color: #EEEEEE;">';
		echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;color:#9F1C30;"><strong>Encaisse</strong></td>';
		echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;color:#9F1C30;"><strong>&nbsp;</strong></td>'; 
		echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;color:#9F1C30"><strong>'.$total_initial.'</strong></td>'; 
		echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;color:#9F1C30"><strong>'.$total_actuel.'</strong></td></tr>'; 
		echo '</tr>';
		echo '</table>';
		
		fin_cadre_relief();  
		echo fin_gauche(),fin_page();
	}
?>

