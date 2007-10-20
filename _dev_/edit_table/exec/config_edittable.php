<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/presentation");
include_spip("inc/barre");
include_spip("inc/tableau");

function exec_config_edittable(){
	echo debut_page(_T('edittable:spip_edittable'));
		echo debut_gauche();
			echo debut_boite_info();
				echo _T('edittable:configuration_du_plugin_edittable');
			echo fin_boite_info();
		echo debut_droite();
			//var_dump($row_structure);
			//debut_cadre_formulaire();
			debut_cadre_trait_couleur("../"._DIR_PLUGIN_EDITTABLE."/img_pack/edittable.png", false, '', _T('edittable:configurer_edittable'));
				debut_cadre_formulaire();
				echo '<form action="?action=edittable_save_config" method="post">';
				echo '<input type="checkbox" name="editer_table_spip" /><b>'._T('edittable:voir_table_spip').'</b><hr />';
				echo '<b>'._T('edittable:cacher_table_avec_prefix').'</b><br />';
				echo _T('edittable:liste_des_prefix_a_cacher').'&nbsp;<input type="text" name="prefix_a_cacher" /><hr />';
				echo '<input type="submit" value="'._T('edittable:enregistrer').'" class="fondo"/>';
				echo '</form>';
				fin_cadre_formulaire();
			fin_cadre_trait_couleur();
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
