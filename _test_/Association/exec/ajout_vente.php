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

function exec_ajout_vente(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour Association'), "", "");

$url_action_ventes=generer_url_ecrire('action_ventes');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Ajouter des ventes'));
	debut_boite_info();

print association_date_du_jour();


echo '<p align="center"><form action="'.$url_action_ventes.'" method="POST">';
echo '<fieldset><legend>Ajouter une vente </legend>';
echo '<table width="70%" class="noclass">';
echo '<tr> ';
echo '<td>Date (AAAA-MM-JJ) :</td>';
echo '<td><input name="date_vente" type="text" value="'.date('Y-m-d').'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Article :</td>';
echo '<td><input name="article"  type="text" size="40"> </td>';
echo '</tr>';
echo '<td>Code :</td>';
echo '<td><input name="code"  type="text"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Nom de l\'acheteur :</td>';
echo '<td><input name="acheteur" type="text" size="40"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Quantit&eacute; achet&eacute;e :</td>';
echo '<td><input name="quantite"  type="text"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Prix de vente (en &euro;) :</td>';
echo '<td><input name="prix_vente"  type="text"> </td>';
echo '</tr>';
echo '<tr>';
echo '<td>Mode de paiement :</td>';
echo '<td><select name="journal" type="text">';
$query = spip_query ("SELECT * FROM spip_asso_banques ORDER BY id_banque");
while ($data = spip_fetch_array($query)) {
echo '<option value="'.$data['code'].'"> '.$data['intitule'].' </option>';
}
echo '<option value="don"> Don </option>';
echo '</select></td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Don :</td>';
echo '<td><input name="don" type="text"> </td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr>';
echo '<td>Envoy&eacute; le (AAAA-MM-JJ) :</td>';
echo '<td><input name="date_envoi"  type="text"></td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Frais d\'envoi (en &euro;) :</td>';
echo '<td><input name="frais_envoi" type="text"> </td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>Commentaires :</td>';
echo '<td><textarea name="commentaire" cols="30" rows="3"></textarea></td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td><input name="action" type="hidden" value="ajoute">';
echo '<input name="" type="submit" value="Envoyer" class="fondo"></td>';
echo '</tr>';

echo '</table>';
echo '</fieldset></form>';


fin_boite_info();

  fin_cadre_relief();  

fin_page();

}

?>

