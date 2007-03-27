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

function exec_ajout_activite(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour Association'), "", "");

$url_action_activites=generer_url_ecrire('action_activites');
$url_retour = $_SERVER["HTTP_REFERER"];

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Ajouter des inscriptions'));
	debut_boite_info();

print('<p>Nous sommes le '.date('d/m/Y').'</p>');

$id_evenement=$_GET['id'];

echo '<p align="center"><form action="'.$url_action_activites.'" method="POST">';
echo '<fieldset><legend>Ajouter une inscription </legend>';
echo '<table width="70%" class="noclass">';
echo '<tr> ';
echo '<td>Date (AAAA-MM-JJ) :</td>';
echo '<td><input name="date" type="text" value="'.date('Y-m-d').'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Nom complet :</td>';
echo '<td><input name="nom"  type="text" size="40"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Num&eacute;ro d\'adh&eacute;rent :</td>';
echo '<td><input name="id_adherent"  type="text" size="40"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Accompagn&eacute; de :</td>';
echo '<td><input name="accompagne"  type="text" size="40"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Nombre d\'inscrits :</td>';
echo '<td><input name="inscrits"  type="text"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Email:</td>';
echo '<td><input name="email"  type="text" size="40"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>T&eacute;l&eacute;phone:</td>';
echo '<td><input name="telephone" type="text"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Adresse compl&egrave;te :</td>';
echo '<td><textarea name="adresse" cols="30" rows="3"></textarea></td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Montant de l\'inscription (en &euro;) :</td>';
echo '<td><input name="montant"  type="text"> </td>';
echo '</tr>';
echo '<tr>';
echo '<td>Date de paiement (AAAA-MM-JJ) :</td>';
echo '<td><input name="date_paiement" value="'.date('Y-m-d').'" type="text"> </td>';
echo '</tr>';
echo '<tr>';
echo '<td>Mode de paiement :</td>';
echo '<td><select name="journal" type="text">';
$query = spip_query ("SELECT * FROM spip_asso_banques ORDER BY id_banque") ;
while ($data = mysql_fetch_assoc($query)) {
echo '<option value="'.$data['code'].'"> '.$data['intitule'].' </option>';
}
echo '</select></td>';
echo '</tr>';
echo '<tr>';
echo '<td>Statut :</td>';
echo '<td><input name="statut"  type="checkbox" value="ok" unchecked>ok</td>';
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
echo '<td>';
echo '<input name="action" type="hidden" value="ajoute">';
echo '<input name="id_evenement" type="hidden" value="'.$id_evenement.'">';
echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input name="" type="submit" value="Ajouter" class="fondo">';
echo '</td>';
echo '</tr>';

echo '</table>';
echo '</fieldset></form>';


fin_boite_info();

  fin_cadre_relief();  

fin_page();

}

?>

