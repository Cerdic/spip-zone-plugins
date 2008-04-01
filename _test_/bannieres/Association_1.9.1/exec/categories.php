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
function exec_categories(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Cat&eacute;gories de cotisation'), "", "");
$url_categories = generer_url_ecrire('categories');
$url_ajout_categorie=generer_url_ecrire('ajout_categorie');
$url_edit_categorie=generer_url_ecrire('edit_categorie');
$url_action_categorie=generer_url_ecrire('action_categorie');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Toutes les cat&eacute;gories de cotisation'));
	debut_boite_info();

print association_date_du_jour();

echo '<table width="70%" border="0">';
echo '<tr bgcolor="#D9D7AA">';
echo '<td><strong>ID</strong></td>';
echo '<td><strong>Cat&eacute;gorie</strong></td>';
echo '<td><strong>Libell&eacute; complet</strong></td>';
echo '<td><strong>Dur&eacute;e (mois)</strong></td>';
echo '<td><strong>Montant</strong></td>';
echo '<td><strong>Commentaires</strong></td>';
echo '<td colspan=2 style="text-align:center;"><strong>Action</strong></td>';
echo'  </tr>';
$query = spip_query ( "SELECT * FROM spip_asso_categories ORDER by id_categorie" ) ;
$class="pair";
while ($data = spip_fetch_array($query))

{
echo '<tr> ';
echo '<td class ='.$class.'>'.$data['id_categorie'].'</td>';
echo '<td class ='.$class.'>'.$data['valeur'].'</td>';
echo '<td class ='.$class.'>'.$data['libelle'].'</td>';
echo '<td class ='.$class.'>'.$data['duree'].'</td>';
echo '<td class ='.$class.'>'.$data['cotisation'].'</td>';
echo '<td class ='.$class.'>'.$data['commentaires'].'</td>';
echo '<td class ='.$class.' style="text-align:center;"><a href="'.$url_action_categorie.'&action=supprime&id='.$data['id_categorie'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/poubelle-12.gif" title="Supprimer"></a></td>';
echo '<td class ='.$class.' style="text-align:center;"><a href="'.$url_edit_categorie.'&id='.$data['id_categorie'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Modifier"></a></td>';
echo'  </tr>';
 }     
echo'</table>';

echo '<div align="center"><br>';
echo '<form action="'.$url_ajout_categorie.'" method="POST">';
echo '<input type="submit" name="Submit" value="Ajouter" class="fondo">';
echo '<br>';
echo '</div>';

fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>
