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

function exec_ventes(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour  Association'), "", "");

$url_asso = generer_url_ecrire('association');
$url_ventes = generer_url_ecrire('ventes');
$url_action_ventes = generer_url_ecrire('action_ventes');
$url_edit_vente=generer_url_ecrire('edit_vente');
$url_ajout_vente=generer_url_ecrire('ajout_vente');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Toutes les ventes'));
	debut_boite_info();

print association_date_du_jour();
	
// PAGINATION ET FILTRES
echo '<table width="70%">';
echo '<tr>';
echo '<td>';

$annee=$_GET['annee'];
if(empty($annee)){$annee = date('Y');}

$query = spip_query ("SELECT date_format( date_vente, '%Y' )  AS annee FROM spip_asso_ventes GROUP BY annee ORDER BY annee");

while ($data = spip_fetch_array($query))
   {
 	if ($data['annee']==$annee)
	{echo ' <strong>'.$data['annee'].'</strong>';}
	else {echo ' <a href="'.$url_ventes.'&annee='.$data['annee'].'">'.$data['annee'].'</a>';}
	}
echo '</td>';
echo '</table>';

//TABLEAU
echo '<form action="'.$url_action_ventes.'" method="POST">';
echo '<table width="70%" border="0">';
echo '<tr bgcolor="#D9D7AA">';
echo '<td style="text-align:right"><strong>ID</strong></td>';
echo '<td style="text-align:right"><strong>Date</strong></td>';
echo '<td><strong>Article</strong></td>';
echo '<td><strong>Code</strong></td>';
echo '<td><strong>Acheteur</strong></td>';
echo '<td style="text-align:right"><strong>Quantit&eacute;</strong></td>';
//echo '<td style="text-align:right"><strong>Prix unitaire</strong></td>';
//echo '<td><strong>Don</strong></td>';
echo '<td style="text-align:right"><strong>Date d\'envoi</strong></td>';
//echo '<td style="text-align:right"><strong>Frais d\'envoi</strong></td>';
echo '<td colspan="2" style="text-align:center"><strong>Action</strong></td>';
echo '</tr>';

$query = spip_query ("SELECT * FROM spip_asso_ventes WHERE date_format( date_vente, '%Y' ) = '$annee'  ORDER by id_vente DESC") ;
 
while ($data = spip_fetch_array($query))
{

if(isset($data['date_envoi']))
{ $class= "pair"; }
else
{$class="impair";}   
   
echo '<tr> ';
echo '<td class ='.$class.' style="text-align:right">'.$data['id_vente'].'</td>';
echo '<td class ='.$class.' style="text-align:right">'.association_datefr($data['date_vente']).'</td>';
echo '<td class ='.$class.'>'.$data['article'].'</td>';
echo '<td class ='.$class.'>'.$data['code'].'</td>';
echo '<td class ='.$class.'>'.$data['acheteur'].'</td>';
echo '<td class ='.$class.' style="text-align:right">'.$data['quantite'].'</td>';
//echo '<td class ='.$class.' style="text-align:right">'.$data['prix_vente'].'&nbsp;&euro;</td>';
//echo '<td class ='.$class.'>'.$data['don'].'</td>';
echo '<td class ='.$class.' style="text-align:right">'.association_datefr($data['date_envoi']).'</td>';
//echo '<td class ='.$class.' style="text-align:right">'.$data['frais_envoi'].'&nbsp;&euro;</td>';
echo '<td class ='.$class.' style="text-align:center"><a href="'.$url_edit_vente.'&id='.$data['id_vente'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Mettre &agrave; jour la vente"></a>';
echo '<td class ='.$class.' style="text-align:center"><input name="delete[]" type="checkbox" value='.$data['id_vente'].'></td>';



//$tirage= $data['tirage'];	
//$livreoffert += $data['livre_don'];
$somme_quantites += $data['quantite'];

echo '</tr>';
 }     
echo '</table>';

echo '<table width="70%">';
echo '<tr>';
echo '<td  style="text-align:right;">';
echo '<input type="submit" name="Submit" value="Envoyer" class="fondo">';
echo '</table>';
echo '</form>';

echo '<p>';
icone(_T('Ajouter une vente'), $url_ajout_vente, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/panier_in.gif','rien.gif' );
echo '</p>';
		
//$total =$i; 


echo'<BR />';  
echo'<table border="0">';
echo' <tr>';
echo'  <td><font color="green"><strong>Nombre d\'articles vendus :</td>';
echo'  <td style="text-align:right;">'.$somme_quantites.'</td>';
echo' </tr>';
echo'</table>';
echo'<BR />';

fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>
