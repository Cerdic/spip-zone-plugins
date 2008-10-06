<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Listing des Images du site.
| Accès hors DW2, pour tous redac, admin ...
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_cata_img() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$couleur_claire, $couleur_foncee;


// desactiver les chekboxs
	$hors_dw = true;
//

debut_page(_T('dw:cat_images_de', array('nom_site_spip' => $nom_site_spip)), "documents", "images");
echo "<a name='haut_page'></a>";
	
// function requises ...
include_spip("inc/dw2_inc_admin");
include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");


	debut_grand_cadre();

		# simple include pour ne pas passer par fonction spip en sup. (na !))
		include(_DIR_PLUGIN_DW2."/inc/dw2_inc_images.php");
		#

	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";
	fin_grand_cadre();
	
	debut_gauche();
		debut_boite_info();
		echo "<img src='"._DIR_IMG_DW2."vignette-16.png' align='absmiddle'>
				<span class='arial2'> "._T('dw:txt_info_vignette')."</span>";
		fin_boite_info();
	
	
	creer_colonne_droite();
		// signature
		debut_boite_info();
			echo _T('dw:signature', array('version' => _DW2_VERS_LOC));
		fin_boite_info();


	// juste pour repousser à droite : creer_colonne_droite ..
	debut_droite();


	fin_page();

}
?>
