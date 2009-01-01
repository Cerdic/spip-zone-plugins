<?php
/**
	 * Kayé
	 * Le cahier de texte électronique spip spécial primaire
	 * Copyright (c) 2007
	 * Cédric Couvrat
	 * http://alecole.ac-poitiers.fr/
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
**/

include_spip('base/create');
include_spip('base/abstract_sql');
include_spip('base/kaye');

	creer_base();	

include_spip('inc/presentation');
function exec_table(){
global $connect_statut, $connect_toutes_rubriques;
debut_page(_T('Cahier de texte'), "", "");
debut_gauche();
echo "<br /><br />";
gros_titre(_T('Cr&eacute;ation des tables'));

debut_cadre_relief();
 $date = date("d-m-Y");
$heure = date("H:i");
Print("Nous sommes le $date et il est $heure");      
echo"<br>";
echo 'Vos nouvelles tables sont cr&eacute;&eacute;es !';

icone_horizontale(_T('retour'), generer_url_ecrire("kaye"), '../'._DIR_PLUGIN_KAYE.'/img_pack/gest_ref.gif');

 fin_cadre_relief();
fin_page();
                        exit;
                }
?>

