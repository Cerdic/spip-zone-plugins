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
		if (isset($theme_zones[$zone]['titre']))
			echo "<div><b style='font-size:120%;'>".typo($theme_zones[$zone]['titre'])."</b> ($zone)</div>";
		else
			echo "<div><b style='font-size:120%;'>$zone</b></div>";
		if (isset($theme_zones[$zone]['descriptif']))
			echo "<div>".typo($theme_zones[$zone]['descriptif'])."</div>";
		echo "<br />";

		//Afficher les différentes noisettes dans des debut_cadre_relief()
		
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
		$liste = array_merge($liste,preg_files(_DIR_SKELS.$plugin."/$sousrepertoire/"));
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
	echo "<b>"._T('noisetier:ajout_noisette')."</b><br />";
	//Ajout d'une noisette
	$action_link = generer_action_auteur("noisetier", 'ajout_noisette');
	echo "<form class='ajaxAction' method='POST' action='$action_link' style='border: 0px; margin: 0px;'>";
	echo form_hidden($action_link);
	echo "<input type='hidden' name='page' value='$page' />";
	echo "<input type='hidden' name='zone' value='$zone' />";
	echo _T('noisetier:ajout_selection_noisette');
	echo " <select name='ajout_noisette' value='' class='fondo'>\n";
	$liste_noisettes = noisetier_liste_noisettes();
	foreach ($liste_noisettes as $nom => $chemin) 
		echo "<option value='$chemin'>$nom</option>\n";
	echo "</select>";
	echo " &nbsp; <input type='submit' name='valider' id='ajout_champ' value='"._T('bouton_ajouter')."' class='fondo'>";
	echo "</form>";
	fin_cadre_enfonce();
	//Ajout d'un texte
	debut_cadre_enfonce();
	$action_link = generer_action_auteur("noisetier", 'ajout_texte');
	echo "<form class='ajaxAction' method='POST' action='$action_link' style='border: 0px; margin: 0px;'>";
	echo form_hidden($action_link);
	echo "<input type='hidden' name='page' value='$page' />";
	echo "<input type='hidden' name='zone' value='$zone' />";
	echo "<b>"._T('noisetier:ajout_texte')."</b>";
	echo " &nbsp; <input type='submit' name='valider' id='ajout_champ' value='"._T('bouton_ajouter')."' class='fondo'>";
	echo "</form>";
	fin_cadre_enfonce();
}


?>