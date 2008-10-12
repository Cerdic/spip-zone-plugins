<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Fonctions communes ... orientées présentation
+--------------------------------------------+
*/

// pointer la page courante dans bloc d'admin
### note sur pointe_page() : le premier elem. du array doit être la page à atteindre
function pointe_page($case_page, $fonction) {
	$pg_exec = _request('exec');
	// --> if() : cas de voir la fiche
	if($fonction) {
		echo "<br /><img style='border:0;' src='"._DIR_IMG_PACK."fleche-right.png' alt='' />&nbsp;\n".
			"<a href='".generer_url_ecrire($case_page[0])."'>".$fonction."</a>\n";
	}

	if (in_array($pg_exec,$case_page)) {
		echo "&nbsp;<img style='border:0;' src='"._DIR_IMG_PACK."fleche-left.png' alt='' />\n";
	}
	echo "<br />\n";
}



// affiche indicateur d'origine 'serveur distant'
function origine_heberge($heberge)
	{
	if($heberge == "distant")
		{ return "<img src='"._DIR_IMG_PACK."attachment.gif' align='absmiddle' alt='' />\n"; }
	else if ($heberge == "local")
		{ return ""; }
	else
		{ return "<img src='"._DIR_IMG_DW2."dot_serveur.gif' align='absmiddle' alt='' />\n"; }
	}


//
// divers affichage (div) (tr) ...
//

//def. <tr>, input form serv_edit
function tr_tab_nouv($ttr, $type_in, $namevar, $valvar, $txt_ex, $readonly, $coul=true)
	{
	global $couleur_claire;
	if ($coul) { $cc = $couleur_claire; } else { $cc = '#FFFFFF'; }
	echo "<tr bgcolor='".$cc."'>\n".
	"<td width='120' height='30' valign='middle'><span class='verdana3'>".$ttr." : </span></td>\n".
	"<td width='10' align='center'><input type='".$type_in."' name='".$namevar."' value='".$valvar."' size='25' ".$readonly."></td>\n".
	"<td><span class='verdana2'>".$txt_ex."</span></td>\n".
	"</tr>\n";
	}

// --
function debut_band_titre($coul, $police="", $arg="")
	{
	global $couleur_foncee;
	$color = ($coul == $couleur_foncee) ? "white" : "#000000";
	if(!$police) { $police="verdana2"; }
	echo "<div class='bande_titre ".$police." ".$arg."' style='background-color:".$coul."; color:".$color."'>\n";
	}

// --
function debut_boite_filet($border, $align="")
	{
	echo "<div class='boite_filet_".$border." ".$align."'>\n";
	}


// --
function bouton_alpha($contenu)
	{
	$bouton = "<div class='bouton_alpha'>\n".$contenu."</div>\n";
	return $bouton;
	}

// --
function bouton_tout_catalogue($page_affiche, $args='')
	{
	$lien = generer_url_ecrire($page_affiche,$args);
	$contenu = "\n<a href='".$lien."' title='"._T('dw:tout_le_catalogue')."'><img src='"._DIR_IMG_PACK."plus.gif' border='0' align='absmiddle'></a>\n";
	echo bouton_alpha($contenu);
	}
	
// --
function bloc_bout_act($sens)
	{
	echo "<div class='icone36' style='float:".$sens."; margin:2px'>\n";
	}

// --
function conten_bloc_bout($sens='right',$width='40')
	{
	echo "<div style='float:".$sens."; width:".$width."px; padding:1px;'>\n";
	}

//
function debut_bloc_lien_page()
	{
	echo "<div style='margin-top:2px;' class='bouton36blanc' 
		onMouseOver=\"changeclass(this,'bouton36gris')\"
		onMouseOut=\"changeclass(this,'bouton36blanc')\">\n";
	}
	
// bloc lien page colonne gauche
function bloc_ico_page($texte, $lien, $icone)
	{
	debut_bloc_lien_page();
	echo "<a href='".$lien."' style='text-decoration:none;'>\n";
	echo "<img src='".$icone."' style='border:0;vertical-align:middle' alt='' />\n<span class='verdana2'><b> ".$texte."</b></span>\n";
	echo "</a>\n";
	fin_bloc();
	}

//
function fin_bloc()
	{
	echo "</div>\n";
	}

//
function style_form_area()
	{
	global $couleur_claire;
	$style_area = "style='margin:6px; padding:2px; border:1px solid $couleur_claire; ".
					"-moz-border-radius-topleft:9px; -moz-border-radius-bottomright:9px;'";
	return $style_area;
	}

//
// 
function message_echec_connexion($message_conex) {
	debut_boite_filet('a','center');
	echo "<img src='"._DIR_IMG_DW2."puce-rouge-breve.gif' align='absmiddle' alt='' />&nbsp;\n";
	echo "<span class='verdana2'>"._T('dw:mess_err_'.$message_conex)."</span>\n";
	fin_bloc();
}



//
// POPUP
//
function href_popup_std($chemin, $target)
	{
	$href = "\"$chemin\" ". 
		#target=\"$target\" 
		"onclick=\"javascript:window.open(this.href, '$target', 
		'width=540,height=550,menubar=no,scrollbars=yes,resizable=yes'); 
		$target.focus(); return false; \"";
	return $href;
	} 

	
//	
// POPUP stat graph Doc
function popup_stats_graph($iddoc,$nom_doc,$return=false)
	{
	$chemin = generer_url_ecrire("dw2_popup_stats", "id_document=".$iddoc);
	$target = "graph_document";
	$lien="<a href=".href_popup_std($chemin,$target). 
		"title=\"Clic &rarr; Popup stats\">$nom_doc</a>\n";
	if($return) { return $lien; }
	else { echo $lien; }
	}

//
// POPUP Outils
function bloc_popup_outils() {
	$chemin = generer_url_ecrire("dw2_outils");
	$target = "dw2_outils";
	debut_bloc_lien_page();
	echo "<a href=".href_popup_std($chemin,$target). 
		"title=\""._T('dw:titre_page_outils')."\" style=\"text-decoration: none;\" >\n
		<img src='"._DIR_IMG_PACK."administration-24.gif' style='vertical-align:middle;border:0' alt='' />\n
		<span class='verdana2'><b> "._T('dw:outils')."</b></span>\n
		</a>\n";
	fin_bloc();
	
}
//
//


// lien page koak : Aide en ligne
function bloc_ico_aide_ligne()
	{
	debut_bloc_lien_page();
	echo "<a href='http://www.koakidi.com/rub_aide_dw2.php' title='"._T('dw:title_aide_01')."' rel='external'>\n";
	echo "<img src='"._DIR_IMG_PACK."racine-24.gif' style='vertical-align:middle;border:0' alt='' />&nbsp;\n";
	echo "<span class='verdana2'>"._T('dw:aide')."</span>\n";
	echo "</a>\n";
	fin_bloc();
	}



// bouton fonction  - Fiche Catalogue - liste serveur
function bloc_minibout_act($title, $lien, $icone, $extension, $id_image) {
	if (!empty($icone)) {
		// affiche une icone (img_pack)
		echo "<div style='float:right; margin:0px 0px 1px 2px; text-align:center;' class='icone36' title='".$title."'>\n";
		echo "<a href='".$lien."'>\n";
		echo "<img src='".$icone."' style='vertical-align:middle;border:0' alt='' />\n";
		echo "</a></div>\n";
	}
	else {
		// affiche une vignette Doc ou vignette par defaut
		include_spip("inc/documents");
		$result=sql_select("fichier","spip_documents","id_vignette=$id_image");
		$row=sql_fetch($result);
		$fichier=$row['fichier'];
		
		$document=array('extension'=>$extension, 'id_vignette'=>$id_image, 'fichier'=>$fichier);

		$aff_vignette = document_et_vignette($document, $lien, true);
		echo "<div style='float:right; margin:0px 0px 1px 2px; text-align:center;' class='icone36' title='".$title."'>\n";
		echo $aff_vignette;
		echo "</div>\n";
	}
}



// Formulaire de modif d'un Titre et Desc' de Doc
function form_titre_desc($id, $titre_doc, $desc_doc, $url_redirect)
	{
	global $connect_id_auteur;
			
	echo "<form action='".generer_url_action("dw2actions", "arg=majtitredocument-".$id)."' method='post'>\n";
	echo "<table width='100%' celpadding='0' celspading='0'>\n";
	echo "<tr><td>\n";
	echo "<div align='left' class='verdana1'>"._T('dw:titre')."\n";
	echo "<textarea name='titre_document' rows='2' cols='18' wrap='soft' ".style_form_area().">".entites_html($titre_doc)."</textarea>\n";
	echo "</div></td><td>\n";
	echo "<div align='right' class='verdana1'>"._T('dw:descriptif')."\n";
	echo "<textarea name='descriptif_document' rows='2' cols='23' wrap='soft' ".style_form_area().">".entites_html($desc_doc)."</textarea>\n";
	echo "</div></td><td>\n";
	echo "<input type='image' src='"._DIR_IMG_DW2."ok_fich.gif' title='"._T('bouton_valider')."' />\n";
	echo "</td></tr></table>\n";
	echo "<input type='hidden' name='redirect' value='".$url_redirect."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-majtitredocument-".$id)."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
	echo "</form>\n";
	}


// fabrication d'un tableau à 2 colonnes, avec icone par item,
// traite un array a 2 entrées : item (fichier ...) lien(url)->validé si $info_supp vaut 'href'
function double_colonne($array, $chaine_titre, $icone_item, $info_supp)
{
global $couleur_claire;
	if ($icone_item)
		{ $ico_itm = "<img src='"._DIR_IMG_DW2.$icone_item."' style='vertical-align:middle;border:0' alt='' />\n"; }
	else
		{ $ico_itm = "<img src='"._DIR_IMG_PACK."rien.gif' style='border:0' alt='' />\n"; }

	// Prepa affichage tableau : listing fichiers (/ 2 coll.)
	ksort($array);
	$nb_k = count($array);
	$ad = '0';
	$af = ceil($nb_k/2); 
	$a_list = array_slice($array, $ad, $af);
	$b_list = array_slice($array, $af);

	
	
	echo "<span class='verdana3'><b>\n";
	if($nb_k<=1)
		{ echo _T('dw:'.$chaine_titre, array('nb_k' => $nb_k)); }
	else
		{ echo _T('dw:'.$chaine_titre.'_s', array('nb_k' => $nb_k)); }
	echo "</b></span></div>\n";
	
	echo "<table width='100%' border='0' cellpadding='2' cellspacing='0' align='center'><tr><td width='50%' valign='top'>\n";
	echo "<table width='100%' border='0' cellpadding='1' cellspacing='0' align='center'>\n";
	// lignes tableau col. a
	$ifond = 0;
	while (list($item,$info_item) = each($a_list))
		{
		// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 20 caract
		$item = wordwrap($item,20,' ',1);

		// prépare le href si info_supp vaut href
		if($info_supp=='href') { $li_itm ="<a href='".$info_item."'>"; $fin_li="</a>\n";}
		else { $li_itm =""; $fin_li="";}
		//
		// prépare le td si info_supp vaut info
		if($info_supp=='info')
			{ $txt_itm ="<td><div align='right'><span class='verdana2'>".$info_item."</span></div></td>\n";}
		else { $txt_itm ="";}
		//
		$ifond = $ifond ^ 1;
		$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
		echo "<tr bgcolor='$couleur'>\n";
		echo "<td height='20'>".$li_itm.$ico_itm."<span class='verdana2'> ".$item."".$fin_li."</span></td>\n";
		echo $txt_itm;
		echo "</tr>\n";
		}
	echo "</table></td><td width='50%' valign='top'>\n";
	echo "<table width='100%' border='0' cellpadding='1' cellspacing='0' align='center'>\n";
	// lignes tableau col. b
	$ifond = 0;
	while (list($item,$info_item) = each($b_list))
		{
		// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 20 caract
		$item = wordwrap($item,20,' ',1);
		
		if($info_supp=='href') { $li_itm ="<a href='".$info_item."'>"; $fin_li="</a>\n";}
		else { $li_itm =""; $fin_li="";}
		if($info_supp=='info')
			{ $txt_itm ="<td><span class='verdana2'><div align='right'>".$info_item."</div></span></td>\n";}
		else { $txt_itm ="";}
		$ifond = $ifond ^ 1;
		$couleur = ($ifond) ? $couleur_claire : '#FFFFFF';
		echo "<tr bgcolor='$couleur'>\n";
		echo "<td height='20'>".$li_itm.$ico_itm."<span class='verdana2'> ".$item."</span>".$fin_li."</td>\n";
		echo $txt_itm;
		echo "</tr>\n";
		}
	echo "</table></td></tr></table>\n";
}


// formulaire choix dates periode
function formulaire_periode($periode1,$periode2,$annee_select,$retour) {
	
	echo "<form action ='".generer_url_ecrire($retour)."' method='post'>\n";
	
	echo "<div style='padding:3px;' align='right'>\n";
	echo _T('dw:periode_date_debut')."&nbsp;\n";
	echo afficher_jour($periode1['jour'], "name='prdd[0]' size='1' class='fondl' ", true) .
		 afficher_mois($periode1['mois'], "name='prdd[1]' size='1' class='fondl' ", true) .
		 afficher_annee($periode1['annee'], "name='prdd[2]' size='1' class='fondl' ",$annee_select);
	echo "\n</div>";

	echo "<div style='padding:3px;' align='right'>\n";
	echo _T('dw:periode_date_fin')."&nbsp;\n";
	echo afficher_jour($periode2['jour'], "name='prdf[0]' size='1' class='fondl' ", true) .
		 afficher_mois($periode2['mois'], "name='prdf[1]' size='1' class='fondl' ", true) .
		 afficher_annee($periode2['annee'], "name='prdf[2]' size='1' class='fondl' ",$annee_select);
	echo "\n</div>";

	echo "<div align='right'>
		<input type='submit' class='fondo' value='". _T('dw:soumettre')."' />\n
		</div>";
	
	echo "</form>\n";

}


//
// MENUS colonne gauche et droite
//

### note sur pointe_page() : le premier elem. du array doit être la page à atteindre

// menu fonction principales telech
function menu_administration_telech() {
	echo debut_cadre_enfonce(_DIR_IMG_DW2."telech.gif",true);
		echo "<div class='verdana2' style='padding:4px;'><div style='font-weight:bold;'>"._T('dw:administration')."<br />\n";
			pointe_page(array("dw2_admin"), _T('dw:accueil'));
			pointe_page(array("dw2_catalogue"), _T('dw:catalogue'));
			pointe_page(array("dw2_stats","dw2_stats_prd"), _T('dw:statistiques'));
			if($GLOBALS['dw2_param']['mode_restreint']=='oui') {
				pointe_page(array("dw2_stats_res","dw2_stats_resdoc"), _T('dw:statistiques_auteurs'));
			}
			pointe_page(array("dw2_categories"), _T('dw:categories'));
			pointe_page(array("dw2_ajouts","dw2_ajouts_det","dw2_images"), _T('dw:ajout_doc'));
			pointe_page(array("dw2_archives"), _T('dw:archives'));
		echo "</div></div>\n";
	echo fin_cadre_enfonce(true);
}

// atteindre fiche du doc 'n' ..
function menu_voir_fiche_telech() {
	echo debut_cadre_enfonce(_DIR_IMG_DW2."fiche_doc.gif",true);
		echo "<div class='verdana2'>\n";
		echo "<form action='".generer_url_ecrire("dw2_modif")."' method='post'>\n";
		echo "<p style='margin:2px;'><b>"._T('dw:voir_fiche')."</b> : \n";
		echo "<input type='text' name='id' size='2' maxlength='10' onClick=\"setvisibility('voir_fiche','visible');\" class='fondl' />&nbsp;&nbsp;\n";
		echo "<span class='visible_au_chargement' id='voir_fiche'>\n";
		echo "<input type='image' src='"._DIR_IMG_DW2."ok_fich.gif' style='vertical-align:top;' title='"._T('bouton_valider')."' />\n";
		echo "</span>\n";
		pointe_page(array("dw2_modif"), "");
		echo "</p></form></div>\n";
	echo fin_cadre_enfonce(true);
}

// configuration & sauvegarde 
function menu_config_sauve_telech() {
	echo debut_cadre_enfonce(_DIR_IMG_DW2."configure.gif",true);
		echo "<div class='verdana2' style='padding:4px;'><b>\n";
			pointe_page(array("dw2_config"), _T('dw:inst_conf'));
			pointe_page(array("dw2_save_tbl"), _T('dw:sauvegarde'));
			if($GLOBALS['dw2_param']['mode_restreint']=='oui') {
				pointe_page(array("dw2_restreint","dw2_restreint_etat"), _T('dw:restreint'));
			}
		echo "</b></div>";
	echo fin_cadre_enfonce(true);
}

// fonctions principales dw_deloc.php
function menu_administration_deloc() {
	echo debut_cadre_enfonce(_DIR_IMG_DW2."deloc.gif",true);
		echo "<span class='verdana2'>"._T('dw:doc_delocalises')."</span><br />\n".
		"<span class='verdana3'><b>"._T('dw:export')."/"._T('dw:import_virtuel')."</b></span><br />\n";
		
		echo "<div class='verdana2'><b>\n";
			pointe_page(array("dw2_deloc"), _T('dw:accueil'));
			pointe_page(array("dw2_serv_edit"), _T('dw:nouveau_serveur'));
		echo "</b></div><br />\n";
	echo fin_cadre_enfonce(true);
}

?>