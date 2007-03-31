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

function exec_edit_don(){
global $connect_statut, $connect_toutes_rubriques;

$url_action_dons = generer_url_ecrire('action_dons');
$url_retour = $_SERVER['HTTP_REFERER'];

debut_page();

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Mise &agrave; jour des dons'));
	debut_boite_info();

print association_date_du_jour();
	
$id_don= $_GET['id'];

$query = spip_query ("SELECT * FROM spip_asso_dons INNER JOIN spip_asso_comptes ON id_don=id_journal WHERE id_don=$id_don AND imputation='don' ");
$i=0;

echo '<form method="post" action="'.$url_action_dons.'">';
echo '<fieldset><legend>Mettre &agrave; jour un don </legend>';
echo '<table width="70%" class="noclass">'; 

while ($data = spip_fetch_array($query))
{
echo '<tr> ';
echo '<td>Don n&deg; :</td>';
echo '<td><input name="id_don" type="text"  size="3" readonly="true" value="'.$data['id_don'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Date (AAAA-MM-JJ) :</td>';
echo '<td><input name="date_don" type="text" value="'.$data['date_don'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Nom du bienfaiteur</td>';
echo '<td > <input name="bienfaiteur" type="text" size="50" value="'.$data['bienfaiteur'].'"></td>';
echo '</tr>';
echo '<td>Don financier (en &euro;)</td>';
echo '<td><input name="argent" type="text" value="'.$data['argent'].'"></td>';
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
echo '<tr> ';
echo '<td>Colis</td>';
echo '<td><input name="colis" type="text" value="'.$data['colis'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Contre-valeur (en &euro;)</td>';
echo '<td><input name="valeur" type="text" value="'.$data['valeur'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Geste de l\'association</td>';
echo '<td><input name="contrepartie" type="text" size="50" value="'.$data['contrepartie'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Remarques </td>';
echo '<td><textarea name="commentaire" cols="40">'.$data['commentaire'].'</textarea> </td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td><input name="action" type="hidden" value="modifie">';
echo '<td><input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input name="submit" type="submit" value="Mettre &agrave; jour" class="fondo"></td>';
echo '</tr>';
	 }
echo '</table>';
echo '</fieldset></form>';

 
fin_boite_info();
	  
  fin_cadre_relief();  
		    

fin_page();
 }  
?>
