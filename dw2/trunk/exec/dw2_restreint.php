<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifi� KOAK2.0 strict, mais si !
+--------------------------------------------+
| Poser restriction telechargement sur rubrique
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_restreint() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee, 
		$spip_display, 
		$spip_lang_left, $spip_lang_right, $spip_lang;

// page prim en cours
$page_affiche=_request('exec');

//
// requis
//

// verif admin .. verif install .. superglobal
include_spip("inc/dw2_inc_admin");

include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");

include_spip("inc/dw2_inc_rubriquage");
include_spip("inc/dw2_inc_hierarchie");


// reconstruire .. var=val des get et post
// var : $id_rub,$id_art
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }

	$id_rub = intval($id_rub);
	$id_art = intval($id_art);
	

//
// affichage page
//

$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('dw:titre_page_admin'), "suivi", "dw2_admin");

echo "<a name='haut_page'></a><br />";

echo gros_titre(_T('dw:titre_page_admin'),'','',true);


echo debut_gauche('',true);

	menu_administration_telech();
	menu_voir_fiche_telech();
	menu_config_sauve_telech();
	
	// module outils
	bloc_popup_outils();

	// module delocaliser
	bloc_ico_page(_T('dw:acc_dw2_dd'), generer_url_ecrire("dw2_deloc"), _DIR_IMG_DW2."deloc.gif");


echo creer_colonne_droite('',true);

	// vers popup aide 
	echo "<br />\n";
	bloc_ico_aide_ligne();

	// signature
	echo "<br />\n";
	echo debut_boite_info(true);
		echo _T('dw:signature', array('version' => _DW2_VERS_LOC));
	echo fin_boite_info(true);
	echo "<br />\n";

echo debut_droite('',true);

	//
	// onglets 		
	echo debut_onglet().
		onglet(_T('dw:rest_page_hierarchie'), generer_url_ecrire("dw2_restreint"), 'page_res', 'page_res', "racine-site-24.gif").
		onglet(_T('dw:rest_page_table'), generer_url_ecrire("dw2_restreint_etat"), 'page_resetat', '', _DIR_IMG_DW2."catalogue.gif").
	fin_onglet();
	echo "<br />\n";

// petit commentaire si ...
// ## pas tres utile puisqu'on affiche pas le bouton du menu si pas "restreint"
if($GLOBALS['dw2_param']['mode_restreint']=='non') {
	debut_band_titre('#E8C8C8');
	echo _L('La restriction de t�l�chargement n\'est pas op�rationnelle !<br />
			(voir page "Configuration").<br />
			Toutes les modifications apport�es ici seront enregistr�es dans la table de restriction
			mais non appliqu�es !');
	fin_bloc();
}

//
// les rubriques a la SPIP
//

	afficher_entete_restreindre($id_rub,$id_art);
		

if(!$id_art) {

	afficher_enfants_rubrique($id_rub);
	
	afficher_articles_enfants($id_rub);
	
}
echo "<br />\n";


//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>\n";

	echo fin_gauche().fin_page();
} // fin exec_
?>