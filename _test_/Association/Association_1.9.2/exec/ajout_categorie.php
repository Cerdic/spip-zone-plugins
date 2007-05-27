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
function exec_ajout_categorie(){
global $connect_statut, $connect_toutes_rubriques;

debut_page(_T('Cat&eacute;gories de cotisation'), "", "");
$url_ajout_categorie=generer_url_ecrire('ajout_categorie');
$url_action_categorie=generer_url_ecrire('action_categorie');

include_spip ('inc/navigation');

debut_cadre_relief(  "", false, "", $titre = _T('Toutes les cat&eacute;gories de cotisation'));
	debut_boite_info();

print association_date_du_jour();

echo '<fieldset><legend>Ajouter une cat&eacute;gorie de cotisation</legend>';
echo '<table width="70%">';	
echo '<form action="'.$url_action_categorie.'" method="post">';	
echo '<tr> ';
echo '<td>Cat&eacute;gorie :</td>';
echo '<td><input name="valeur" type="text"></td></tr>';
echo '<tr> ';
echo '<td>Libell&eacute; complet :</td>';
echo '<td><input name="libelle" type="text" size="50"></td>';
echo '<tr> ';
echo '<td>Dur&eacute;e (en mois) :</td>';
echo '<td><input name="duree" type="text" value="12"></td>';
echo '<tr> ';
echo '<td>Montant (en euros) :</td>';
echo '<td><input name="montant" type="text"></td></tr>';
echo '<tr> ';      
echo '<td>Commentaires :</td>';
echo '<td colspan="3"><textarea name="commentaires" cols="40"></textarea>';
echo '<input type="hidden" name="action" value="ajoute"></td></tr>';
echo '<tr>';
echo '<td></td>';
echo '<td><input name="submit" type="submit" value="Ajouter" class="fondo"></td></tr>';
echo '</form>';
echo '</table>';
echo '</fieldset>';

fin_boite_info();

fin_cadre_relief();  

fin_page();
}
?>
