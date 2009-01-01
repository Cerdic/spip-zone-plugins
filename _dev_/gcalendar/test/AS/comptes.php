<?php

include_spip('inc/presentation');

function exec_comptes(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour  Association'), "", "");

$url_comptes = generer_url_ecrire('comptes');
$url_edit_compte = generer_url_ecrire('edit_compte');
$url_action_comptes = generer_url_ecrire('action_comptes');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Informations comptables'));
	debut_boite_info();

print('Nous sommes le '.date('d-m-Y').'<br>');

if ( isset ($_POST['imputation'] )) {
	$imputation = $_POST['imputation']; }
			else { $imputation= "%"; }

// FILTRES

echo '<table width="70%">';
echo '<tr>';
echo '<td>';
//Bricolage?
if ( isset ($_POST['imputation'] )) {
	$imputation = $_POST['imputation']; }
	elseif ( isset ($_GET['imputation'] )) {
		$imputation =  $_GET['imputation']; }
		else { $imputation= "%"; }

$annee=$_GET['annee'];
if(empty($annee)){$annee = date('Y');}

$query = "SELECT date_format( date, '%Y' )  AS annee FROM spip_asso_comptes WHERE imputation like '$imputation' GROUP BY annee ORDER by annee";
$val = spip_query ($query) ;

while ($data = mysql_fetch_assoc($val))
   {
 	if ($data['annee']==$annee)
	{echo ' <strong>'.$data['annee'].'</strong>';}
	else {echo ' <a href="'.$url_comptes.'&annee='.$data['annee'].'&imputation='.$imputation.'">'.$data['annee'].'</a>';}
	}
echo '</td>';
echo '<td style="text-align:right;">';
echo '<form method="post" action="'.$url_comptes.'">';
echo '<select name ="imputation" class="fondl" onchange="form.submit()">';
echo '<option value = "%" ';
	if ($imputation=="%") {echo 'selected';}
	echo '  > Tous les comptes</option>';
echo '<option value = "cotisation" ';
	if ($imputation=="cotisation") {echo 'selected';}
	echo ' > Livre des cotisations</option>';
echo '<option value = "vente" ';
	if ($imputation=="vente") {echo 'selected';}
	echo ' > Livre des ventes</option>';
/*
echo '<option value = "bienfaiteur" ';
	if ($imputation=="bienfaiteur") {echo 'selected';}
	echo ' > Journal des dons</option>';

echo '<option value = "achat" ';
	if ($imputation=="achat") {echo 'selected';}
	echo ' > Journal des achats</option>';
	*/
echo '</select>';
echo '</form>';
echo '</table>';

//TABLEAU
//echo '<form method="post" action="'.$url_action_comptes.'">';

echo '<table width="70%" border="0">';
echo '<tr bgcolor="silver">';
echo '<td style="text-align:right;"><strong>ID</strong></td>';
echo '<td style="text-align:right;"><strong>Date</strong></td>';
echo '<td style="text-align:right;"><strong>Recette</strong></td>';
echo '<td style="text-align:right;"><strong>D&eacute;pense</strong></td>';
echo '<td><strong>Justification</strong></td>';
echo '<td><strong>Journal</strong></td>';
echo '<td><strong>&nbsp;</strong></td>';
echo '</tr>';

$query = "SELECT * FROM spip_asso_comptes WHERE date_format( date, '%Y' ) = '$annee' AND imputation like '$imputation'  ORDER BY date DESC, id_compte DESC";
//$query = "SELECT * FROM spip_asso_comptes WHERE date_format( date, '%Y' ) = '$annee' AND imputation like '$imputation'  ORDER BY date DESC LIMIT $debut,$max_par_page";

$val = spip_query (${query}) ;

while ($data = mysql_fetch_assoc($val)) {

	if ($data['recette'] >0)
    { $class= "pair";}
    else { $class="impair";}	   
    
sscanf($data['date'], "%4s-%2s-%2s", $annee, $mois, $jour);

echo '<tr> ';
echo '<td class ='.$class.' style="text-align:right;">'.$data['id_compte'].'</td>';
//echo '<td class ='.$class.'>'.$jour.'-'.$mois. '-'.$annee.'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.$jour . '-' . $mois . '-' . $annee.'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.$data['recette'].'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.$data['depense'].'</td>';
echo '<td class ='.$class.'>'.$data['justification'].'</td>';
echo '<td class ='.$class.'>'.$data['journal'].'</td>';
echo '<td class ='.$class.' style="text-align:center"><a href="'.$url_edit_compte.'&id='.$data['id_compte'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Mettre &agrave; jour l\'op&eacuteration"></a></td>';
//echo '<td class ='.$class.'><input name="delete[]" type="checkbox" value='.$data['id_compte'].'></td>';
echo '</tr>';
}
echo '</table>';

//echo '<table width="70%">';
//echo '<tr>';
//echo '<td></td>';
//echo '<td style="text-align:right;">';
//echo '<input type="submit" name="Submit" value="Envoyer" class="fondo">';
//echo '</table>';

//echo '</form>';


echo 'En bleu : Recettes - En rose : D&eacute;penses'; 

fin_boite_info();
  fin_cadre_relief();  
fin_page();
}
?>

