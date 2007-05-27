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

function exec_edit_compte(){
global $connect_statut, $connect_toutes_rubriques;

$url_action_comptes =generer_url_ecrire('action_comptes');
$url_retour = $_SERVER["HTTP_REFERER"];

debut_page();
include_spip ('inc/navigation');
debut_cadre_relief(  "", false, "", $titre = _T('Modification des comptes'));

debut_boite_info();
print association_date_du_jour();

$id_compte=$_GET['id'];

echo '<form action="'.$url_action_comptes.'" method="POST">';
echo '<fieldset><legend>Mettre &agrave; jour l\'op&eacute;ration #'.$id_compte.'</legend>';
echo '<table width="70%" class="noclass">';
	
$query = spip_query ("SELECT * FROM spip_asso_comptes  WHERE id_compte=$id_compte") ;
 $i=0;
while ($data = spip_fetch_array($query))
    {
echo '<input name="id_compte"  value="'.$data['id_compte'].'" type="hidden">';
echo '<tr> ';
echo '<td>Imputation :</td>';
echo '<td><input name="imputation"  readonly="true" value='.$data['imputation'].'   /> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Date :</td>';
echo '<td><input name="date" value="'.$data['date'].'" type="text"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Recette:</td>';
echo '<td><input name="recette" value="'.$data['recette'].'" type="text" > </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>D&eacute;pense :</td>';
echo '<td><input name="depense" value="'.$data['depense'].'"  type="text" > </td>';
echo '</tr>';
echo '<tr>';
echo '<td>Mode de paiement :</td>';
echo '<td><select name="journal" type="text">';
$sql = spip_query ("SELECT * FROM spip_asso_banques ORDER BY id_banque");
while ($banque = spip_fetch_array($sql)) {
echo '<option value="'.$banque['code'].'" ';
	if ($data['journal']==$banque['code']) { echo ' selected="selected"'; }
echo '>'.$banque['intitule'].'</option>';
}
echo '<option value="don"';
	if ($data["journal"]=="don") { echo ' selected="selected"'; }
echo '> Don </option>';
echo '</select></td>';
echo '</tr>';
echo '<tr>';
echo '<td>Justification :</td>';
echo '<td><input name="justification" value="'.$data['justification'].'" size="40" type="text"></td>';
echo '</tr>';
}
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td><input name="action" type="hidden" value="modifie">';
echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input name="" type="submit" value="Modifier" class="fondo"></td>';
echo '</tr>';
echo '</table>';
echo '</fieldset></form>';


fin_boite_info();

	  
  fin_cadre_relief();  
		    

fin_page();
 }  
?>
