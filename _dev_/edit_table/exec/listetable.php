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
				echo "Hello World !";
			echo fin_boite_info();
			echo debut_raccourcis();
				echo '<a href="?exec=listetable">'._T('edittable:les_table').'</a>';
			echo fin_raccourcis();
		echo debut_droite();
			$res_list_table = spip_query("SHOW TABLES;");
			debut_cadre_trait_couleur("../"._DIR_PLUGIN_edittable."/img_pack/digg.png", false, '', _T('edittable:mes_edittable_en'));
			echo '<table>';
			while ($row_table = MYSQL_fetch_row($res_list_table)){
				//var_dump($row_table);
				echo '<tr><td>'.$row_table[0].'</td><td><a href="?exec=edittable&amp;table='.$row_table[0].'">'.$row_table[0].'</a></td></tr>';
			}
			echo '</table>';
			fin_cadre_trait_couleur(false);
			
			
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
