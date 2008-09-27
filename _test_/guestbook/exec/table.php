<?php
/**
	 * Livre d'or
	 *
	 * Copyright (c) 2008
	 * Bernard Blazin  http://www.libertyweb.info & Yohann Prigent (potter64)
	 * http://www.plugandspip.com 
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
include_spip('base/create');
include_spip('base/abstract_sql');
include_spip('base/livre');

	creer_base();	

include_spip('inc/presentation');
function exec_table(){
global $connect_statut, $connect_toutes_rubriques;
debut_page(_T('livre:lelivre'), '', '');
debut_gauche();
echo "<br /><br />";
gros_titre(_T('livre:creation_des_tables_mysql'));

debut_cadre_relief();
$date = date("d-m-Y");
$heure = date("H:i");
echo"<br />";
echo _T('livre:installhd');

icone_horizontale(_T('livre:retour'), generer_url_ecrire("livre"), '../'._DIR_PLUGIN_LIVRE.'/img_pack/livredor.png');

 fin_cadre_relief();
fin_page();
                        exit;
                }
?>

