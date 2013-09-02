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
function exec_ajout_banque(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Cat&eacute;gories de cotisation'), "", "");
$url_ajout_banque=generer_url_ecrire('ajout_banque');
$url_action_banques=generer_url_ecrire('action_banques');
$url_retour = $_SERVER['HTTP_REFERER'];

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Comptes financiers'));
	debut_boite_info();

print association_date_du_jour();

echo '<fieldset><legend>Ajouter un compte financier</legend>';
echo '<table width="70%">';	
echo '<form action="'.$url_action_banques.'" method="post">';	
echo '<td>Code :</td>';
echo '<td><input name="code" type="text"></td>';
echo '<tr>';
echo '<td>Intitul&eacute; :</td>';
echo '<td><input name="intitule" type="text" size="50"></td></tr>';
echo '<tr> ';
echo '<td>R&eacute;f&eacute;rence :</td>';
echo '<td><input name="reference" type="text" size="50"></td>';
echo '<tr> ';
echo '<td>Solde initial :</td>';
echo '<td><input name="solde" type="text"></td></tr>';
echo '<tr> ';      
echo '<td>Date :</td>';
echo '<td><input name="date" type="text"></td></tr>';
echo '<tr> ';     
echo '<td>Commentaire :</td>';
echo '<td colspan="3"><textarea name="commentaire" cols="40"></textarea>';
echo '<input type="hidden" name="action" value="ajoute"></td></tr>';
echo '<tr>';
echo '<td></td>';
echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
echo '<td><input name="submit" type="submit" value="Ajouter" class="fondo">';
echo '</td></tr>';
echo '</form>';
echo '</table>';
echo '</fieldset>';

fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>
