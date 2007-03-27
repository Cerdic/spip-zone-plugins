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

function exec_activites(){
global $connect_statut, $connect_toutes_rubriques, $table_prefix;

debut_page(_T('Gestion pour  Association'), "", "");

$url_articles = generer_url_ecrire('articles');
$url_activites = generer_url_ecrire('activites');
$url_ajout_activite = generer_url_ecrire('ajout_activite');
$url_voir_activites = generer_url_ecrire('voir_activites');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Toutes les activit&eacute;s'));
	debut_boite_info();

print('<p>Nous sommes le '.date('d/m/Y').'</p>');

// FILTRES

//Bricolage?
if ( isset ($_POST['mot'] )) {
	$mot = $_POST['mot']; }
	elseif ( isset ($_GET['mot'] )) {
		$mot =  $_GET['mot']; }
		else { $mot= "%"; }
		
echo '<table width="70%">';
echo '<tr>';
echo '<td>';
$annee=$_GET['annee'];
if(empty($annee)){$annee = date('Y');}

global $table_prefix;
$query = spip_query ("SELECT date_format( date_debut, '%Y' )  AS annee FROM spip_evenements GROUP BY annee ORDER by annee");

while ($data = mysql_fetch_assoc($query))
   {
 	if ($data['annee']==$annee)
	{echo ' <strong>'.$data['annee'].'</strong>';}
	else {echo '<a href="'.$url_activites.'&annee='.$data['annee'].'&mot='.$mot.'">'.$data['annee'].'</a>';}
	}
echo '</td>';
echo '<td style="text-align:right;">';
echo '<form method="post" action="'.$url_activites.'">';
echo '<select name ="mot" class="fondl" onchange="form.submit()">';
echo '<option value="%"';
	if ($mot=="%") {echo ' selected="selected"';}
	echo '> Toutes</option>';
$query = spip_query("SELECT * FROM spip_mots WHERE type='Evènements'");
while($data = mysql_fetch_assoc($query)) 
{
echo '<option value="'.$data["titre"].'"';
	if ($mot==$data["titre"]) { echo ' selected="selected"'; }
	echo '> '.$data["titre"].'</option>';
}
echo '</select>';
echo '</form>';
echo '</table>';

//TABLEAU
echo '<table width="70%">';
echo '<tr bgcolor="silver">';
echo '<td style="text-align:right;"><strong>ID</strong></td>';
echo '<td><strong>Date</strong></td>';
echo '<td><strong>Heure</strong></td>';
echo '<td><strong>Intitul&eacute;</strong></td>';
echo '<td><strong>Lieu</strong></td>';
echo '<td><strong>Inscrits</strong></td>';
echo '<td colspan="3" style="text-align:center;"><strong>Action</strong></td>';
echo '</tr>';

$max_par_page=30;
$debut=$_GET['debut'];

if (empty($debut))
{$debut=0;}

$query = spip_query ("SELECT *, spip_evenements.titre AS intitule, spip_mots.titre AS motact  FROM ".$table_prefix."_evenements INNER JOIN spip_mots_evenements ON  spip_mots_evenements.id_evenement=spip_evenements.id_evenement INNER JOIN spip_mots ON spip_mots_evenements.id_mot=spip_mots.id_mot WHERE date_format( date_debut, '%Y' ) = $annee AND spip_mots.titre like '$mot' ORDER BY date_debut DESC LIMIT $debut,$max_par_page");

while ($data = mysql_fetch_assoc($query)) {

$class= "pair";
$date = substr($data['date_debut'],0,10);
$heure = substr($data['date_debut'],10,6);
echo '<tr> ';
echo '<td class ='.$class.' style="text-align:right;">'.$data['id_evenement'].'</td>';
//echo '<td class ='.$class.'>'.$jour.'-'.$mois. '-'.$annee.'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.association_datefr($date).'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.$heure.'</td>';
echo '<td class ='.$class.'>'.$data['intitule'].'</td>';
echo '<td class ='.$class.'>'.$data['lieu'].'</td>';
echo '<td class ='.$class.'>&nbsp</td>';
echo '<td class ='.$class.' style="text-align:center"><a href="'.$url_articles.'&id_article='.$data['id_article'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Modifier l\'article"></a></td>';
echo '<td class ='.$class.'><a href="'.$url_ajout_activite.'&id='.$data['id_evenement'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/cotis-12.gif" title="Ajouter une inscription"></a>';
echo '<td class ='.$class.' style="text-align:center;"><a href="'.$url_voir_activites.'&id='.$data['id_evenement'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/voir-12.gif" title="Voir la liste des inscriptions"></a></td>';
echo '</tr>';
}
echo '</table>';

echo '<table width="70%">';
echo '<tr>';

//SOUS-PAGINATION
echo '<td>';
$query = spip_query("SELECT * FROM ".$table_prefix."_asso_comptes WHERE date_format( date, '%Y' ) = $annee AND imputation like '$imputation' ");
$nombre_selection=spip_num_rows($query);
$pages=intval($nombre_selection/$max_par_page) + 1;

if ($pages == 1)	
{ echo '';}
else {
	for ($i=0;$i<$pages;$i++)
	{ 
	$position= $i * $max_par_page;
	if ($position == $debut)
	{ echo '<strong>'.$position.' </strong>'; }
	else 
	{ echo '<a href="'.$url_comptes.'&annee='.$annee.'&debut='.$position.'&imputation='.$imputation.'">'.$position.'</a> '; }
	}	
}
echo '</td>';
echo '</table>';

echo '<br />';


fin_boite_info();
  fin_cadre_relief();  
fin_page();
}
?>

