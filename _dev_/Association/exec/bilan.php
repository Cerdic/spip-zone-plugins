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

function exec_bilan(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour  Association'), "", "");

$url_comptes = generer_url_ecrire('comptes');
$url_edit_compte = generer_url_ecrire('edit_compte');
$url_action_comptes = generer_url_ecrire('action_comptes');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Bilans comptables'));
	debut_boite_info();

print association_date_du_jour();

$annee = date('Y');
$class= "impair";

//TABLEAU EXPLOITATION
echo '<table width="70%" border="0" align="center">';
echo '<tr bgcolor="#D9D7AA">';
echo '<td><strong>RESULTAT COURANT '.$annee.'</strong></td>';
echo '<td style="text-align:right;"><strong>Recettes</strong></td>';
echo '<td style="text-align:right;"><strong>D&eacute;penses</strong></td>';
echo '<td style="text-align:right;"><strong>Solde</strong></td>';
echo '</tr>';

$query = spip_query ("SELECT libelle, valeur, sum( recette ) AS recettes, sum( depense ) AS depenses, date_format( date, '%Y' ) AS annee FROM spip_asso_comptes RIGHT JOIN spip_asso_livres ON valeur=imputation GROUP BY valeur, annee HAVING annee = $annee ORDER BY annee DESC");

while ($data = spip_fetch_array ($query)) {
$recettes=$data['recettes'];
$depenses=$data['depenses'];
$soldes=$recettes - $depenses;
echo '<tr>';
echo '<td class ='.$class.'>'.$data['libelle'].'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.number_format($recettes, 2, ',', ' ').'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.number_format($depenses, 2, ',', ' ').'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.number_format($soldes, 2, ',', ' ').'</td>';
echo '</tr>';
$total_recettes += $recettes;	
$total_depenses += $depenses;	
$total_soldes += $soldes;	
}
$total_recettes=number_format($total_recettes, 2, ',', ' '); 
$total_depenses=number_format($total_depenses, 2, ',', ' '); 
$total_soldes=number_format($total_soldes, 2, ',', ' '); 
echo '<tr>';
echo '<td style="color:#9F1C30;"><strong>R&eacute;sultat courant</strong></td>';
echo '<td style="text-align:right;color:#9F1C30;"><strong>'.$total_recettes.'</strong></td>'; 
echo '<td style="text-align:right;color:#9F1C30;"><strong>'.$total_depenses.'</strong></td>';
echo '<td style="text-align:right;color:#9F1C30;"><strong>'.$total_soldes.'</strong></td></tr>'; 
echo '</tr>';
echo '<tr>';
echo '<td style="text-align:right;">&nbsp;</td>';
echo '<tr bgcolor="#D9D7AA">';
echo '<td><strong>ENCAISSE '.$annee.'</strong></td>';
echo '<td style="text-align:right;" colspan="2"><strong>Avoir initial</strong></td>';
echo '<td style="text-align:right;"><strong>Avoir actuel</strong></td>';
echo '</tr>';

$query = spip_query ( "SELECT * FROM spip_asso_banques ORDER BY id_banque" );

while ($banque = spip_fetch_array($query)) {
$date_solde=$banque['date'];
$journal=$banque['code'];
$solde=$banque['solde'];
echo '<tr>';
echo '<td class ='.$class.'>'.$banque['intitule']; 
echo '<td class ='.$class.' style="text-align:right;">'.association_datefr($date_solde).'</td>'; 
echo '<td class ='.$class.' style="text-align:right;">'.number_format($solde, 2, ',', ' ').'</td>'; 

$sql = spip_query ( "SELECT sum( recette ) AS recettes, sum( depense ) AS depenses, date FROM spip_asso_comptes WHERE date > '$date_solde' AND journal = '$journal' GROUP BY '$journal' " );

if ($compte = spip_fetch_array($sql)) {
	$recettes=$compte['recettes'];
	$depenses=$compte['depenses'];
} else {
	$recettes=0;
	$depenses=0;
}		

$avoir_actuel=$solde + $recettes - $depenses;
echo '<td class ='.$class.' style="text-align:right;">'.number_format($avoir_actuel, 2, ',', ' ').'</tr>';
$total_actuel += $avoir_actuel;		

$total_initial += $solde;
}

$total_initial=number_format($total_initial, 2, ',', ' '); 
$total_actuel=number_format($total_actuel, 2, ',', ' '); 
echo '<tr>';
echo '<td style="color:#9F1C30;"><strong>Encaisse</strong></td>';
echo '<td style="text-align:right;color:#9F1C30;"><strong>&nbsp;</strong></td>'; 
echo '<td style="text-align:right;color:#9F1C30"><strong>'.$total_initial.'</strong></td>'; 
echo '<td style="text-align:right;color:#9F1C30"><strong>'.$total_actuel.'</strong></td></tr>'; 
echo '</tr>';
echo '</table>';

fin_boite_info();
  fin_cadre_relief();  
fin_page();
}
?>

