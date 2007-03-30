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
function exec_ajout_adherent() {
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Ajout d\'adh&eacute;rent'), "", "");

$url_asso = generer_url_ecrire('association');
$url_action_adherents = generer_url_ecrire('action_adherents');
$url_retour = $_SERVER['HTTP_REFERER'];

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Ajouter des  membres actifs'));
	debut_boite_info();

print  association_date_du_jour();

echo' <p align="center"><form action="'.$url_action_adherents.'" method="POST">';

//FORMULAIRE MEMBRE
echo'   <fieldset><legend>Ajouter un membre </legend>';
echo'   <table width="70%" class="noclass">';
echo '<tr> ';
echo '<td>'._T('asso:reference_interne').' :</td>';
echo '<td><input name="id_asso" type="text"></td></tr>';
echo '<tr> ';
echo '<td>Nom :</td>';
echo '<td><input name="nom" type="text" size="40"></td>';
echo '<tr> ';
echo '<td>Pr&eacute;nom :</td>';
echo '<td><input name="prenom" type="text" size="40"></td></tr>';
echo '<tr> ';
echo '<td>Sexe:</td>';
echo '<td><input name="sexe" type="radio" value="H"> H ';
echo '<input name="sexe" type="radio" value="F"> F ';
echo '<tr> ';
echo '<td>Date de naissance: (AAAA-MM-JJ)</td>';
echo '<td><input name="naissance" type="text"></td></tr>';
echo '<tr> ';
echo '<td>Cat&eacute;gorie :</td>';
echo '<td><select name="categorie" type="text">';
echo '<option value = ""> Choisissez une cat&eacute;gorie de cotisation</option>';
$sql = "SELECT * FROM spip_asso_categories";
$req = spip_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
while($categorie = mysql_fetch_assoc($req)) 
{
echo '<option value = "'.$categorie["valeur"].'"> '.$categorie["libelle"].'</option>';
}
echo '</select>';
echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';
echo '<td>Email:</td>';
echo '<td colspan="3"><input name="email" type="text" size="40"></td></tr>';
echo '<tr> ';
echo '<td>Fonction :</td>';
echo '<td><input name="fonction" type="text" size="40" value=""></td>';
echo '<tr> ';
echo '<td>Rue :</td>';
echo '<td><textarea name="rue" cols="30"></textarea>';
//echo '<td>N&deg; :</td>';
//echo '<td><input name="numero" type="text" size="10"></td></tr>';
echo '<tr> ';
echo '<td>Ville:</td>';
echo '<td><input name="ville" type="text" size="40"></td>';
echo '<td>Code Postal:</td>';
echo '<td><input name="cp" type="text"></td></tr>';
echo '<tr> ';
echo '<td>Portable :</td>';
echo '<td><input name="portable" type="text"></td>';
echo '<td>T&eacute;l&eacute;phone :</td>';
echo '<td><input name="telephone" type="text"></td></tr>';
echo '<tr> ';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';
echo '<td>Profession :</td>';
echo '<td><input name="profession" type="text"  size= "40"></td>';
echo '<tr> ';	
echo '<td>Soci&eacute;t&eacute; :</td>';
echo '<td><input name="societe" type="text" size= "40"></td></tr>';
echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';	
echo '<td>'._T('asso:secteur').' :</td>';
echo '<td><input name="secteur" type="text"></td>';
echo '<td>Accord de publication :</td>';
echo '<td><input name="publication" type="radio" value="oui" checked>oui';
echo '<input name="publication" type="radio" value="non" unchecked>non</td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:utilisateur1').' :</td>';
echo '<td><input name="utilisateur1" type="text"></td>';
echo '<td>'._T('asso:utilisateur2').' :</td>';
echo '<td><input name="utilisateur2" type="text"></td></tr>';
echo '<tr> ';
echo '<td>'._T('asso:utilisateur3').' :</td>';
echo '<td><input name="utilisateur3" type="text"></td>';
echo '<td>'._T('asso:utilisateur4').' :</td>';
echo '<td><input name="utilisateur4" type="text"></td></tr>';
echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td></tr>';
echo '<tr> ';      
echo '<td>Remarques :</td>';
echo '<td colspan="3"><textarea name="remarques" cols="65" rows="3"></textarea></td></tr>';
echo '<td><input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input type="hidden" name="action" value="ajoute"></td></tr>';
echo '<td><input name="" type="submit" value="Envoyer" class="fondo"</tr>';
echo'</table>';
echo '</form>';

echo' </fieldset>';

fin_boite_info();
	
  fin_cadre_relief();  

fin_page();
}

?>

