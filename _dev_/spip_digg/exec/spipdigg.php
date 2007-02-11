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
				echo '<a href="?exec=editer_digg&id_digg=new">'._T('spipdigg:ajouter_un_digg').'</a><br />';
				echo '<a href="?exec=spipdigg">'._T('spipdigg:mes_diggs').'</a>';
			echo fin_raccourcis();
		echo debut_droite();
			//~ echo gros_titre(_T('spipdigg:spip_digg'));
			$sql_list_digg_prepa = "SELECT diggs.* FROM spip_diggs diggs, spip_diggs_auteurs diggs_auteurs WHERE diggs_auteurs.id_auteur = '".$GLOBALS['auteur_session']['id_auteur']."' AND diggs.id_digg = diggs_auteurs.id_digg AND diggs.statut = 'prepa';";
			$res_list_digg_prepa = spip_query($sql_list_digg_prepa);
			debut_cadre_trait_couleur("../"._DIR_PLUGIN_SPIPDIGG."/img_pack/digg.png", false, '', _T('spipdigg:mes_diggs_en_prepa'));
			echo '<table>';
			while ($row_diggs = spip_fetch_array($res_list_digg_prepa)){
				echo '<tr><td>'.$row_diggs['id_digg'].'</td><td><a href="?exec=diggs&amp;id_digg='.$row_diggs['id_digg'].'">'.$row_diggs['titre'].'</a></td></tr>'; //-<img src="../dist/images/poubelle.gif" alt="'._T('spipdigg:supprimer_digg').'" /<br />';
			}
			echo '</table>';
			fin_cadre_trait_couleur(false);
			
			$sql_list_digg_prop = "SELECT diggs.* FROM spip_diggs diggs, spip_diggs_auteurs diggs_auteurs WHERE diggs_auteurs.id_auteur = '".$GLOBALS['auteur_session']['id_auteur']."' AND diggs.id_digg = diggs_auteurs.id_digg AND diggs.statut == 'prop';";
			$res_list_digg_prop = spip_query($sql_list_digg);
			if (spip_num_rows($res_list_digg_prop) > 0){
				debut_cadre_trait_couleur("../"._DIR_PLUGIN_SPIPDIGG."/img_pack/digg.png", false, '', _T('spipdigg:mes_diggs_prop'));
				echo '<table>';
				while ($row_diggs = spip_fetch_array($res_list_digg_prop)){
					echo '<tr><td>'.$row_diggs['id_digg'].'</td><td><a href="?exec=diggs&amp;id_digg='.$row_diggs['id_digg'].'">'.$row_diggs['titre'].'</a></td></tr>'; //-<img src="../dist/images/poubelle.gif" alt="'._T('spipdigg:supprimer_digg').'" /<br />';
				}
				echo '</table>';
				fin_cadre_trait_couleur(false);
			}
			
			
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
