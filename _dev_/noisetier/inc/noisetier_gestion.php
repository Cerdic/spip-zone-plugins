<?php

include_spip('inc/plugin');

function noisetier_gestion_zone ($zone, $page, $cadre_formulaire=false) {
	global $theme_zones;
	if (isset($theme_zones[$zone]['insere_avant:'.$page]))
		echo $theme_zones[$zone]['insere_avant:'.$page];
	else
		echo $theme_zones[$zone]['insere_avant'];
	echo "<div id='zone-$zone' name='zone-$zone'>";

	if ($cadre_formulaire) debut_cadre_formulaire(); else debut_cadre_trait_couleur();
		if ($cadre_formulaire) $style_titre = 'font-size:120%;'; else $style_titre = 'font-size:110%;';
		if (isset($theme_zones[$zone]['titre']))
			echo "<div style='$style_titre'><b>".typo($theme_zones[$zone]['titre'])."</b> <span style='font-size:85%;'>($zone)</span></div>";
		else
			echo "<div style='$style_titre'><b>$zone</b></div>";
		if (isset($theme_zones[$zone]['descriptif'])){
			if (!$cadre_formulaire) $style_descriptif = 'font-size:90%;'; else $style_descriptif = '';
			echo "<div style='$style_descriptif'>".typo($theme_zones[$zone]['descriptif'])."</div>";
		}
		echo "<br />";

		//Afficher les différentes noisettes 
		if ($page=='') $condition = "";
		else $condition = " AND page REGEXP '(^toutes$)|((^|,)$page(,|$))' AND exclue NOT REGEXP '((^|,)$page(,|$))'";
		$query = "SELECT * FROM spip_noisettes WHERE zone='$zone'$condition ORDER BY position";
		$res = spip_query($query);
		while ($row = spip_fetch_array($res)) {
			$type = $row['type'];
			if ($type=='texte') noisetier_affiche_texte($row);
			if ($type=='noisette') noisetier_affiche_noisette($row);
		}
		
		//Formulaire d'ajout d'une noisette
		noisetier_form_ajout_noisette_texte($page==''?'toutes':$page,$zone);

	if ($cadre_formulaire) fin_cadre_formulaire(); else fin_cadre_trait_couleur();
	echo '<br />';

	echo "</div>";
	if (isset($theme_zones[$zone]['insere_apres:'.$page]))
		echo $theme_zones[$zone]['insere_apres:'.$page];
	else
		echo $theme_zones[$zone]['insere_apres'];

}

// Liste les éléments html d'un sous répertoire qu'ils soient dans un plugin ou dans le répertoire squelettes
function noisetier_liste_elements_sousrepertoire ($sousrepertoire) {
	static $liste;
	if (!isset($liste)) {
		$liste = array();
		// Recherche dans le répertoire des squelettes
		$liste = array_merge($liste,preg_files("../squelettes".$plugin."/$sousrepertoire/"));
		// Recherche dans les plugins
		$lcpa = liste_chemin_plugin_actifs();
		foreach ($lcpa as $plugin)
			$liste = array_merge($liste,preg_files(_DIR_PLUGINS.$plugin."/$sousrepertoire/"));
	}
	return $liste;
}

// Liste des noisettes possibles
function noisetier_liste_noisettes() {
	static $result;
	if (!isset($result)) {
		$result = array();
		$liste = noisetier_liste_elements_sousrepertoire ('noisettes');
		foreach ($liste as $chemin) {
			if(preg_match('/\/noisettes\/([[:graph:]]+).htm/',$chemin,$noisette)) $result[$noisette[1]]=$chemin;
		}
		asort($result);
	}
	return $result;
}

// Formulaire d'ajout d'une noisette
function noisetier_form_ajout_noisette_texte($page,$zone) {
	debut_cadre_enfonce();
	echo bouton_block_invisible("form-ajout-$zone");
	echo "<b>"._T('noisetier:ajout_noisette_texte')."</b>";
	$redirect = generer_url_ecrire('noisetier',"page=$page");;
	//Ajout d'une noisette
	$action_link = generer_action_auteur("noisetier_ajout", 'ajout_noisette', $redirect);
	echo debut_block_invisible("form-ajout-$zone");
	echo "<form class='ajaxAction' method='POST' action='$action_link' style='border: 0px; margin: 10px 0px;'>";
	echo form_hidden($action_link);
	echo "<input type='hidden' name='page' value='$page' />";
	echo "<input type='hidden' name='zone' value='$zone' />";
	//echo _T('noisetier:ajout_noisette');
	echo _T('noisetier:ajout_selection_noisette');
	echo " <select name='url_noisette' value='' class='fondo' style='width:150px;'>\n";
	$liste_noisettes = noisetier_liste_noisettes();
	foreach ($liste_noisettes as $nom => $chemin) 
		echo "<option value='$chemin'>$nom</option>\n";
	echo "</select>";
	echo " &nbsp; <input type='submit' name='valider' id='ajout_champ' value='"._T('bouton_ajouter')."' class='fondo'>";
	echo "</form>";
	echo "<hr />";
	//Ajout d'un texte
	$action_link = generer_action_auteur("noisetier_ajout", 'ajout_texte', $redirect);
	echo "<form class='ajaxAction' method='POST' action='$action_link' style='border: 0px; margin: 10px 0px;'>";
	echo form_hidden($action_link);
	echo "<input type='hidden' name='page' value='$page' />";
	echo "<input type='hidden' name='zone' value='$zone' />";
	echo _T('noisetier:ajout_texte');
	echo " &nbsp; <input type='submit' name='valider' id='ajout_champ' value='"._T('bouton_ajouter')."' class='fondo'>";
	echo "</form>";
	echo fin_block();
	fin_cadre_enfonce();
}

// Affiche un texte
function noisetier_affiche_texte($row) {
	global $couleur_claire, $spip_lang_left, $spip_lang_right;
	debut_cadre_relief();
	$id_noisette = $row['id_noisette'];
	echo "<img src='"._DIR_PLUGIN_NOISETIER."img_pack/texte-24.png' class ='sortableChampsHandle' style='float:$spip_lang_left;position:relative;margin-right:5px;'/>";
	// Supprimer le texte (ajouter action)
	echo "<div class='verdana1' style='float: $spip_lang_right; font-weight: bold;position:relative;display:inline;'>";
	echo "<a ><img src='"._DIR_IMG_PACK."supprimer.gif' style='border:0' alt='"._T("noisetier:supprimer_texte")."'></a>";
	echo "</div>\n";
	echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>";
	echo bouton_block_invisible("noisette-$id_noisette");
	echo "<strong id='titre_nom_$id_noisette'>".typo($row['titre'])."</strong>";
	echo "<div style='font-size:90%;'>".typo($row['descriptif'])."</div></div>";
	echo debut_block_invisible("noisette-$id_noisette");
	
	echo fin_block();
	fin_cadre_relief();
}

// Affiche une noisette
function noisetier_affiche_noisette($row) {
	global $couleur_claire, $spip_lang_left, $spip_lang_right;
	debut_cadre_relief();
	$id_noisette = $row['id_noisette'];
	echo "<img src='"._DIR_PLUGIN_NOISETIER."img_pack/noisette-24.png' class ='sortableChampsHandle' style='float:$spip_lang_left;position:relative;margin-right:5px;'/>";
	// Supprimer le texte (ajouter action)
	echo "<div style='float: $spip_lang_right; position:relative;'>";
	echo "<a ><img src='"._DIR_IMG_PACK."supprimer.gif' style='border:0' alt='"._T("noisetier:supprimer_noisette")."'></a>";
	echo "</div>\n";
	echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>";
	echo bouton_block_invisible("noisette-$id_noisette");
	echo "<strong id='titre_nom_$id_noisette'>".typo($row['titre'])."</strong>";
	echo "<div style='font-size:90%;'>".typo($row['descriptif'])."</div></div>";
	echo debut_block_invisible("noisette-$id_noisette");
	
	echo fin_block();
	fin_cadre_relief();
}

?>