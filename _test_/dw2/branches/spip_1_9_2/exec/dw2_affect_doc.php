<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Affecter un doc en "import" a un article/Rubrique
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_affect_doc() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

// page prim en cours
#$page_affiche=_request('exec');

//
// requis dw
//

// verif admin .. verif install .. superglobal
include_spip("inc/dw2_inc_admin");
include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");
include_spip("inc/dw2_inc_deloc");

// config
$type_categorie = $GLOBALS['dw2_param']['type_categorie'];

// reconstruire .. var=val des get et post
// var : $id_document, $id_serv
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }

$id_document=intval($id_document);
$id_serv=intval($id_serv);


//
// prepa
//

//
// affichage
//

debut_page(_T('dw:titre_page_deloc'), "suivi", "dw2_deloc");
	echo "<a name='haut_page'></a><br />";
gros_titre(_T('dw:titre_page_deloc'));


debut_gauche();
	// fonctions principales dw_deloc.php
	menu_administration_deloc();
	
	// module outils
	bloc_popup_outils();
	
	// retour dw2 admin
	bloc_ico_page(_T('dw:acc_dw2_st'), generer_url_ecrire("dw2_admin"), _DIR_IMG_DW2."telech.gif");
	echo "<br />";
	
	// Def. module doc deloc
	echo "<br />";
	debut_boite_info();
		echo "<span class='verdana2'>"._T('dw:txt_dd_intro_gauche')."</span><br />";
	fin_boite_info();
	
creer_colonne_droite();

	// vers popup aide 
	bloc_ico_aide_ligne();

	// signature
	echo "<br />";
	debut_boite_info();
		echo _T('dw:signature', array('version' => _DW2_VERS_LOC));
	fin_boite_info();
	echo "<br />";

debut_droite();

// iframe de collecte articles
echo "\n<iframe id='action_dw2' name='action_dw2' src='".generer_url_ecrire("dw2_cherchart")."' width='1' height='1' style='position: absolute; visibility:hidden;'></iframe>\n";


debut_cadre_relief(_DIR_IMG_DW2."import-24.gif");

	//titre
	debut_band_titre($couleur_foncee);
		echo "<div align='center'>";
		echo "<span class='verdana3'><b>"._T('dw:destination_doc')."</b></span></div>\n";
	fin_bloc();
	// nom fichier
	$query="SELECT url FROM spip_dw2_doc WHERE id_document=$id_document";
	$result=spip_query($query);
	$row=spip_fetch_array($result);
	$fichier = substr(strrchr($row['url'],'/'), 1);
	echo "<br /><span class='verdana3'>"._T('dw:fichier')." ".$fichier."</span><br /><br />\n";
	
	
	//
	// formulaire
	//
	
	echo "<form name='designedoc' action='".generer_url_action("dw2actions", "arg=docserveurlier-".$id_document)."' method='post'>";

	// #TITRE et #DESCRIPTIF du Doc
	debut_cadre_enfonce(_DIR_IMG_PACK."doc-24.gif", false, "", _T('dw:doc_lie_trt_descrip'));
	echo "<div align='center'>";
	echo "<span class='verdana2'><b>"._T('dw:titre')."</b></span><br />\n";
	echo "<textarea name='trt_doc' rows='1' cols='40' wrap='soft' class='fondl'></textarea><br />\n";
	echo "<span class='verdana2'><b>"._T('dw:descriptif')."</b></span><br />\n";
	echo "<textarea name='descrip_doc' rows='2' cols='40' wrap='soft' class='fondl'></textarea>\n";
	echo "</div>";
	fin_cadre_enfonce();
	
	echo "<br />";

	// choix rubrique
	debut_cadre_relief(_DIR_IMG_PACK."rubrique-24.gif");
	echo "<b>"._T('dw:dans_rub')."</b><br />\n";
	echo "<br /><SELECT name='id_rub' style='background-color: $couleur_claire; font-size: 90%; width:100%; font-face:verdana,arial,helvetica,sans-serif;' size=1>\n";
	rub_parent("0");
	echo "</SELECT>";
	echo "<br /><br />"._T('dw:recherche_art_de_rub')." \n";
	
	echo "<a href='javascript:rechercheart();'><img src='"._DIR_IMG_DW2."ok_fich.gif' border='0' align='top' /></a>\n";

	echo "<br /><br />\n";
	echo "<select name='proposition'>\n";
	echo "<option value=''>Articles de la rubrique -----></option>";
	echo "</select>\n";
	echo "<br />";
	
	fin_cadre_relief();
	echo "<input type='hidden' name='type_categorie' value='$type_categorie' />\n";
	echo "<input type='hidden' name='fichier' value='$fichier' />\n";
	echo "<input type ='hidden' name='id_serv' value='$id_serv' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-docserveurlier-".$id_document)."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";			
	echo "<div align='right'><input type=submit value='"._T('dw:suite')."' class='fondo'></div>\n";
	echo "</form>\n";
	
fin_cadre_relief();
	
	// bouton annuler la Destination... 
	echo "<br />";
	bouton_annule_dest($id_document,$id_serv);


//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_
?>
