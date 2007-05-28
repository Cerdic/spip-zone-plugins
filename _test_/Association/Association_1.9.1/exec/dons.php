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

function exec_dons(){
global $connect_statut, $connect_toutes_rubriques;

$url_asso = generer_url_ecrire('association');	
$url_dons = generer_url_ecrire('dons');
$url_ajout_don= generer_url_ecrire('ajout_don');
$url_edit_don =generer_url_ecrire('edit_don');
$url_action_dons = generer_url_ecrire('action_dons');

debut_page(_T('Gestion pour  Association'), "", "");

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Tous les dons'));
	debut_boite_info();

print association_date_du_jour();

// PAGINATION ET FILTRES
echo '<table width="70%">';
echo '<tr>';
echo '<td>';

$annee=$_GET['annee'];
if(empty($annee)){$annee = date('Y');}

$query = spip_query ( "SELECT date_format( date_don, '%Y' )  AS annee FROM spip_asso_dons GROUP BY annee ORDER BY annee" );

while ($data = spip_fetch_array($query))
   {
 	if ($data['annee']==$annee)
	{echo ' <strong>'.$data['annee'].'</strong>';}
	else {echo ' <a href="'.$url_dons.'&annee='.$data['annee'].'">'.$data['annee'].'</a>';}
	}
echo '</td>';
echo '</table>';

//TABLEAU
echo '<form method="post" action="'.$url_action_dons.'">';
echo '<table width="70%" border="0">';
echo '<tr bgcolor="#C8F7FB"> ';
echo '<td><strong>ID</strong></td>';
echo '<td><strong>Date</strong></td>';
echo '<td><strong>NOM</strong></td>';
//echo '<td><strong>Email</strong></td>';
//echo '<td><strong>Adresse</strong></td>';
//echo '<td><strong>T&eacute;l&eacute;phone</strong></td>';
echo '<td style="text-align:right;"><strong>Argent</strong></td>';
echo '<td><strong>Colis</strong></td>';
echo '<td style="text-align:right;"><strong>Valeur</strong></td>';
echo '<td><strong>Contrepartie</strong></td>';
echo '<td colspan=2><strong>Action</strong></td>';
echo '</tr>';

$query = spip_query ("SELECT * FROM spip_asso_dons WHERE date_format( date_don, '%Y' ) = '$annee'  ORDER by id_don" ) ;

while ($data = spip_fetch_array($query))
   {
$class="pair";
echo '<tr>';
echo '<td class ='.$class.'>'.$data['id_don'].'</td>';
echo '<td class ='.$class.'>'.association_datefr($data["date_don"]).'</td>';
echo '<td class ='.$class.'>'.$data["bienfaiteur"].'</td>';
// echo '   <td class ='.$class.'></td>';
//echo '   <td class ='.$class.'>'.$data['adresse'].'</td>';
//echo '   <td class ='.$class.'>'.$data['telephone'].'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.$data['argent'].'&nbsp;&euro;</td>';
echo '<td class ='.$class.'>'.$data['colis'].'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.$data['valeur'].'&nbsp;&euro;</td>';
echo '<td class ='.$class.'>'.$data['contrepartie'].'</td>';
//echo '   <td class ='.$class.'>'.$data['commentaire'].'</td>';
echo '<td class ='.$class.' style="text-align:center"><a href="'.$url_edit_don.'&id='.$data['id_don'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Mettre &agrave; jour le don"></a>';
echo '<td class ='.$class.'><input name="delete[]" type="checkbox" value='.$data['id_don'].'></td>';
echo '</tr>';
  }
echo '</table>';

echo '<table width="70%" border="0">';
echo '<tr>';
echo ' <td style="text-align:right;">';
echo '<input type="submit" name="Submit" value="Supprimer" class="fondo">';
echo ' </table>';
echo '</form>';

echo '<div align="center">';
icone(_T('asso:Ajouter un don'), $url_ajout_don, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/bienfaiteur.png','creer.gif' );
echo '</div>';
  
   fin_boite_info();
  fin_cadre_relief();  
fin_page();
}
?>
