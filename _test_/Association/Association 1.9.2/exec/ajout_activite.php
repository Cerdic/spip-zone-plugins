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

debut_page(_T('asso:titre_gestion_pour_association'), "", "");

$url_action_activites=generer_url_ecrire('action_activites');
$url_retour = $_SERVER["HTTP_REFERER"];

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_ajouter_inscriptions'));
debut_boite_info();

print association_date_du_jour();

$id_evenement=preg_replace("/[^0-9]/","",$_GET['id']);

echo '<p align="center"><form action="'.$url_action_activites.'" method="POST">';
echo '<fieldset><legend>'._T('asso:activite_ajouter_inscription').'</legend>';
echo '<table width="70%" class="noclass">';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_date').' (AAAA-MM-JJ) :</td>';
echo '<td><input name="date" type="text" value="'.date('Y-m-d').'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_nomcomplet').' :</td>';
echo '<td><input name="nom"  type="text" size="40"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_adherent').' :</td>';
echo '<td><input name="id_membre" type="text"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_membres').' :</td>';
echo '<td><input name="membres"  type="text" size="40"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_non_membres').' :</td>';
echo '<td><input name="non_membres"  type="text" size="40"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_nombre_inscrit').' :</td>';
echo '<td><input name="inscrits"  type="text"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_email').' :</td>';
echo '<td><input name="email"  type="text" size="40"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_telephone').' :</td>';
echo '<td><input name="telephone" type="text"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_adresse_complete').' :</td>';
echo '<td><textarea name="adresse" cols="30" rows="3"></textarea></td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_montant_inscription').' :</td>';
echo '<td><input name="montant"  type="text"> </td>';
echo '</tr>';
echo '<tr>';
echo '<td>'._T('asso:activite_libelle_statut').' :</td>';
echo '<td><input name="statut"  type="checkbox" value="ok" unchecked>ok</td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>'._T('asso:activite_libelle_commentaires').' :</td>';
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
echo '<input name="" type="submit" value="'._T('asso:activite_bouton_ajouter').'" class="fondo">';
echo '</td>';
echo '</tr>';

echo '</table>';
echo '</fieldset></form>';


fin_boite_info();

  fin_cadre_relief();  

fin_page();

}

?>

