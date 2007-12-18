<?php
/*
+-------------------------------------------+

+-------------------------------------------+
| generer arbo rubriques comme spip
+-------------------------------------------+
*/


// http://doc.spip.org/@sous_enfant_rub
function sous_enfant_rubfo($collection2){
	global $lang_dir, $spip_lang_dir, $spip_lang_left;
	if (!function_exists('debut_block_invisible')) include_spip('inc/vieilles_defs');

	$result3 = sql_query("SELECT * FROM spip_rubriques WHERE id_parent='$collection2' ORDER BY 0+titre,titre");

	if (!sql_count($result3)) return '';
	$retour = debut_block_invisible("enfants$collection2")."\n<ul style='margin: 0px; padding: 0px; padding-top: 3px;'>\n";
	while($row=sql_fetch($result3)){
			$id_rubrique2=$row['id_rubrique'];
			$id_parent2=$row['id_parent'];
			$titre2=supprimer_numero($row['titre']);
			changer_typo($row['lang']);

			$retour.="<div class='arial11' " .
			  http_style_background("rubrique-12.gif", "left center no-repeat; padding: 2px; padding-$spip_lang_left: 18px; margin-$spip_lang_left: 3px")
			  			. "><a href='"
						. generer_url_ecrire("spipbb_admin","id_salon=$id_rubrique2")
						. "'><span dir='$lang_dir'>"
						. typo($titre2)."</span></a></div>\n";
	}
	$retour .= "</ul>\n\n".fin_block()."\n\n";
	
	return $retour;
}


// http://doc.spip.org/@enfant_rub
function enfant_rubfo($collection){
	global $couleur_foncee, $lang_dir;
	global $spip_display, $spip_lang_left, $spip_lang_right, $spip_lang;


	$les_enfants = "";

	$res = sql_query("SELECT id_rubrique, id_parent, titre, descriptif, lang 
					FROM spip_rubriques 
					WHERE id_parent='$collection' 
					ORDER BY 0+titre,titre");

	# compter les forums
	if($nombre_forums=sql_count($res)) {
		$flag_ordonne = ($nombre_forums>1)?true:false;
	}
	else $flag_ordonne = false;


	while($row=sql_fetch($res)) {
		$id_rubrique=$row['id_rubrique'];
		$id_parent=$row['id_parent'];
		$titre=supprimer_numero($row['titre']);
		
		$ifond = $ifond ^ 1;
		$coul_ligne = ($ifond) ? $couleur_claire : '#ffffff';
		
		/*
		// gafospip . trouver rubrique secteur (hotel) des forums, 
		// si unique secteur forums -> usage mot : rub_gaforum
		$rq_gaf =	"SELECT smr.id_rubrique 
					FROM spip_mots_rubriques smr 
					LEFT JOIN spip_rubriques sr ON sr.id_rubrique = smr.id_rubrique
					WHERE sr.id_rubrique=$id_rubrique AND smr.id_mot = ".$GLOBALS['id_mot_rub_gaf'];
		$rs_gaf = sql_query($rq_gaf);
		if(sql_count($rs_gaf)) { $icone_secteur = _DIR_IMG_GAF."gaf_ico-24.gif"; }
		*/
		if($id_rubrique == $GLOBALS['spipbb']['id_secteur']) {
			$icone_secteur = _DIR_IMG_SPIPBB."spipbb-24.png";
		}
		else { $icone_secteur = "secteur-24.gif"; }
		//
		
		$les_sous_enfants = sous_enfant_rubfo($id_rubrique);

		changer_typo($row['lang']);

		$descriptif=propre($row['descriptif']);

		if ($spip_display == 4) $les_enfants .= "";


		$les_enfants .= "<tr class='verdana3' bgcolor='".$coul_ligne."'><td width='6%' valign='top'>"
			. http_img_pack(($id_parent ? "rubrique-24.gif" : $icone_secteur), '','')
			. "</td><td width='2%' valign='top'>"
			. (!$les_sous_enfants ? "" : bouton_block_invisible("enfants$id_rubrique"))
			. "</td><td width='93%' valign='top'>"
			. (!acces_restreint_rubrique($id_rubrique) ? "" : 
				http_img_pack("admin-12.gif", '', '', _T('image_administrer_rubrique')))
			. ""
			. "<span dir='$lang_dir' style='color:$couleur_foncee;'><b>"
			. "<a href='" . generer_url_ecrire("spipbb_admin","id_salon=$id_rubrique") ."'>"
			. typo($titre)
			. "</a></b></span>"
			. (!$descriptif ? '' : "<div class='verdana1'>$descriptif</div>");

		if ($spip_display != 4) $les_enfants .= $les_sous_enfants;
		
		$les_enfants .= "<div style='clear:both;'></div>" . "</td>";

		if($flag_ordonne AND _request('id_salon')) {
			$les_enfants .= "<td width='3%' valign='top' class='verdana2'>\n";
			$les_enfants .= bouton_ordonne_salon($id_rubrique,generer_url_ecrire("spipbb_admin","id_salon="._request('id_salon'),true));
			$les_enfants .= "</td>";
		}
		
		$les_enfants .= "</tr>";

		if ($spip_display == 4) $les_enfants .= "";
	}

	changer_typo($spip_lang); # remettre la typo de l'interface pour la suite
	return $les_enfants;

}



// http://doc.spip.org/@afficher_enfant_rub
function afficher_enfant_rubfo($id_rubrique, $afficher_bouton_creer=false) {
	global  $spip_lang_right;

	echo "\n<table cellpadding='3' cellspacing='0' border='0' width='600'>\n";
	echo enfant_rubfo($id_rubrique);
	echo "</table>\n";

}

?>
