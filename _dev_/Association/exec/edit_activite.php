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

debut_page();

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Mise &agrave; jour des inscriptions'));
	debut_boite_info();

	print('Nous sommes le '.date('d/m/Y').'');
	
$id_activite= $_GET['id'];

$query = spip_query ("SELECT * FROM spip_asso_activites RIGHT JOIN spip_asso_comptes ON id_activite=id_journal WHERE id_activite=$id_activite AND imputation='activite' ");
$i=0;

echo '<form method="post" action="'.$url_action_activites.'">';
echo '<fieldset><legend>Mettre &agrave; jour une inscription </legend>';
echo '<table width="70%" class="noclass">'; 

while ($data = mysql_fetch_assoc($query))
{
echo '<tr> ';
echo '<td>Inscription n&deg; :</td>';
echo '<td><input name="id_activite" type="text"  size="3" readonly="true" value="'.$data['id_activite'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Date (AAAA-MM-JJ) :</td>';
echo '<td><input name="date" type="text" value="'.$data['date'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Nom complet :</td>';
echo '<td><input name="nom"  type="text" size="40" value="'.$data['nom'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Adh&eacute;rent :</td>';
echo '<td><select name="id_adherent">';
echo '<option value="0"> -- Invitation ext&eacute;rieure -- </option>';
$query_adh = spip_query ("SELECT id_adherent, CONCAT(nom,' ',prenom,IF((SELECT count(*) FROM spip_asso_activites where spip_asso_adherents.id_adherent=spip_asso_activites.id_adherent AND spip_asso_activites.id_evenement=".$data['id_evenement']."),' (d&eacute;j&agrave; inscrit)','')) as usuel FROM spip_asso_adherents ORDER BY nom,prenom") ;
while ($data_adh = mysql_fetch_assoc($query_adh)) {
print_r($data_adh);
echo '<option value="'.$data_adh['id_adherent'].'"'.($data_adh['id_adherent'] == $data['id_adherent'] ? ' selected="selected"' : '').'>'.$data_adh['usuel'].'</option>';
}
echo '</select></td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Accompagn&eacute; de :</td>';
echo '<td><input name="accompagne"  type="text" size="40" value="'.$data['accompagne'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Nombre d\'inscrits :</td>';
echo '<td><input name="inscrits"  type="text" value="'.$data['inscrits'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Email:</td>';
echo '<td><input name="email"  type="text" size="40" value="'.$data['email'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>T&eacute;l&eacute;phone:</td>';
echo '<td><input name="telephone" type="text" value="'.$data['telephone'].'"> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Adresse compl&egrave;te :</td>';
echo '<td><textarea name="adresse" cols="30" rows="3">'.$data['adresse'].'</textarea></td>';
echo '</tr>';
echo '<tr>'; 
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr> ';
echo '<td>Montant de l\'inscription (en &euro;) :</td>';
echo '<td><input name="montant"  type="text" value="'.$data['montant'].'"> </td>';
echo '</tr>';
echo '<tr>';
echo '<td>Date de paiement (AAAA-MM-JJ) :</td>';
echo '<td><input name="date_paiement" value="'.$data['date_paiement'].'" type="text"> </td>';
echo '</tr>';
echo '<tr>';
echo '<td>Mode de paiement :</td>';
echo '<td><select name="journal" type="text">';
$sql = spip_query ("SELECT * FROM spip_asso_banques ORDER BY id_banque");
while ($banque = mysql_fetch_assoc($sql)) {
echo '<option value="'.$banque['code'].'" ';
	if ($data['journal']==$banque['code']) { echo ' selected="selected"'; }
echo '>'.$banque['intitule'].'</option>';
}
echo '</select></td>';
echo '</tr>';
echo '<tr>';
echo '<td>Statut :</td>';
echo '<td><input name="statut"  type="checkbox" value="ok"';
	if ($data['statut']=='ok') { echo ' checked="checked"'; }
echo '> ok</td>';
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
echo '<input name="id_evenement" type="hidden" value="'.$data['id_evenement'].'">';
echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
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
