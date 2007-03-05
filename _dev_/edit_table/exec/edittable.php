<?php
//
// exec/edittable.php
//
if (!defined("_ECRIRE_INC_VERSION")) return;

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_EDITTABLE',(_DIR_PLUGINS.end($p)));

include_spip("inc/presentation");
include_spip("inc/barre");
include_spip("inc/tableau");

function exec_edittable(){
	echo debut_page(_T('edittable:spip_edittable'));
		echo debut_gauche();
			echo debut_boite_info();
				echo _request('table');
			echo fin_boite_info();
			echo debut_raccourcis();
				echo '<a href="?exec=listetable">'._T('edittable:les_table').'</a>';
			echo fin_raccourcis();
		echo debut_droite();
			//var_dump($row_structure);
			
			$olonne_cle = "000";
			
			$res_structure_table = spip_query(" DESC "._request('table').";");
			//debut_cadre_formulaire();
			while ($row_structure = spip_fetch_array($res_structure_table)){
				//afficher_tableau($row_structure);
				if ($row_structure['Key'] == "PRI" OR $row_structure['Extra'] == "auto_increment"){
					$olonne_cle = $row_structure['Field'];
				}
				$colone_temp == $row_structure['Field'];
				$structure = $row_structure;
			}
			if ( $olonne_cle == '000' ) { $olonne_cle = $colone_temp; }
			
			$res_list_edittable = spip_query("SELECT * FROM "._request('table').";");
			debut_cadre_trait_couleur("../"._DIR_PLUGIN_edittable."/img_pack/digg.png", false, '', _T('edittable:mes_edittable_en'));
			//echo '<table>';
			while ($row_edittable = spip_fetch_array($res_list_edittable)){
				echo _T('edittable:enregistrement_numero').'&nbsp;<b>'.$row_edittable[$olonne_cle].'</b>';
				echo '&nbsp;'._T('edittable:cle_primaire').'&nbsp;:'.$olonne_cle.'&nbsp;=&nbsp;'.$row_edittable[$olonne_cle];
				echo '<br /><a href="?exec=edittable_voir&amp;valeur_cle='.$row_edittable[$olonne_cle].'&amp;table='._request('table').'&amp;colonne_cle='.$olonne_cle.'">'._T('edittable:editer_enregistrement').'</a><hr />';
			}
			//echo '</table>';
			fin_cadre_trait_couleur(false);
			
			echo '<br />';
			
			$res_structure_table = spip_query(" DESC "._request('table').";");
			debut_cadre_formulaire();
			while ($row_structure = spip_fetch_array($res_structure_table)){
				afficher_tableau($row_structure);
				//if $row_structure
			}
			fin_cadre_formulaire();
			
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
