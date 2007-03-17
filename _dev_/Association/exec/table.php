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
include_spip('base/create');
include_spip('base/abstract_sql');
include_spip('base/association');

	creer_base();	

include_spip('inc/presentation');
function exec_table(){
global $connect_statut, $connect_toutes_rubriques;
debut_page(_T('association'), "", "");
debut_gauche();
echo "<br /><br />";
gros_titre(_T('Cr&eacute;ation des tables'));

debut_cadre_relief();
 $date = date("d-m-Y");
$heure = date("H:i");
Print("Nous sommes le $date et il est $heure");      
echo"<br>";
echo 'Vos nouvelles tables sont cr&eacute;&eacute;es !';

icone_horizontale(_T('retour'), generer_url_ecrire("association"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/annonce.gif');

 fin_cadre_relief();
fin_page();
                        exit;
                }
?>

