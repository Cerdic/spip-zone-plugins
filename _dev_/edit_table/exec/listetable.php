<?php
//
// exec/listetable.php
//
if (!defined("_ECRIRE_INC_VERSION")) return;

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_EDITTABLE',(_DIR_PLUGINS.end($p)));

include_spip("inc/presentation");
include_spip("inc/barre");

function exec_listetable(){
	echo debut_page(_T('edittable:spip_edittable'));
		echo debut_gauche();
			echo debut_boite_info();
				echo _T('edittable:les_tables_de_la_base');
			echo fin_boite_info();
			echo debut_raccourcis();
				echo '<a href="?exec=listetable">'._T('edittable:les_table').'</a>';
			echo fin_raccourcis();
		echo debut_droite();
			$res_list_table = spip_query("SHOW TABLES;");
			debut_cadre_trait_couleur("../"._DIR_PLUGIN_edittable."/img_pack/digg.png", false, '', _T('edittable:mes_edittable_en'));
			//echo '<table style="width:100%">';
			while ($row_table = MYSQL_fetch_row($res_list_table)){
				//var_dump($row_table);
				//echo '<tr><td style="border-bottom: 1px solid black; margin: 20px; widht: 100%;">'; echo debut_boite_info(); echo '<b>'.$row_table[0].'</b><br /><a href="?exec=edittable&amp;table='.$row_table[0].'">'._T('edittable:editer_la_table').'</a></td></tr>';
				$nbr_enreg = mysql_num_rows(mysql_query("SELECT * FROM ".$row_table[0].";"));
				echo debut_boite_info();
				echo '<b>'.$row_table[0].'</b><br />';
				echo _T('edittable:nombre_d_enregistrement').' : <b>'.$nbr_enreg.'</b><br />';
				echo '<a href="?exec=edittable&amp;table='.$row_table[0].'">'._T('edittable:editer_la_table').'</a>';
				echo fin_boite_info(); echo '<br />';
			}
			fin_cadre_trait_couleur(false);
			
			
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
