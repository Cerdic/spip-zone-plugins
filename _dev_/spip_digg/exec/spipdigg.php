<?php
//
// exec/spipdigg.php
//
if (!defined("_ECRIRE_INC_VERSION")) return;

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SPIPDIGG',(_DIR_PLUGINS.end($p)));

include_spip("inc/presentation");
include_spip("inc/barre");

function exec_spipdigg(){
	echo debut_page(_T('spipdigg:spip_diggs'));
		echo debut_gauche();
			echo debut_boite_info();
				echo "Hello World !";
			echo fin_boite_info();
			echo debut_raccourcis();
				echo '<a href="?exec=editer_digg&new=oui">'._T('spipdigg:ajouter_des_digg').'</a>';
			echo fin_raccourcis();
		echo debut_droite();
			//~ echo gros_titre(_T('spipdigg:spip_digg'));
			$sql_list_digg = "SELECT diggs.* FROM spip_diggs diggs, spip_diggs_auteurs diggs_auteurs WHERE diggs_auteurs.id_auteur = '".$GLOBALS['auteur_session']['id_auteur']."' AND diggs.id_digg = diggs_auteurs.id_digg;";
			//echo $sql_list_digg;
			$res_list_digg = spip_query($sql_list_digg);

			debut_cadre_trait_couleur("../"._DIR_PLUGIN_SPIPDIGG."/img_pack/digg.png", false, '', _T('spipdigg:mes_diggs'));
			while ($row_diggs = spip_fetch_array($res_list_digg)){
				echo $row_diggs['id_digg'].'&nbsp;<a href="?exec=diggs&amp;id_digg='.$row_diggs['id_digg'].'">'.$row_diggs['titre'].'</a><br />'; //-<img src="../dist/images/poubelle.gif" alt="'._T('spipdigg:supprimer_digg').'" /<br />';
			}
			fin_cadre_trait_couleur(true);
			
			
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
