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

function exec_banques(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Banques'), "", "");

$url_banques = generer_url_ecrire('banques');
$url_ajout_banque=generer_url_ecrire('ajout_banque');
$url_edit_banque=generer_url_ecrire('edit_banque');
$url_action_banques=generer_url_ecrire('action_banques');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Tous les comptes financiers'));
	debut_boite_info();

print association_date_du_jour();

echo '<table width="70%" border="0">';
echo '<tr bgcolor="#D9D7AA">';
echo '<td><strong>ID</strong></td>';
echo '<td><strong>Code</strong></td>';
echo '<td><strong>Intitul&eacute;</strong></td>';
echo '<td><strong>R&eacute;f&eacute;rence</strong></td>';
echo '<td style="text-align:right;"><strong>Solde initial</strong></td>';
echo '<td><strong>Date</strong></td>';
echo '<td colspan=2 style="text-align:center;"><strong>Action</strong></td>';
echo'  </tr>';
$query = spip_query ( "SELECT * FROM spip_asso_banques ORDER by id_banque" );
$class="pair";
while ($data = spip_fetch_array($query))

{
echo '<tr> ';
echo '<td class ='.$class.'>'.$data['id_banque'].'</td>';
echo '<td class ='.$class.'>'.$data['valeur'].'</td>';
echo '<td class ='.$class.'>'.$data['intitule'].'</td>';
echo '<td class ='.$class.'>'.$data['reference'].'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.number_format($data['solde'], 2, ',', ' ').' &euro;</td>';
echo '<td class ='.$class.'>'.association_datefr($data['date']).'</td>';
echo '<td class ='.$class.' style="text-align:center;"><a href="'.$url_action_banques.'&action=supprime&id='.$data['id_banque'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/poubelle-12.gif" title="Supprimer"></a></td>';
echo '<td class ='.$class.' style="text-align:center;"><a href="'.$url_edit_banque.'&id='.$data['id_banque'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Modifier"></a></td>';
echo'  </tr>';
 }     
echo'</table>';

echo '<div align="center"><br>';
echo '<form action="'.$url_ajout_banque.'" method="POST">';
echo '<input type="submit" name="Submit" value="Ajouter" class="fondo">';
echo '<br>';
echo '</div>';

fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>
