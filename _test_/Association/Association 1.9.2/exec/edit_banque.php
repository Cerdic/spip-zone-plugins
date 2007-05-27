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

function exec_edit_banque(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Cat&eacute;gories de cotisation'), "", "");
$url_asso = generer_url_ecrire('association');
$url_banques = generer_url_ecrire('banques');
$url_action_banques=generer_url_ecrire('action_banques');
$url_retour = $_SERVER['HTTP_REFERER'];

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Comptes financiers'));
	debut_boite_info();

print association_date_du_jour();

$action=$_GET['action'];
$id=$_GET['id'];

$query = spip_query( "SELECT * FROM spip_asso_banques WHERE id_banque='$id' ");
	
echo '<fieldset><legend>Modifier un compte financier</legend>';
echo '<table width="70%">';	
echo '<form action="'.$url_action_banques.'" method="post">';	

	while($data = spip_fetch_array($query)) 
{
echo '<tr>';
echo '<td>Num&eacute;ro :</td>';
echo '<td><input name="id" type="text" size="3" readonly="true" value="'.$data['id_banque'].'"></td></tr>';
echo '<tr>';
echo '<td>Code :</td>';
echo '<td><input name="code" type="text" value="'.$data['valeur'].'"></td></tr>';
echo '<tr>';
echo '<td>Intitul&eacute; :</td>';
echo '<td><input name="intitule" type="text" size="50" value="'.$data['intitule'].'"></td>';
echo '<tr> ';
echo '<td>R&eacute;f&eacute;rence :</td>';
echo '<td><input name="reference" type="text" size="50" value="'.$data['reference'].'"></td>';
echo '<tr> ';
echo '<td>Solde (en euros) :</td>';
echo '<td><input name="solde" type="text" value="'.$data['solde'].'"></td></tr>';
echo '<tr> ';
echo '<td>Date (AAA-MM-JJ) :</td>';
echo '<td><input name="date" type="text" value="'.$data['date'].'"></td></tr>';
echo '<tr> ';      
echo '<td>Commentaires :</td>';
echo '<td colspan="3"><textarea name="commentaire" cols="38" rows="3">'.$data["commentaire"].'</textarea>';
echo '<input type="hidden" name="action" value="modifie"></td></tr>';
}
echo '<tr>';
echo '<td></td>';
echo '<td><input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<input name="submit" type="submit" value="Modifier" class="fondo"></td></tr>';
echo '</form>';
echo '</table>';
echo '</fieldset>';


fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>
