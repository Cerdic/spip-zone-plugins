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

function exec_ajout_compte(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour Association'), "", "");

$url_action_comptes=generer_url_ecrire('action_comptes');
$url_retour = $_SERVER["HTTP_REFERER"];

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Livre de comptes'));
	debut_boite_info();

print association_date_du_jour();

echo '<p align="center"><form action="'.$url_action_comptes.'" method="POST">';
echo '<fieldset><legend>Ajouter une op&eacute;ration </legend>';
echo '<table width="70%" class="noclass">';
echo '<tr> ';
echo '<td>Date (AAAA-MM-JJ) :</td>';
echo '<td><input name="date" type="text" value="'.date('Y-m-d').'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Imputation :</td>';
echo '<td><select name="imputation" type="text">';
echo '<option value="achat"> Achats </option>';
echo '<option value="divers"> Divers </option>';
echo '</select></td>';
echo '</tr>';
echo '<tr> ';
echo '<td>D&eacute;pense (en &euro;) :</td>';
echo '<td><input name="depense" type="text"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Recette (en &euro;) :</td>';
echo '<td><input name="recette" type="text"> </td>';
echo '</tr>';
echo '<tr>';
echo '<td>Mode de r&egrave;glement :</td>';
echo '<td><select name="journal" type="text">';
$query = spip_query ( "SELECT * FROM spip_asso_banques ORDER BY id_banque" ) ;
while ($data = spip_fetch_array($query)) {
echo '<option value="'.$data['code'].'"> '.$data['intitule'].' </option>';
}
echo '<option value="don"> Don </option>';
echo '</select></td>';
echo '</tr>';

echo '<tr>'; 
echo '<td>Justification :</td>';
echo '<td><input name="justification"  type="text"  size="50"></td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td><input name="action" type="hidden" value="ajoute">';
echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input name="" type="submit" value="Ajouter" class="fondo"></td>';
echo '</tr>';

echo '</table>';
echo '</fieldset></form>';


fin_boite_info();

  fin_cadre_relief();  

fin_page();

}

?>

