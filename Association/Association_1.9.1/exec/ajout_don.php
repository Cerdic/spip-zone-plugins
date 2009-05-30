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

function exec_ajout_don(){
global $connect_statut, $connect_toutes_rubriques;
debut_page(_T('Gestion pour  Association'), "", "");

$url_asso = generer_url_ecrire('association');
$url_ajout_don = generer_url_ecrire('ajout_don');
$url_action_dons = generer_url_ecrire('action_dons');
$url_retour = $_SERVER["HTTP_REFERER"];

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Ajout de don'));
	debut_boite_info();
	
print association_date_du_jour();

echo '<p><form action="'.$url_action_dons.'" method="POST">';
echo '<fieldset>';
echo '<legend>Ajouter un don</legend>';
echo '<table width="70%" class="noclass">';
echo '<tr> ';
echo '<td>Date</td>';
echo '<td > <input name="date_don" type="text" value="'.date('Y-m-d').'"></td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Nom du bienfaiteur</td>';
echo '<td > <input name="bienfaiteur" type="text" size="50"></td>';
echo '</tr>';
echo '<td>Don financier (en &euro;)</td>';
echo '<td><input name="argent" type="text"></td>';
echo '</tr>';
echo '<tr>';
echo '<td>Mode de paiement :</td>';
echo '<td><select name="journal" type="text">';
$query = spip_query ( "SELECT * FROM spip_asso_banques ORDER BY id_banque" );
while ($data = spip_fetch_array($query)) {
echo '<option value="'.$data['code'].'"> '.$data['intitule'].' </option>';
}
echo '<option value="don"> Don </option>';
echo '</select></td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Colis</td>';
echo '<td><input name="colis" type="text"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Contre-valeur (en &euro;)</td>';
echo '<td><input name="valeur" type="text"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Geste de l\'association</td>';
echo '<td><input name="contrepartie" type="text" size="50"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Remarques </td>';
echo '<td><textarea name="commentaire" cols="40"></textarea> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>&nbsp;</td>';
echo '<td><input name="action" type="hidden" value="ajoute">';
echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input name="" type="submit" value="Ajouter" class="fondo"></td>';
echo '</tr>';
echo '</table>';
echo '</fieldset></form></p>';

   fin_boite_info();
  fin_cadre_relief();  
fin_page();
}
?>
