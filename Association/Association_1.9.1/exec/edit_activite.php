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

function exec_edit_activite(){
global $connect_statut, $connect_toutes_rubriques;

$url_action_activites = generer_url_ecrire('action_activites');
$url_retour = $_SERVER["HTTP_REFERER"];

debut_page(_T('asso:activite_titre_mise_a_jour_inscriptions'));

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_mise_a_jour_inscriptions'));
	debut_boite_info();

print association_date_du_jour();
	
$id_activite= $_GET['id'];

$query = spip_query ("SELECT * FROM spip_asso_activites WHERE id_activite=$id_activite ");

echo '<form method="post" action="'.$url_action_activites.'">';
echo '<fieldset><legend>'._T('asso:activite_mise_a_jour_inscription').'</legend>';
echo '<table width="70%" class="noclass">'; 

while ($data = spip_fetch_array($query))
{
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_inscription').' :</td>';
echo '<td><input name="id_activite" type="text"  size="3" readonly="true" value="'.$data['id_activite'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_date').' (AAAA-MM-JJ) :</td>';
echo '<td><input name="date" type="text" value="'.$data['date'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_nomcomplet').' :</td>';
echo '<td><input name="nom"  type="text" size="40" value="'.$data['nom'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_adherent').' :</td>';
echo '<td><input name="id_membre" type="text" value="'.$data['id_adherent'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_membres').' :</td>';
echo '<td><input name="membres"  type="text" size="40" value="'.$data['membres'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_non_membres').' :</td>';
echo '<td><input name="non_membres"  type="text" size="40" value="'.$data['non_membres'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_nombre_inscrit').' :</td>';
echo '<td><input name="inscrits"  type="text" value="'.$data['inscrits'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_email').' :</td>';
echo '<td><input name="email"  type="text" size="40" value="'.$data['email'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_telephone').' :</td>';
echo '<td><input name="telephone" type="text" value="'.$data['telephone'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_adresse_complete').' :</td>';
echo '<td><textarea name="adresse" cols="30" rows="3">'.$data['adresse'].'</textarea></td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr> ';
echo '<td>'._T('asso:activite_libelle_montant_inscription').' :</td>';
echo '<td><input name="montant"  type="text" value="'.$data['montant'].'"> </td>';
echo '</tr>';
echo '<tr>';
echo '<td>'._T('asso:activite_libelle_statut').' :</td>';
echo '<td><input name="statut"  type="checkbox" value="ok"';
	if ($data['statut']=='ok') { echo ' checked="checked"'; }
echo '> ok</td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>'._T('asso:activite_libelle_commentaires').' :</td>';
echo '<td><textarea name="commentaire" cols="30" rows="3">'.$data['commentaire'].'</textarea></td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td><input name="action" type="hidden" value="modifie">';
echo '<input name="id_evenement" type="hidden" value="'.$data['id_evenement'].'">';
echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input name="submit" type="submit" value="'._T('asso:activite_bouton_envoyer').'" class="fondo"></td>';
echo '</tr>';
	 }
echo '</table>';
echo '</fieldset></form>';

 
fin_boite_info();
	  
  fin_cadre_relief();  
		    

fin_page();
 }  
?>
