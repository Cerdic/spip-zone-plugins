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
debut_cadre_formulaire();
$link= generer_url_ecrire('adherents');
$link1= generer_url_ecrire('ajout_adherent');
$link2= generer_url_ecrire('dons');
$link3= generer_url_ecrire('edit_relances');
$link4= generer_url_ecrire('ventes');
$link5= generer_url_ecrire('activites');
$link6= generer_url_ecrire('comptes');

//$link7= generer_url_ecrire('prets');

echo '<table width="70%" border="0">';
echo '<tr>';
echo '<td>';
icone_horizontale(_T('asso:menu2_titre_gestion_membres'), $link, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' ); 
echo '</td>';

echo '<td>';
icone_horizontale(_T('asso:menu2_titre_ajouter_membre'),$link1, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','creer.gif');
echo '</td>';

echo '<td>';
	icone_horizontale(_T('asso:menu2_titre_relances_cotisations'),$link3,  '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ico_panier.png','rien.gif' ); 
echo '</td>';

if (lire_config("association/dons")) {
	echo '<td>';
	icone_horizontale(_T('asso:menu2_titre_gestion_dons'), $link2, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/bienfaiteur.png','rien.gif' ); 
	echo '</td>';
}
if (lire_config("association/ventes")) {
	echo '<td>';
	icone_horizontale(_T('asso:menu2_titre_ventes_asso'), $link4, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/journaux.png','rien.gif' ); 
	echo '</td>';
}
if (lire_config("association/activites")) {
	echo '<td>';
	icone_horizontale(_T('asso:menu2_titre_gestion_activites'), $link5, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' ); 
	echo '</td>';
}
if (lire_config("association/comptes")) {
	echo '<td>';
	icone_horizontale(_T('asso:menu2_titre_livres_comptes'), $link6, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' ); 
	echo '</td>';
}
	echo '</tr>';
	echo '</table>';

fin_cadre_formulaire();

?>
