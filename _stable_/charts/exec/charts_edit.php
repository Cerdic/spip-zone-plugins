<?php
/*
 * charts
 *
 * Auteur :
 * Cedric MORIN
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/charts');


function charts_update(){
	include_spip('base/abstract_sql');
	$id_chart = intval(_request('id_chart'));
	$new = _request('new');
	$supp_chart = intval(_request('supp_chart'));
	$retour = _request('retour');
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$code = _request('code');
	$hauteur = _request('hauteur');
	$largeur = _request('largeur');
	$background = _request('background');
	$supp_confirme = _request('supp_confirme');
	$supp_rejet = _request('supp_rejet');

	//
	// Modifications des donnees de base du formulaire
	//
	if (charts_chart_administrable($id_chart)) {
		if ($supp_chart = intval($supp_chart) AND $supp_confirme AND !$supp_rejet) {
			$query = "DELETE FROM spip_charts WHERE id_chart=$supp_chart";
			$result = spip_query($query);
			if ($retour) {
				$retour = urldecode($retour);
				Header("Location: $retour");
				exit;
			}
		}
	}
	
	if (charts_chart_editable($id_chart)) {
		// creation
		if ($new == 'oui' && $titre) {
			$id_chart = spip_abstract_insert(
			"spip_charts","(titre,descriptif,largeur,hauteur,code,background)",
			"(".
				spip_abstract_quote($titre).", ".
				spip_abstract_quote($descriptif).", ".
				spip_abstract_quote($largeur).", ".
				spip_abstract_quote($hauteur).", ".
				spip_abstract_quote($code).", ".
				spip_abstract_quote($background) . ")");
		}
		// maj
		else if ($id_chart && $titre) {
			spip_query("UPDATE spip_charts SET ".
				"titre=".spip_abstract_quote($titre).", ".
				"descriptif=".spip_abstract_quote($descriptif).", ".
				"largeur=".spip_abstract_quote($largeur).", ".
				"hauteur=".spip_abstract_quote($hauteur).", ".
				"code=".spip_abstract_quote($code).", ".
				"background=".spip_abstract_quote($background).
				"WHERE id_chart=$id_chart");
		}
		// lecture
		$result = spip_query("SELECT * FROM spip_charts WHERE id_chart=".spip_abstract_quote($id_chart));
		if ($row = spip_fetch_array($result)) {
			$id_chart = $row['id_chart'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$largeur = $row['largeur'];
			$hauteur = $row['hauteur'];
			$code = $row['code'];
			$background = $row['background'];
		}
	}	
	
	return $id_chart;
}

function exec_charts_edit(){
	global $spip_lang_right;
	$id_chart = intval(_request('id_chart'));
	$new = _request('new');
	$supp_chart = intval(_request('supp_chart'));
	$retour = _request('retour');
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$code = _request('code');
	$hauteur = _request('hauteur');
	$largeur = _request('largeur');
	$background = _request('background');
	$supp_confirme = _request('supp_confirme');
	$supp_rejet = _request('supp_rejet');

	
  charts_verifier_base();

	if ($retour)
		$retour = urldecode($retour);
  include_spip("inc/presentation");
	include_spip("inc/config");

	$clean_link = parametre_url(self(),'new','');
	$chart_link = generer_url_ecrire('charts_edit');
	if ($new == 'oui' && !$titre)
		$chart_link = parametre_url($chart_link,"new",$new);
	if ($retour) 
		$chart_link = parametre_url($chart_link,"retour",urlencode($retour));

		
	//
	// Recupere les donnees
	//
	if ($new == 'oui' && !$titre) {
		$titre = _T("charts:nouveau_graphique");
		$descriptif = "";
		$code = "";
		$hauteur = 300;
		$largeur = 400;
		$background = "ffffff";
		$js_titre = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
	}
	else {
		//
		// Modifications au structure du formulaire
		//
		$id_chart = charts_update();
	
		$result = spip_query("SELECT * FROM spip_charts WHERE id_chart=".spip_abstract_quote($id_chart));
		if ($row = spip_fetch_array($result)) {
			$id_chart = $row['id_chart'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$largeur = $row['largeur'];
			$hauteur = $row['hauteur'];
			$code = $row['code'];
			$background = $row['background'];
		}
		$js_titre = "";
	}
	$chart_link = parametre_url($chart_link,"id_chart",$id_chart);
	$clean_link = parametre_url($clean_link,"id_chart",$id_chart);

	//
	// Affichage de la page
	//

	debut_page("&laquo; $titre &raquo;", "documents", "charts","");

	debut_gauche();
	echo "<br /><br />\n";

	debut_droite();

	if ($supp_chart && $supp_confirme==NULL && $supp_rejet==NULL) {
		echo "<p>";
		echo _T('charts:confirmer_supression')."</p>\n";
		$link = parametre_url($clean_link,'supp_chart', $supp_chart);
		echo "<form method='POST' action='"
			. $link
			. "' style='border: 0px; margin: 0px;'>";
		echo "<div style='text-align:$spip_lang_right'>";
		echo "<input type='submit' name='supp_confirme' value=\""._T('item_oui')."\" class='fondo'>";
		echo " &nbsp; ";
		echo "<input type='submit' name='supp_rejet' value=\""._T('item_non')."\" class='fondo'>";
		echo "</div>";
		echo "</form><br />\n";
	}


	if ($id_chart) {
		debut_cadre_relief("../"._DIR_PLUGIN_CHARTS."/img_pack/chart-24.gif");

		gros_titre($titre);

		if ($descriptif) {
			echo "<p /><div align='left' border: 1px dashed #aaaaaa;'>";
			echo "<strong class='verdana2'>"._T('info_descriptif')."</strong> ";
			echo propre($descriptif);
			echo "</div>\n";
		}

		if (strlen($code)) {
			echo "<br />";

			echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
			echo bouton_block_visible("preview_chart");
			echo "<strong class='verdana3' style='text-transchart: uppercase;'>"
				._T("charts:apparence_graphique")."</strong>";
			echo "</div>\n";

			echo debut_block_visible("preview_chart");
			echo _T("charts:previsu_info")."<p>\n";
			echo "<div class='spip_charts'>";
			echo propre("<chart$id_chart>");
			echo "</div>\n";
			echo fin_block();
		}

		afficher_articles(_T("charts:articles_lies"),
			array('FROM' => 'spip_articles AS articles, spip_charts_articles AS lien',
			'WHERE' => "lien.id_article=articles.id_article AND id_chart=$id_chart AND statut!='poubelle'",
			'ORDER BY' => "titre"));

		fin_cadre_relief();
	}


	//
	// Icones retour et suppression
	//
	echo "<div style='text-align:$spip_lang_right'>";
	if ($retour) {
		icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_CHARTS."/img_pack/chart-24.gif", "rien.gif",'right');
	}
	if ($id_chart && charts_chart_administrable($id_chart)) {
		echo "<div style='float:$spip_lang_left'>";
		$link = parametre_url($clean_link,'supp_chart', $id_chart);
		if (!$retour) {
			$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('chart_tous')));
		}
		icone(_T("charts:supprimer"), $link, "../"._DIR_PLUGIN_CHARTS."/img_pack/chart-24.gif", "supprimer.gif");
		echo "</div>";
	}
	echo "<div style='clear:both;'></div>";
	echo "</div>";

	//
	// Edition des donnees du formulaire
	//
	if (charts_chart_editable($id_chart)) {
		echo "<p>";
		debut_cadre_formulaire();

		echo "<div class='verdana2'>";
		echo "<form method='POST' action='"
			. $chart_link
			. "' style='border: 0px; margin: 0px;'>";

		echo "<strong><label for='titre_chart'>"._T("charts:titre_graphique")."</label></strong> "._T('info_obligatoire_02');
		echo "<br />";
		echo "<input type='text' name='titre' id='titre_chart' CLASS='formo' ".
			"value=\"".entites_html($titre)."\" size='40'$js_titre /><br />\n";

		echo "<strong><label for='desc_chart'>"._T('info_descriptif')."</label></strong>";
		echo "<br />";
		echo "<textarea name='descriptif' id='desc_chart' class='forml' rows='4' cols='40' wrap='soft'>";
		echo entites_html($descriptif);
		echo "</textarea><br />\n";

		echo "<strong><label for='largeur_chart'>"._T("charts:largeur")."</label></strong> "._T('info_obligatoire_02');
		echo "<br />";
		echo "<input type='text' name='largeur' id='largeur_chart' CLASS='formo' ".
			"value=\"".$largeur."\" size='40' /><br />\n";
			
		echo "<strong><label for='hauteur_chart'>"._T("charts:hauteur")."</label></strong> "._T('info_obligatoire_02');
		echo "<br />";
		echo "<input type='text' name='hauteur' id='hauteur_chart' CLASS='formo' ".
			"value=\"".$hauteur."\" size='40' /><br />\n";
			
		echo "<strong><label for='background_chart'>"._T("charts:couleur_fond")."</label></strong> "._T('info_obligatoire_02');
		echo "<br />";
		echo "<input type='text' name='background' id='background_chart' CLASS='formo' ".
			"value=\"".$background."\" size='40' /><br />\n";

		echo "<strong><label for='code_chart'>"._T('charts:code_du_graphique')."</label></strong>";
		echo "<br />";
		echo "<textarea name='code' id='code_chart' class='forml' rows='70' cols='40' wrap='soft'>";
		echo entites_html($code);
		echo "</textarea><br />\n";
		
 	
		echo "<div align='right'>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";

		echo "</form>";

		echo "</div>\n";

		fin_cadre_formulaire();
	}


	fin_page();
}
?>
