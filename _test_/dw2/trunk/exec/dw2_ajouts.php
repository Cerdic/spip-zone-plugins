<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Ajout document standard.
| En mode "manuel", affiche selecteur des Doc 
| enregistrables dans le catalogue.
| En mode "auto" simple avis !
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_ajouts() {

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

// config 
$mode_enregistre_doc = $GLOBALS['dw2_param']['mode_enregistre_doc'];
$type_categorie = $GLOBALS['dw2_param']['type_categorie'];
$criteres_auto_doc = $GLOBALS['dw2_param']['criteres_auto_doc'];

// reconstruire .. var=val des get et post
// var : ajout_un ; doc ; ajouttout ; tab_ajoutout
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }

//
// prepa
//

// enregistre et affiche tous les docs dispo
	if ($ajouttout=="oui") {
		$nbr_doc_enreg=count($tab_ajoutout);
		foreach($tab_ajoutout as $item)
			{ ajout_doc_catalogue($item); }
	}

// enregistre  affiche LE doc sélectionne
	if ($ajout_un=="oui") {
		$retour_nomfichier = ajout_doc_catalogue($doc,'','oui');
	}

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

	// signature
	echo "<br />";
	debut_boite_info();
		echo _T('dw:signature', array('version' => _DW2_VERS_LOC));
	fin_boite_info();
	echo "<br />";

debut_droite();

	//
	// onglets page ajouts global/catalogue images		
	echo debut_onglet().
		onglet(_T('dw:ajout_manuel'), generer_url_ecrire("dw2_ajouts"), 'page_gen', 'page_gen', _DIR_IMG_DW2."ajout_doc.gif").
		onglet(_T('dw:ajout_images_1_1'), generer_url_ecrire("dw2_images"), 'page_img', '', _DIR_IMG_DW2."cata_img.gif").
		onglet(_T('dw:ajout_par_article'), generer_url_ecrire("dw2_ajouts_det"), 'page_det', '', _DIR_IMG_DW2."catalogue.gif").
	fin_onglet();


debut_cadre_relief("rien.gif");

debut_band_titre($couleur_foncee, "verdana3", "bold");
	echo _T('dw:txt_ajout_titre_page');
fin_bloc();


	// enregistre et affiche tous les docs dispo
	if ($ajouttout=="oui") {

		echo "<div class='cadre-couleur cadre-padding'>\n";
		if($nbr_doc_enreg<=1)
			{ echo _T('dw:txt_ajout_01', array('nbr_doc_enreg' => $nbr_doc_enreg)); }
		else
			{ echo _T('dw:txt_ajout_01_s', array('nbr_doc_enreg' => $nbr_doc_enreg)); }
		
		echo "</div>\n\n";
	}


	// enregistre  affiche LE doc sélectionne
	if ($ajout_un=="oui") {
		// affichage du fichier qui vient d'être enregistrée ...
		echo "<span class='verdana3'><b>"._T('dw:enreg_lien_dans_cat')."</b></span><br />\n";
		echo "<div class='iconeoff verdana2'>";
		echo _T('dw:fichier')." : <b>$retour_nomfichier</b><br />\n";
		echo "</div><br />\n";	
		// bouton  vers : "modif" 
		echo "<div class='cadre-couleur cadre-padding center verdana2'><br />\n";
		echo "<form action='".generer_url_ecrire("dw2_modif", "id=".$doc)."' method='post'>\n";
		echo _T('dw:txt_modif_01')."<br /><br />\n";
		echo "<input type='submit' value='"._T('dw:modifier')."' class='fondo'>\n</form>\n</div>\n";
	}

		
	echo "<br /><div align='center'>\n\n";
	
	// si en mode inclus manuel :
	// on regarde les documents dans spip_documents
	// qui ne sont pas des type 1, 2 ou 3 (jpg,png,gif) et pas encore dans spip_dw2_doc
	if($mode_enregistre_doc=='manuel') {
		$query="SELECT sd.id_document, sd.fichier ".
				"FROM spip_documents sd LEFT JOIN spip_dw2_doc dw ON sd.id_document = dw.id_document ".
				"WHERE sd.mode = 'document' AND sd.id_type > '3' AND dw.id_document IS NULL ORDER BY titre";
		$result=spip_query($query);
		$nbres=spip_num_rows($result);
	}
	
	if ($nbres==0) {
		echo _T('dw:txt_ajout_rien')."<br /><br />\n";
		debut_band_titre($couleur_claire);
		echo _T('dw:txt_install_17')."<b>"._T('dw:cfg_mode_enregistre_doc_val_'.$mode_enregistre_doc)."</b>\n";
		fin_bloc();
	}
	else {
		// formulaire ajouter un Document
		// selecteur, bouton ajout_un, bouton ajouttout
		$prep_ajoutout=array(); 
		while ($row=spip_fetch_array($result)) {
			$iddoc=$row['id_document'];
			$nomfichier = substr(strrchr($row['fichier'],'/'), 1);
			
			//
			$origine=origine_doc($iddoc);
			// si en statut 'publie' OK .. on enregistre
			if($origine[2]=='1') {
				$prep_ajoutout[$iddoc]['origine']=$origine;
				$prep_ajoutout[$iddoc]['fichier']=$nomfichier;
			}
		}
		
		$nb_prep_ajoutout=count($prep_ajoutout);
		
		if($nb_prep_ajoutout>0) {
			// selecteur ajout 1/1
			echo "<form action='".generer_url_ecrire("dw2_ajouts")."' method='post'>\n";
			echo "<div class='cadre-couleur cadre-padding'><b>"._T('dw:select_doc')."</b></div><br />\n";
			echo "<select name='doc' size='10' class='fondl'>";
			
			foreach($prep_ajoutout as $k => $v) {
				echo "<option value='$k'>[$k] ".$v['fichier']." - ".$v['origine'][0]." ".$v['origine'][1]."</option>\n";
			}	
			echo "</select>\n";
			echo "<input type='hidden' name='ajout_un' value='oui'>\n";
			echo "<div align='center'><br /><input type=submit value="._T('dw:ajouter')." class='fondo'></div><br />\n";
			
			// info
			echo "<div align='center' style='background:#EFEFEF; padding:2px;' class='verdana1'>\n";
			echo _T('dw:txt_ajout_03', array("type_categorie" =>_T('dw:cfg_type_categorie_val_'.$type_categorie)));
			echo "<br /></div><br />\n";
			echo "</form>\n";
			
			// bouton tout ajouter
			echo "<div class='cadre-couleur cadre-padding'><b>"._T('dw:txt_ajout_05')."</b></div><br />\n";
			echo "<form action='".generer_url_ecrire("dw2_ajouts")."' method='post'>\n";
			echo "<input type='hidden' name='ajouttout' value='oui'>\n";
			foreach($prep_ajoutout as $k => $val) {
				echo "<input type='hidden' name='tab_ajoutout[]' value='".$k."'>\n";
			}
			echo "<input type='submit' value='"._T('dw:ajout_tout')."' class='fondo'>\n";
			echo "</form>\n";
		}
		else {
			echo _T('dw:txt_ajout_rien')."<br /><br />";
			debut_band_titre($couleur_claire);
				echo _T('dw:txt_install_17')."<b>"._T('dw:cfg_mode_enregistre_doc_val_'.$mode_enregistre_doc)."</b>";
			fin_bloc();
			if($criteres_auto_doc) {
				$tb_crit_doc=join(',',$criteres_auto_doc);
				debut_band_titre($couleur_claire);
				echo _T('txt_install_30')."<b>";
				foreach($tb_crit_doc as $crit) {
					echo _T('cfg_criteres_auto_doc_val_'.$crit)."&nbsp;";
				}
				echo "</b>";
				fin_bloc();
				
				
			}
		}
	}
	echo "</div><br />";
fin_cadre_relief();


//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_
?>
