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

function exec_edit_vente(){
global $connect_statut, $connect_toutes_rubriques;

$url_action_ventes = generer_url_ecrire('action_ventes');

debut_page();

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Mise &agrave; jour des ventes'));
	debut_boite_info();

print association_date_du_jour();
	
$id_vente= $_GET['id'];

$query = spip_query ("SELECT * FROM spip_asso_ventes INNER JOIN spip_asso_comptes ON id_vente=id_journal WHERE id_vente=$id_vente AND imputation='vente' " );
echo '<form method="post" action="'.$url_action_ventes.'">';
echo '<fieldset><legend>Mettre &agrave; jour une vente </legend>';
echo '<table width="70%" class="noclass">'; 

while ($data = spip_fetch_array($query))
{
echo '<tr> ';
echo '<td>Vente n&deg; :</td>';
echo '<td><input name="id_vente" type="text"  size="3" readonly="true" value="'.$data['id_vente'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Date (AAAA-MM-JJ) :</td>';
echo '<td><input name="date_vente" type="text" value="'.$data['date_vente'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Article :</td>';
echo '<td><input name="article"  type="text" size="40" value="'.$data['article'].'"> </td>';
echo '</tr>';
echo '<td>Code :</td>';
echo '<td><input name="code"  type="text" value="'.$data['code'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Nom de l\'acheteur :</td>';
echo '<td><input name="acheteur" type="text" size="40" value="'.$data['acheteur'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Quantit&eacute; achet&eacute;e :</td>';
echo '<td><input name="quantite"  type="text" value="'.$data['quantite'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Prix de vente(en &euro;) :</td>';
echo '<td><input name="prix_vente"  type="text" value="'.$data['prix_vente'].'"> </td>';
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
echo '<td>Don :</td>';
echo '<td><input name="don" type="text" value="'.$data['don'].'"> </td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr>';
echo '<td>Envoy&eacute; le (AAAA-MM-JJ) :</td>';
echo '<td><input name="date_envoi"  type="text" value="'.$data['date_envoi'].'"></td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Frais d\'envoi (en &euro;) :</td>';
echo '<td><input name="frais_envoi" type="text" value="'.$data['frais_envoi'].'"> </td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>Commentaires :</td>';
echo '<td><textarea name="commentaire" cols="30" rows="3">'.$data['commentaire'].'</textarea></td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td><input name="action" type="hidden" value="modifie">';
echo '<input name="submit" type="submit" value="Envoyer" class="fondo"></td>';
echo '</tr>';
	 }
echo '</table>';
echo '</fieldset></form>';

 
fin_boite_info();
	  
  fin_cadre_relief();  
		    

fin_page();
 }  
?>
