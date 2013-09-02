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
function exec_edit_categorie(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Cat&eacute;gories de cotisation'), "", "");
$url_asso = generer_url_ecrire('association');
$url_ajouter = generer_url_ecrire('ajouter');
$url_relance = generer_url_ecrire('essai');
$url_bienfaiteur = generer_url_ecrire('bienfaiteur');
$url_vente = generer_url_ecrire('ventes');
$url_banque = generer_url_ecrire('banque');
$url_delete = generer_url_ecrire('delete_membre');
$url_action_categorie=generer_url_ecrire('action_categorie');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Toutes les cat&eacute;gories de cotisation'));
	debut_boite_info();

print association_date_du_jour();

$action=$_GET['action'];
$id=$_GET['id'];
$query = spip_query( "SELECT * FROM spip_asso_categories WHERE id_categorie='$id' ");
	
echo '<fieldset><legend>Modifier une cat&eacute;gorie de cotisation</legend>';
echo '<table width="70%">';	
echo '<form action="'.$url_action_categorie.'" method="post">';	

	while($data = spip_fetch_array($query)) 
{
echo '<tr> ';
echo '<td>Num&eacute;ro :</td>';
echo '<td><input name="id" type="text" size="3" readonly="true" value="'.$data['id_categorie'].'"></td></tr>';
echo '<tr> ';
echo '<td>Cat&eacute;gorie :</td>';
echo '<td><input name="valeur" type="text" value="'.$data['valeur'].'"></td></tr>';
echo '<tr> ';
echo '<td>Libell&eacute; complet :</td>';
echo '<td><input name="libelle" type="text" size="50" value="'.$data['libelle'].'"></td>';
echo '<tr> ';
echo '<td>Dur&eacute;e (en mois) :</td>';
echo '<td><input name="duree" type="text" value="'.$data['duree'].'"></td>';
echo '<tr> ';
echo '<td>Montant (en euros) :</td>';
echo '<td><input name="montant" type="text" value="'.$data['cotisation'].'"></td></tr>';
echo '<tr> ';      
echo '<td>Commentaires :</td>';
echo '<td colspan="3"><textarea name="commentaires" cols="38" rows="3">'.$data["commentaires"].'</textarea>';
echo '<input type="hidden" name="action" value="modifie"></td></tr>';
}
echo '<tr>';
echo '<td></td>';
echo '<td><input name="submit" type="submit" value="Modifier" class="fondo"></td></tr>';
echo '</form>';
echo '</table>';
echo '</fieldset>';


fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>
