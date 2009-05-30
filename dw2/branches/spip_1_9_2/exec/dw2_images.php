<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Ajout Images
| Listing des Images du site.
| Forcer compteur sur fichiers img (jpg, gif, png)
+--------------------------------------------+
*/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_images() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

// page prim en cours
$page_affiche=_request('exec');

//
// requis
//

// verif admin .. verif install .. superglobal
include_spip("inc/dw2_inc_admin");
include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");
include_spip("inc/dw2_inc_ajouts");



//
// affichage page
//

debut_page(_T('dw:titre_page_admin'), "suivi", "dw2_admin");
echo "<a name='haut_page'></a><br />";

gros_titre(_T('dw:titre_page_admin'));


debut_gauche();

	menu_administration_telech();
	menu_voir_fiche_telech();
	menu_config_sauve_telech();
	
	// module outils
	bloc_popup_outils();

	// module delocaliser
	bloc_ico_page(_T('dw:acc_dw2_dd'), generer_url_ecrire("dw2_deloc"), _DIR_IMG_DW2."deloc.gif");


creer_colonne_droite();

	// vers popup aide 
	bloc_ico_aide_ligne();

	// info
	echo "<br />";
	debut_boite_info();
		echo "<img src='"._DIR_IMG_DW2."vignette-16.png' align='absmiddle'>
			<span class='arial2'> "._T('dw:txt_info_vignette')."</span>";
	fin_boite_info();

	// signature
	echo "<br />";
	debut_boite_info();
		echo _T('dw:signature', array('version' => _DW2_VERS_LOC));
	fin_boite_info();
	echo "<br />";

debut_droite();
	
	//
	//
	// onglets page ajouts global/catalogue images		
	echo debut_onglet().
		onglet(_T('dw:ajout_manuel'), generer_url_ecrire("dw2_ajouts"), 'page_gen', '', _DIR_IMG_DW2."ajout_doc.gif").
		onglet(_T('dw:ajout_images_1_1'), generer_url_ecrire("dw2_images"), 'page_img', 'page_img', _DIR_IMG_DW2."cata_img.gif").
		onglet(_T('dw:ajout_par_article'), generer_url_ecrire("dw2_ajouts_det"), 'page_det', '', _DIR_IMG_DW2."catalogue.gif").
	fin_onglet();
	
	
	// catalogue d'images (enreg. img un/un)
	# simple include pour ne pas passer par fonction spip en sup. (na !))
	include(_DIR_PLUGIN_DW2."/inc/dw2_inc_images.php");
	#

//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_

?>
