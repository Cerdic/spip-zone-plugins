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

$nbfeuille=4;
$nomfeuille[1]= "style01.css";
$nomfeuille[2]="style02.css";
$nomfeuille[3]="style03.css";
$nomfeuille[4]="style04.css";

srand((double)microtime()*1000000);

$feuille=rand(1,$nbfeuille);
//fin choix aleatoire
function exec_essai(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour Association'), "", "");


$url_asso = generer_url_ecrire('association');
$url_relance = generer_url_ecrire('relance');
$url_essai = generer_url_ecrire('essai');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Tous les membres &agrave; relancer'));
debut_boite_info();


	print('Nous sommes le '.date('d-m-Y').'');

//Bricolage?
if ( isset ($_POST['statut'] )) {
	$statut = $_POST['statut']; }
	else { $statut= "echu"; }
		
		
echo '<table width="70%" border="0">';
echo '<tr>';
echo '<td colspan="15">';

echo '<td style="text-align:right;">';

// Menu de sélection
echo '<form method="post" action="#">';
echo '<input type="hidden" name="lettre" value="'.$lettre.'">';
echo '<select name ="statut" class="fondl" onchange="form.submit()">';
echo '<option value = "ok" ';
	if ($statut=="ok") {echo 'selected';}
	echo ' > A jour';
echo '<option value = "echu" ';
	if ($statut=="echu") {echo 'selected';}
	echo ' > A relancer';
echo '<option value = "relance" ';
	if ($statut=="relance") {echo 'selected';}
	echo ' > Relanc&eacute;';
echo '<option value = "prospect" ';
	if ($statut=="prospect") {echo 'selected';}
	echo ' > Prospect';
echo '</select>';
echo '</form>';

echo '<form method="post" action="'.$url_relance.'">';
echo '<tr bgcolor="#D9D7AA">';
echo '<td><strong>ID</strong></td>';
echo '<td><strong>Nom</strong></td>';
echo '<td><strong>Pr&eacute;nom</strong></td>';
echo '<td><strong>T&eacute;l&eacute;phone</strong></td>';
echo '<td><strong>Portable</strong></td>';
//echo '<td><strong>A jour</strong></td>';
echo '<td><strong>Validit&eacute;</strong></td>';
//echo '<td><strong>Relance</strong></td>';
echo '<td><strong>Env</strong></td>';
echo '</tr>';
$query = "SELECT * FROM spip_asso_adherents WHERE email <> ''  AND statut like '$statut' AND statut <> 'sorti' ORDER by nom" ;
$val = spip_query (${query}) ;
 $i=0;
while ($data = mysql_fetch_assoc($val))
   {
	$i++;
$id_adherent=$data['id_adherent'];

	switch($data['statut'])
    {
    case "echu":
        $class= "impair";
        break;
    case "ok":
        $class="valide";
	   break;
    case "relance":
        $class="pair";	   
        break;
    case "prospect":
		$class="prospect";	   
        break;	   
     }

echo '<tr> ';
echo '<td class ='.$class.' style="text-align:right">'.$data['id_adherent'].'</td>';
echo '<td class ='.$class.'>'.$data["nom"].'</td>';
// echo '   <td class ='.$class.'></td>';
echo '<td class ='.$class.'>'.$data['prenom'].'</td>';
echo '<td class ='.$class.'>'.$data['telephone'].'</td>';
echo '<td class ='.$class.'>'.$data['portable'].'</td>';
//echo '<td class ='.$class.' style="text-align:center;"><img src="/ecrire/img_pack/'.$puce.'" title="'.$title.'"></td>';
echo '<td class ='.$class.'>'.datefr($data['validite']).'</td>';
//echo '<td class ='.$class.'>'.$data['rappel'].'</td>';
echo '<td class ='.$class.' style="text-align:center;">';
echo '<input name="relance[]" type="checkbox" value="'.$data['id_adherent'].'" checked >';
echo '<input type="hidden" name="statut[]" value="'.$statut.'">';
echo '<input type="hidden" name="email[]" value="'.$data["email"].'">';
echo '</td>';
echo '</tr>';
  }
echo '</table>';
echo '<p></p>';
echo '<BR/>';
//echo '<p> '; 
//$total =$i;  
//echo '<font color="blue"><strong>Nombre de relance :',$i,'</strong></font><BR/>';
//echo '</p>';

echo '<fieldset>';
echo '<legend>Envoyer une relance de cotisation</legend>';
echo '<table class="noclass">';
echo '<tr> ';
echo '<td>Sujet</td>';
echo '<td > <input name="sujet" type="text" size="66" value="'.stripslashes(_T('asso:titre_relance')).'"></td>';
echo '</tr>';
echo '<tr> ';
echo '<td style="vertical-align:top;">Message</td>';
echo '<td><textarea name="message" cols="50" rows="15">'.stripslashes(_T('asso:message_relance')).'</textarea> </td>';
echo '</tr>';
echo '<tr> ';
echo '<td>&nbsp;</td>';
echo '<td><input type="submit" name="Submit" value="Envoyer" class="fondo"></td>';
echo '</tr>';
echo '</table>';
echo '</fieldset>';
echo '</form>';
  
   fin_boite_info();
  fin_cadre_relief();  
fin_page();
}
?>