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

function exec_edit_labels(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Gestion pour Association'), "", "");


$url_asso = generer_url_ecrire('association');
$url_action_labels = generer_url_ecrire('action_labels');
$url_edit_relances = generer_url_ecrire('edit_relances');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Toutes les &eacute;tiquettes &agrave; g&eacute;n&eacute;rer'));
debut_boite_info();


	print association_date_du_jour();

	if ( isset ($_POST['statut'] )) 
		{ $statut = $_POST['statut']; }
	else 
		{ $statut= "ok"; }

echo '<table width="70%" border="0">';
echo '<tr>';
// Menu de sélection
echo '<td style="text-align:right;">';
echo '<form method="post" action="#">';
echo '<input type="hidden" name="lettre" value="'.$lettre.'">';
echo '<select name ="statut" class="fondl" onchange="form.submit()">';
echo '<option value="ok"';
	if ($statut=="ok") { echo ' selected="selected"';}
	echo '> A jour';
echo '<option value="echu"';
	if ($statut=="echu") {echo ' selected="selected"';}
	echo '> A relancer';
echo '<option value="relance"';
	if ($statut=="relance") {echo ' selected="selected"';}
	echo '> Relanc&eacute;';
echo '<option value="prospect"';
	if ($statut=="prospect") {echo ' selected="selected"';}
	echo '> Prospect';
echo '</select>';
echo '</form>';
echo '</td></tr>';
echo '</table>';

echo '<table style="width:70%;text-align:center;" border="0">';
echo '<form method="post" action="'.$url_action_labels.'">';
echo '<tr bgcolor="#D9D7AA">';
echo '<td><strong>ID</strong></td>';
echo '<td><strong>Cher</strong></td>';
echo '<td><strong>Pr&eacute;nom</strong></td>';
echo '<td><strong>Nom</strong></td>';
echo '<td><strong>Rue</strong></td>';
echo '<td><strong>CP</strong></td>';
echo '<td><strong>Ville;</strong></td>';
echo '<td><strong>Env</strong></td>';
echo '</tr>';
$query = spip_query ("SELECT * FROM spip_asso_adherents WHERE statut like '$statut' ORDER by nom" );
 $i=0;
while ($data = spip_fetch_array($query))
   {
	$i++;
$id_adherent=$data['id_adherent'];
$sexe=$data['sexe'];

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
echo '<td class ='.$class.'>';
if ($sexe=='H'){ echo 'M.'; }
	elseif ($sexe=='F'){ echo 'Mme'; }
		else { echo '&nbsp;'; }
echo '</td>';
echo '<td class ='.$class.'>'.$data['prenom'].'</td>';
echo '<td class ='.$class.'>'.$data["nom"].'</td>';
echo '<td class ='.$class.'>'.$data['rue'].'</td>';
echo '<td class ='.$class.'>'.$data['cp'].'</td>';
echo '<td class ='.$class.'>'.$data['ville'].'</td>';
echo '<td class ='.$class.' style="text-align:center;">';
echo '<input name="label[]" type="checkbox" value="'.$data['id_adherent'].'" checked="checked" />';
echo '</td>';
echo '</tr>';
  }
echo '<tr> ';
echo '<td colspan="8" style="text-align:right;"><input type="submit" name="Submit" value="Etiquettes" class="fondo"></td>';
echo '</tr>';
echo '</table>';
echo '</form>';
  
   fin_boite_info();
  fin_cadre_relief();  
fin_page();
}
?>
