﻿<?php
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
include_spip('association_mes_options');

function exec_voir_adherent(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour  Association'), "", "");

// LES URL'S
$url_asso = generer_url_ecrire('association');
$url_edit_compte = generer_url_ecrire('edit_compte');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Fiche signalétique Membre'));
	debut_boite_info();
	
//LE MENU
print ('Nous sommes le '.date('d-m-Y').'');


// FICHE SIGNALETIQUE 	
$id_adherent = $_GET['id'];

$sql = "SELECT * FROM spip_asso_adherents where id_adherent='$id_adherent'";
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());  
	
echo '<fieldset><legend>Fiche signalétique # '.$id_adherent.'</legend>';
echo '<table width="70%">';	
	while($data = mysql_fetch_assoc($req)) 
{
echo '<tr> ';
echo '<td>'._T('asso:reference_interne').' :</td>';
echo '<td><strong>'.$data['id_asso'].'</strong></td>';
echo '<td rowspan=5><img src="/IMG/assologo'.$data['id_adherent'].'"></td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';
echo '<td>Nom :</td>';
echo '<td><strong>'.$data['nom'].'</strong></td>';
echo '<tr> ';
echo '<td>Pr&eacute;nom :</td>';
echo '<td><strong>'.$data['prenom'].'</strong></td></tr>';
echo '<tr> ';
echo '<td>Sexe:</td>';
echo '<td><strong>';
if($data['sexe']=="H") {echo 'Masculin';}
else {echo 'F&eacute;minin';}
echo '</strong></td>';
echo '<tr> ';
echo '<td>Date de naissance:</td>';
echo '<td><strong>'.datefr($data['naissance']).'</strong></td></tr>';
echo '<tr> ';
echo '<td>Cat&eacute;gorie de cotisation:</td>';
echo '<td><strong>'.$data['categorie'].'</strong></td>';
echo '<td>Statut de cotisation :</td>';
echo '<td><strong>';
if ($data['statut']=="ok") {echo 'A jour';}
if ($data['statut']=="echu") {echo 'A &eacute;ch&eacute;ance';}
if ($data['statut']=="relance") {echo 'Relanc&eacute;';}
if ($data['statut']=="sorti") {echo 'D&eacute;sactiv&eacute;';}
if ($data['statut']=="prospect") {echo 'Prospect';}
echo '</strong></td>';
echo '<tr> ';
echo '<td>Fonction :</td>';
echo '<td><strong>'.$data['fonction'].'</strong></td>';
echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';
echo '<td>Email:</td>';
echo '<td colspan="3"><strong>'.$data['email'].'</strong></td></tr>';
echo '<tr> ';
echo '<td style="vertical-align:top;">Adresse :</td>';
echo '<td><strong>'.$data['rue'].'<br>'.$data['cp'].' '.$data['ville'].'</strong></td>';
echo '<td>Portable :<br>T&eacute;l&eacute;phone :</td>';
echo '<td><strong>'.$data["portable"].'<br>'.$data["telephone"].'</strong></td></tr>';
echo '<tr> ';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';
echo '<td>Soci&eacute;t&eacute; :</td>';
echo '<td><strong>'.$data["societe"].'</strong></td>';
echo '<td>Profession :</td>';
echo '<td><strong>'.$data["profession"].'</strong></td></tr>';
echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';	
echo '<td>'._T('asso:secteur').' :</td>';
echo '<td><strong>'.$data["secteur"].'</strong></td>';
echo '<td>Accord de publication :</td>';
echo '<td><strong>'.$data['publication'].'</strong></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:utilisateur1').' :</td>';
echo '<td><strong>'.$data["utilisateur1"].'</strong></td>';
echo '<td>'._T('asso:utilisateur2').' :</td>';
echo '<td><strong>'.$data["utilisateur2"].'</strong></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:utilisateur3').' :</td>';
echo '<td><strong>'.$data["utilisateur3"].'</strong></td>';
echo '<td>'._T('asso:utilisateur4').' :</td>';
echo '<td><strong>'.$data["utilisateur4"].'</strong></td></tr>';
echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
$id_auteur = $data['id_auteur'];
$spip = spip_query("SELECT * FROM spip_auteurs where id_auteur='$id_auteur'");
if ($auteur = mysql_fetch_assoc($spip)) 
echo '<tr> ';	
echo '<td>Visiteur SPIP :</td>';
echo '<td><strong>'.$auteur['nom'].'</strong></td>';
echo '<td>Identifiant :</td>';
echo '<td><strong>'.$auteur['login'].'</strong></td></tr>';
echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';      
echo '<td>Remarques :</td>';
echo '<td colspan="3"><strong>'.$data["remarques"].'</strong></td></tr>';
}
echo '</table>';

echo '<div align="center"><br>';
$link=generer_url_ecrire('edit_adherent',"id=$id_adherent");
icone(_T('asso:Modifier_le_membre'), $link, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','edit.gif' );
echo '</div>';

echo '</fieldset>';

// FICHE HISTORIQUE 	
echo '<fieldset><legend>Historique Cotisations</legend>';
echo '<table width="70%" border="0">';
echo '<tr bgcolor="silver">';
echo '<td style="text-align:right;"><strong>ID</strong></td>';
echo '<td><strong>Date</strong></td>';
echo '<td><strong>Livre</strong></td>';
echo '<td style="text-align:right;"><strong>Paiement</strong></td>';
echo '<td><strong>Justification</strong></td>';
echo '<td><strong>Journal</strong></td>';
echo '<td><strong>&nbsp;</strong></td>';
echo '</tr>';

$query = "SELECT * FROM spip_asso_comptes WHERE id_journal=$id_adherent ORDER BY date DESC";
//$query = "SELECT * FROM spip_asso_comptes WHERE date_format( date, '%Y' ) = '$annee' AND imputation like '$imputation'  ORDER BY date DESC LIMIT $debut,$max_par_page";

$val = spip_query (${query}) ;

while ($data = mysql_fetch_assoc($val)) {

	if ($data['recette'] >0)
    { $class= "pair";}
    else { $class="impair";}	   
  
$somme_recettes += $data['recette'];
$somme_depenses += $data['depense'];

echo '<tr> ';
echo '<td class ='.$class.' style="text-align:right;">'.$data['id_compte'].'</td>';
//echo '<td class ='.$class.'>'.$jour.'-'.$mois. '-'.$annee.'</td>';
echo '<td class ='.$class.'>'.datefr($data['date']).'</td>';
echo '<td class ='.$class.'>'.$data['imputation'].'</td>';
echo '<td class ='.$class.' style="text-align:right;">'.$data['recette'].' &euro;</td>';
echo '<td class ='.$class.'>'.$data['justification'].'</td>';
echo '<td class ='.$class.'>'.$data['journal'].'</td>';
echo '<td class ='.$class.' style="text-align:center"><a href="'.$url_edit_compte.'&id='.$data['id_compte'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Mettre &agrave; jour l\'op&eacuteration"></a></td>';
//echo '<td class ='.$class.'><input name="delete[]" type="checkbox" value='.$data['id_compte'].'></td>';
echo '</tr>';
}
echo '</table>';

// ON FERME TOUT
fin_boite_info();
	  
  fin_cadre_relief();  

fin_page();} 
?>

