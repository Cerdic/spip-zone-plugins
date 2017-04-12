<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifi� KOAK2.0 strict, mais si !
+--------------------------------------------+
| .. generer/afficher arbo rubriques, 
| .. bloc info restriction
| (+ source : spip 1.9.1)
+-------------------------------------------+
*/


// modif de (inc/rubrique) http://code.spip.net/@sous_enfant_rub
function sous_enfants_rubrique($collection2){
	global $lang_dir, $spip_lang_dir, $spip_lang_left;

	$result3 = sql_select("*","spip_rubriques","id_parent='$collection2'","","0+titre,titre");

	if (!sql_count($result3)) return '';
	$retour = debut_block_depliable(false,"enfants$collection2")."\n<ul style='margin: 0px; padding: 0px; padding-top: 3px;'>\n"; // block invisible
	while($row=sql_fetch($result3)){
			$id_rubrique2=$row['id_rubrique'];
			$id_parent2=$row['id_parent'];
			$titre2=$row['titre'];
			changer_typo($row['lang']);
			
			// dw2 .. dependance restriction telech
			$hierarchie = hierarchie_rub($id_rubrique2);
			$type='rubrique';
			// releve le niveau de restriction de l'objet / ou dependance directe
			$restrict = dependance_restriction($id_rubrique2, $type, $hierarchie, true);
			$niv_res = $restrict[0];

			$retour.="<div class='arial11' " .
			  http_style_background(_DIR_IMG_PACK."rubrique-12.gif", 
			  						"left center no-repeat; padding: 2px; padding-$spip_lang_left: 18px; margin-$spip_lang_left: 3px") . ">
									<a href='" . generer_url_ecrire("dw2_restreint","id_rub=$id_rubrique2") . "'>
									<span dir='$lang_dir'>".typo($titre2)."</span></a>&nbsp;".
									$inser_ico = icone_niveau_restreint($niv_res) .
									"</div>\n";
	}
	$retour .= "</ul>\n\n".fin_block()."\n\n";
	
	return $retour;
}


// origine: http://code.spip.net/@enfant_rub
function enfants_rubrique($collection){
	global $couleur_foncee, $lang_dir;
	global $spip_display, $spip_lang_left, $spip_lang_right, $spip_lang;

	$les_enfants = "";

	$res = sql_select("id_rubrique, id_parent, titre, descriptif, lang","spip_rubriques","id_parent='$collection'","","0+titre,titre");

	while($row=sql_fetch($res)){
		$id_rubrique=$row['id_rubrique'];
		$id_parent=$row['id_parent'];
		$titre=$row['titre'];
		
		// dw2 .. dependance restriction telech
		$hierarchie = hierarchie_rub($id_rubrique);
		$type='rubrique';
		// releve le niveau de restriction de l'objet / ou dependance directe
		$restrict = dependance_restriction($id_rubrique, $type, $hierarchie, true);
		$niv_res = $restrict[0];

		
		$les_sous_enfants = sous_enfants_rubrique($id_rubrique);

		changer_typo($row['lang']);

		$descriptif=propre($row['descriptif']);

		if ($spip_display == 4) $les_enfants .= "<li>";

		$les_enfants .= "<div class='enfants'>" .
			debut_cadre_sous_rub(($id_parent ? "rubrique-24.gif" : "secteur-24.gif"), true) .
		  (is_string($logo) ? $logo : '') .
		  (!$les_sous_enfants ? "" : bouton_block_depliable(_T("info_sans_titre"),false,"enfants$id_rubrique")) .
		  (!acces_restreint_rubrique($id_rubrique) ? "" :
		   http_img_pack("admin-12.gif", '', " width='12' height='12'", _T('image_administrer_rubrique'))) .
		  " <span dir='$lang_dir'><b><a href='" . 
		  generer_url_ecrire("dw2_restreint","id_rub=$id_rubrique") .
		  "'><font color='$couleur_foncee'>".
		  typo($titre) .
		  "</font></a></b></span>&nbsp;" .
		  $inser_ico = icone_niveau_restreint($niv_res);
		  /*(!$descriptif ? '' : "<div class='verdana1'>$descriptif</div>");*/

		if ($spip_display != 4) $les_enfants .= $les_sous_enfants;
		
		$les_enfants .= "<div style='clear:both;'></div>"  .
		  fin_cadre_sous_rub(true) .
		  "</div>";

		if ($spip_display == 4) $les_enfants .= "</li>";
	}

	changer_typo($spip_lang); # remettre la typo de l'interface pour la suite
	return (($spip_display == 4) ? "<ul> $les_enfants</ul>" :  $les_enfants);

}



//
// Menu hierarchique sur �l�ment unique
//
function aff_menu_parents($id_rubrique, $id_article, $parents="", $souche="") {
	global $spip_lang_left, $lang_dir;

	if ($id_article) {
		if(!$souche) { $souche=$id_article; }
		$result=sql_select("id_article, id_rubrique, titre",
							"spip_articles",
							"id_article=$id_article");
		while($row = sql_fetch($result)) {
			$id_article = $row['id_article'];
			$id_rubrique = $row['id_rubrique'];
			$titre = $row['titre'];
			$logo = _DIR_IMG_PACK."rubrique-12.gif";
			/*
			$parents = "<div class='verdana3' ". 
			  http_style_background($logo, "$spip_lang_left top no-repeat; padding-$spip_lang_left: 25px"). 
			  "><a href='".generer_url_ecrire("dw2_admin", "page_affiche=restreint&id_rub=".$id_rubrique)."'>".typo($titre)."</a></div>\n<div style='margin-$spip_lang_left: 3px;'>".$parents."</div>";
			*/	
		}
		aff_menu_parents($id_rubrique, $id_article="", $parents, $souche);
	}
	else if($id_rubrique) {
		if(!$souche) { $souche=$id_rubrique; }

		$result = sql_select("id_rubrique, id_parent, titre, lang","spip_rubriques","id_rubrique=$id_rubrique");

		while ($row = sql_fetch($result)) {
			$id_rubrique = $row['id_rubrique'];
			$id_parent = $row['id_parent'];
			$titre = $row['titre'];
			changer_typo($row['lang']);

			/*if (acces_restreint_rubrique($id_rubrique))
				$logo = "admin-12.gif";*/
			if (!$id_parent)
				$logo = _DIR_IMG_PACK."secteur-12.gif";
			else
				$logo = _DIR_IMG_PACK."rubrique-12.gif";

			if($id_rubrique!=$souche) {
				$parents = "<div class='verdana3' ". 
				http_style_background($logo, "$spip_lang_left top no-repeat; padding-$spip_lang_left: 25px"). 
				"><a href='".generer_url_ecrire("dw2_restreint", "id_rub=".$id_rubrique)."'>".typo($titre)."</a></div>\n<div style='margin-$spip_lang_left: 3px;'>".$parents."</div>";
			}
		}
		aff_menu_parents($id_parent, '', $parents, $souche);
	}
	else {
		if($souche) {
		$logo = _DIR_IMG_PACK."racine-site-12.gif";
		$parents = "<div class='verdana3' " .
		  http_style_background($logo, "$spip_lang_left top no-repeat; padding-$spip_lang_left: 25px"). 
		  "><a href='".generer_url_ecrire("dw2_restreint")."'><b>"._T('info_racine_site')."</b></a></div>\n<div style='margin-$spip_lang_left: 3px;'>".$parents."</div>";
	
		echo $parents;
		}
	}
}




//
// entete page restreindre
//
function afficher_entete_restreindre($id_rubrique,$id_article) {
	global $connect_id_auteur;
	
	if($id_rubrique) {
		// prepa info rubrique parent
		$row = sql_fetsel("id_parent, titre, descriptif","spip_rubriques","id_rubrique=$id_rubrique");
		$titre = $row['titre'];
		$id_parent = $row['id_parent'];
	}
	if($id_article) {
		$row=sql_fetsel("titre","spip_articles","id_article=$id_article");
		$titre = $row['titre'];
	}
	
	if($id_parent=='0') { $ico_rang = _DIR_IMG_PACK."secteur-24.gif"; }
	elseif($id_parent) { $ico_rang = _DIR_IMG_PACK."rubrique-24.gif"; }
	elseif($id_article) { $ico_rang = _DIR_IMG_PACK."article-24.gif"; }
	else {
		$ico_rang = _DIR_IMG_PACK."racine-site-24.gif";
		$titre = _T('info_racine_site');//chaine-texte spip
		$flag_racine = true;
	}
		
	// recherche sur spip_dw2_acces_restreint si restrict (hierarchie...)
	if($flag_racine) {
		$type='racine';
	}
	elseif($id_rubrique) {
		$hierarchie = hierarchie_rub($id_rubrique);
		$type='rubrique';
		$id_objet=$id_rubrique;
	}
	elseif($id_article) {
		$hierarchie = hierarchie_art($id_article);
		$type='article';
		$id_objet=$id_article;
	}

	// releve la dependance du parent directe superieur
	$restrict = dependance_restriction($id_objet, $type, $hierarchie);
	$niveau_p = $restrict[0];
	$maitre_p = $restrict[1];
	$id_maitre_p = $restrict[2];
	$titre_maitre_p = titre_maitre_dependance($maitre_p,$id_maitre_p);
	
	// releve le niveau de restriction de l'objet / ou dependance directe
	$restrict_objet = dependance_restriction($id_objet, $type, $hierarchie, true);
	$niveau_objet = $restrict_objet[0];
	
	//
	// affichage
	//
	debut_cadre_relief("");
	
		// menu hierarchique de l'objet
		aff_menu_parents($id_rubrique, $id_article, $parents="");
		
		echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'><tr>\n";
		if(!$flag_racine) { echo "<td width='5%'></td>\n"; }
		echo "<td width='7%'><img src='".$ico_rang."' border='0' valign='absmiddle' /></td>\n";
		echo "<td>";
		gros_titre(typo($titre));
		echo "</td></tr></table>\n";
	

	
	
	// formulaire "restriction"
	//
	if($flag_racine) { $id_objet='0'; }
	
	if($type == 'article') { $nomobjet = 'id_art'; }
	else { $nomobjet = 'id_rub';}
	
	echo debut_cadre_enfonce(_DIR_IMG_DW2."restreint-24.gif", true, "", _T('dw:rest_titre_formulaire'));
		
	//commentaire dependance
	if($flag_racine) {
		if($maitre_p) {
			debut_cadre_relief("", true, "", "");
				echo _T('dw:rest_des_secteurs')."<br />";
				echo _T('dw:rest_etat_tous_secteurs')._T('dw:restreint_val_'.$niveau_p);
			fin_cadre_relief();
		}
	} else {
		debut_cadre_relief("", true, "","" );
			echo "<b>"._T('dw:rest_dependance_direct_sup')."</b><br />";
		if($maitre_p) {
			echo _T('dw:rest_dependance_detail', array('maitre_p' => _T('dw:'.$maitre_p), 'titre_maitre_p'=>$titre_maitre_p)).
					_T('dw:restreint_val_'.$niveau_p);
		} else {
			echo _T('dw:rest_dependance_aucune');
		}
		fin_cadre_relief();
	}
		
		
		echo "<form action='".generer_url_action("dw2actions", "arg=restrictgen-".$id_objet)."' method='post' class='arial2'>\n";
		echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_restreint", $nomobjet."=".$id_objet)."' />\n";
		echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-restrictgen-".$id_objet)."' />";
		echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />";
		echo "<input type='hidden' name='type' value='$type' />";
	
		// selecteur
		selecteur_restreindre($niveau_objet);
		
		echo "</form>\n";
	
	echo fin_cadre_enfonce(true);
	fin_cadre_relief();
}



// http://code.spip.net/@afficher_enfant_rub
// rubrique-secteur / sous-rub dble colonne
function afficher_enfants_rubrique($id_rubrique) {
	global  $spip_lang_right;
	
	$les_enfants = enfants_rubrique($id_rubrique);
	$n = strlen($les_enfants);

	$les_enfants2=substr($les_enfants,round($n/2));

	if (strpos($les_enfants2,"<div class='enfants'>")){
		$les_enfants2=substr($les_enfants2,strpos($les_enfants2,"<div class='enfants'>"));
		$n2 = strlen($les_enfants2);
		$les_enfants=substr($les_enfants,0,$n-$n2);
	}else{
		$les_enfants2="";
	}
	
	// tableau des sous-rubs / secteurs
	echo 
		"\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n",
		"\n<tr><td valign='top' width=50% rowspan=2>\n",
		$les_enfants,
		"</td>\n",
		"\n<td width='20' rowspan='2'>",
		http_img_pack("rien.gif", ' ', "width='20'"),
	  	"</td>\n",
		"\n<td valign='top' width='50%'>",
		$les_enfants2,
		"&nbsp;",
		"</td></tr>",
		"\n<tr><td style='text-align: ",
		$spip_lang_right,
		";' valign='bottom'><div align='",
		$spip_lang_right,
	 	"'>\n";

	echo "</div></td></tr></table>\n";
}


function afficher_articles_enfants($id_rubrique) {
	global $browser_name, $vl;
	
	// recup' nombre de ligne passe en url, fixe debut LIMIT ...		
	$dl=($vl+0);
	
	// premiere val de tranche en cours
	$nba1 = $dl+1;
	
	$nbr_lignes_tableau = $GLOBALS['dw2_param']['nbr_lignes_tableau'];
	
	$q=sql_select("SQL_CALC_FOUND_ROWS id_article, titre", 
					"spip_articles",
					"id_rubrique=$id_rubrique",
					"",
					"",
					"$dl,$nbr_lignes_tableau");
	
	$nl= sql_query("SELECT FOUND_ROWS()");
	list($nligne) = @sql_fetch($nl);

		
	while($r=sql_fetch($q)) {
		$id_article=$r['id_article'];
		$titre=$r['titre'];
		$hierarchie=hierarchie_art($id_article);
		// releve le niveau de restriction de l'objet / ou dependance directe
		$rest_art = dependance_restriction($id_article, 'article', $hierarchie, true);
		
		$ret.= "<tr class='tr_liste verdana2'".
			(eregi("msie", $browser_name) ? " onmouseover=\"changeclass(this,'tr_liste_over');\" onmouseout=\"changeclass(this,'tr_liste');\"" :'').
			">\n<td colspan='2'>".
			icone_niveau_restreint($rest_art[0]).
			"&nbsp;<a href='".
			generer_url_ecrire("dw2_restreint", "id_art=".$id_article)."'>".
			typo($titre)."</a></td>\n".
			"</tr>\n";
	}
	
	//
	// aff...
	if(sql_count($q)) {
		debut_cadre_relief("article-24.gif");
		if($nligne>$nbr_lignes_tableau) {
			debut_band_titre("#dfdfdf");
				echo "<div align='center' class='verdana2'>\n";
				tranches($nba1, $nligne, $nbr_lignes_tableau);
				echo "</div>\n";
			fin_bloc();
		}	
		echo "\n<br /><table cellpadding='2' cellspacing='1' width='100%' border='0'>\n";
		echo $ret;
		echo "</table>\n";
		fin_cadre_relief();
	}
}

?>