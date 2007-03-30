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

function exec_ajout_cotisation(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Ajout de cotisation'), "", "");

$url_action_cotisations = generer_url_ecrire('action_cotisations');
$url_retour = $_SERVER['HTTP_REFERER'];

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Tous les membres actifs'));
	debut_boite_info();

print association_date_du_jour();

$id_adherent=$_GET['id'];

$sql = "SELECT * FROM spip_asso_adherents where id_adherent='$id_adherent'";
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());  
    	
echo'   <fieldset><legend>Ajouter une cotisation </legend>';
echo'   <table width="70%" class="noclass">';
echo' <p align="center"><form action="'.$url_action_cotisations.'" method="POST">';

while($data = mysql_fetch_assoc($req)) {
$nom=$data['nom'];
$prenom=$data['prenom'];
$categorie=$data['categorie'];
$validite=$data['validite'];
$split = split("-",$validite); 
$annee = $split[0]; 
$mois = $split[1]; 
$jour = $split[2]; 

echo '<tr>';
echo '<td>Adh&eacute;rent :</td>'; 
echo '<td><strong>'.$data['nom'].' '.$data['prenom'].'</strong></td>';
echo '<tr>';
echo '<td>Cat&eacute;gorie :</td>'; 
echo '<td>'.$data['categorie'].'</td>';

echo '<tr> ';	
echo '<td>&nbsp;</td>';
echo '<td>&nbsp;</td>';
echo '<tr>';

echo '<td>Date du paiement (AAAA-MM-JJ):';
echo '<td><input name="date" type="text" value="'.date('Y-m-d').'">';
echo '<tr> ';
echo '<td>Montant pay&eacute; (en euros):</td>';

$sql = "SELECT * FROM spip_asso_categories WHERE valeur='$categorie'";
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());  

while($data = mysql_fetch_assoc($req)) {
$duree=$data['duree'];
$mois=$mois+$duree;
$validite=date("Y-m-d", mktime(0, 0, 0, $mois, $jour, $annee));

echo '<td><input name="montant" type="text" value="'.$data['cotisation'].'"></td>';
}
echo '<tr>';
echo '<td>Mode de paiement :</td>';
echo '<td><select name="journal" type="text">';
$query = "SELECT * FROM spip_asso_banques ORDER BY id_banque";
$val = spip_query ($query) ;
while ($data = mysql_fetch_assoc($val)) {
echo '<option value="'.$data['code'].'"> '.$data['intitule'].' </option>';
}
echo '<option value="don"> Don </option>';
echo '</select></td>';
echo '<tr>';
echo '<td>Validit&eacute; (AAAA-MM-JJ):';
echo '<td><input name="validite" type="text" value="'.$validite.'">';
echo '<tr> ';
echo '<td>Justification :';
echo '<td><input name="justification" type="text" size="50" value="'.$prenom.' '.$nom.'">';
echo '<input type="hidden" name="id_adherent" value="'.$id_adherent.'">';
echo '<input type="hidden" name="nom" value="'.$nom.'">';
echo '<input type="hidden" name="prenom" value="'.$prenom.'">';
echo '<input type="hidden" name="action" value="ajoute"></td></tr>';
}
echo '</table>';
echo '<tr>';
echo '<td>&nbsp;</td>';
echo '<td><input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input name="submit" type="submit" value="Ajouter" class="fondo"></td></tr>';
echo '</form>';
echo '</table>';
echo '</fieldset>';

// ON FERME TOUT
fin_boite_info();
	  
  fin_cadre_relief();  

fin_page();
}
?>
