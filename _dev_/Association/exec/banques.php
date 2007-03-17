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
include_spip('association_mes_options');

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

print('<p>Nous sommes le '.date('d-m-Y').'</p>');

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
$query = "SELECT * FROM spip_asso_banques ORDER by id_banque" ;
$val = spip_query ($query) ;
$class="pair";
while ($data = mysql_fetch_assoc($val))

{
echo '<tr> ';
echo '<td class ='.$class.'>'.$data['id_banque'].'</td>';
echo '<td class ='.$class.'>'.$data['valeur'].'</td>';
echo '<td class ='.$class.'>'.$data['intitule'].'</td>';
echo '<td class ='.$class.'>'.$data['reference'].'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.$data['solde'].' &euro;</td>';
echo '<td class ='.$class.'>'.datefr($data['date']).'</td>';
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