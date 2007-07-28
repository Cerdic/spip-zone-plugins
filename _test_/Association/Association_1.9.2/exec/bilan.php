<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');

	function exec_bilan(){
		global $connect_statut, $connect_toutes_rubriques;
		
		if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}
		
		debut_page(_T('Gestion pour  Association'), "", '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/finances.jpg','rien.gif');
		
		$url_comptes = generer_url_ecrire('comptes');
		$url_edit_compte = generer_url_ecrire('edit_compte');
		$url_action_comptes = generer_url_ecrire('action_comptes');
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		
		echo association_date_du_jour();	
		
		fin_boite_info();
		
		debut_droite();
		
		debut_cadre_relief(  "../"._DIR_PLUGIN_ASSOCIATION."/img_pack/finances.jpg", false, "", $titre = _T('Bilans comptables'));
		
		$annee = date('Y');
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
		
		$query = spip_query ("SELECT imputation, sum( recette ) AS recettes, sum( depense ) AS depenses, date_format( date, '%Y' ) AS annee, code, intitule, classe FROM spip_asso_comptes RIGHT JOIN spip_asso_plan ON imputation=code GROUP BY code, annee HAVING annee = $annee AND classe IN (6,7) ORDER BY annee DESC");
		
		while ($data = spip_fetch_array ($query)) {
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
		
		$query = spip_query ( "SELECT * FROM spip_asso_plan WHERE classe='5' ORDER BY code" );
		
		while ($banque = spip_fetch_array($query)) {
			$date_solde=$banque['date_anterieure'];
			$journal=$banque['code'];
			$solde=$banque['solde_anterieur'];
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$banque['intitule']; 
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.association_datefr($date_solde).'</td>'; 
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($solde, 2, ',', ' ').'</td>'; 
			
			$sql = spip_query ( "SELECT sum( recette ) AS recettes, sum( depense ) AS depenses, date FROM spip_asso_comptes WHERE date > '$date_solde' AND journal = '$journal' GROUP BY '$journal' " );
			
			if ($compte = spip_fetch_array($sql)) {
				$recettes=$compte['recettes'];
				$depenses=$compte['depenses'];
			} 
			else {
				$recettes=0;
				$depenses=0;
			}		
			
			$avoir_actuel=$solde + $recettes - $depenses;
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($avoir_actuel, 2, ',', ' ').'</tr>';
			$total_actuel += $avoir_actuel;		
			$total_initial += $solde;
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
		fin_page();
	}
?>

