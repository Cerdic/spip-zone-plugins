<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/presentation');

/**
 * Compte le nombre de visites des objets d'une rubrique et de ses sous-rubriques
 *
 * On peut passer en paramètre un ou plusieurs types d'objets pour n'afficher que les visites de ces objets,
 * sinon on retourne les visites de tous les types d'objets trouvés dans spip_visites_articles et spip_visites_objets
 *
 * @param  integer $id_parent         Identifiant de la rubrique parente à partir de laquelle compter
 * @param  string  $critere           Critère pour le comptage : visites|popularite
 * @param  integer $nombre_branche    Nombre d'objets dans la branche
 * @param  integer $nombre_rub        Nombre d'objets dans la rubrique
 * @param  array   $objets            Types d'objets
 * @return integer                    Nombre d'objets
 */
function enfants_objets($id_parent, $critere, &$nombre_branche, &$nombre_rub, $objets=array()) {

	include_spip('base/objets'); // on ne sait jamais

	$result = sql_select("id_rubrique", "spip_rubriques", "id_parent=" . intval($id_parent));
	$nombre = 0;

	while ($row = sql_fetch($result)) {
		$visites = 0;
		$id_rubrique = $row['id_rubrique'];
		foreach($objets as $objet){
			$visites += intval(sql_getfetsel("SUM(" . $critere . ")", table_objet_sql($objet), "id_rubrique=" . intval($id_rubrique)));
		}
		$nombre_rub[$id_rubrique] = $visites;
		$nombre_branche[$id_rubrique] = $visites;
		$nombre += $visites + enfants_objets($id_rubrique, $critere, $nombre_branche, $nombre_rub, $objets);
	}
	if (!isset($nombre_branche[$id_parent])) {
		$nombre_branche[$id_parent] = 0;
	}
	$nombre_branche[$id_parent] += $nombre;

	return $nombre;
}


/**
 * Affiche un tableau avec les pourcentages de visites par secteur
 *
 * On peut passer en paramètre un ou plusieurs types d'objets pour n'afficher que les visites de ces objets,
 * sinon on retourne les visites de tous les types d'objets trouvés dans spip_visites_articles et spip_visites_objets
 *
 * @param  integer      $id_parent    Identifiant de la rubrique parente à partir de laquelle compter
 * @param  integer      $decalage     ?
 * @param  integer      $taille       ?
 * @param  string       $critere      Critère pour le comptage : visites|popularite
 * @param  integer      $gauche       Décalage entre les pourcentages et les barres
 * @param  array        $objets       Types d'objets
 * @return string                     Contenu HTML
 */
function enfants_objets_aff($id_parent, $decalage, $taille, $critere, $gauche = 0, $objets='') {

	include_spip('base/objets'); // on ne sait jamais

	// Sans objets demandés explicitement, on va chercher tous les types d'objets
	// ayant un champ id_rubrique dans spip_visites_articles et spip_visites_objets,
	// à l'exclusion des rubriques.
	if (!$objets) {
		$objets = array();
		if (sql_countsel('spip_visites_articles')) {
			$objets[] = 'article';
		}
		if ($objets_visites = sql_fetsel('DISTINCT objet', 'spip_visites_objets', 'objet != '.sql_quote('rubrique'))) {
			foreach($objets_visites as $objet) {
				// on s'assure que l'objet ait un champ id_rubrique
				$table_objet_sql = table_objet_sql($objet);
				$trouver_table   = charger_fonction('trouver_table','base');
				$desc            = $trouver_table($table_objet_sql);
				if (isset($desc['field']['id_rubrique'])) {
					$objets[] = $objet;
				}
			}
		}
	}
	// sinon on s'assure d'avoir un array des objets passés en paramètres
	elseif (is_string($objets)) {
		$objets = array($objets);
	}

	global $spip_lang_right, $spip_lang_left;
	static $total_site = null;
	static $niveau = 0;
	static $nombre_branche;
	static $nombre_rub;
	if (is_null($total_site)) {
		$nombre_branche = array();
		$nombre_rub = array();
		$total_site = enfants_objets(0, $critere, $nombre_branche, $nombre_rub, $objets);
		if ($total_site < 1) {
			$total_site = 1;
		}
	}

	$visites_abs = 0;
	$out = "";
	$width = intval(floor(($nombre_branche[$id_parent] / $total_site) * $taille));
	$width = "width:{$width}px;float:$spip_lang_left;";

	$result = sql_select("id_rubrique, titre, descriptif", "spip_rubriques", "id_parent=$id_parent", '', '0+titre,titre');

	while ($row = sql_fetch($result)) {
		$id_rubrique = $row['id_rubrique'];
		$titre = typo($row['titre']);
		$descriptif = attribut_html(couper(typo($row['descriptif']), 80));

		if ($nombre_branche[$id_rubrique] > 0 or $nombre_rub[$id_rubrique] > 0) {
			$largeur_branche = ceil(($nombre_branche[$id_rubrique] - $nombre_rub[$id_rubrique]) * $taille / $total_site);
			$largeur_rub = ceil($nombre_rub[$id_rubrique] * $taille / $total_site);

			if ($largeur_branche + $largeur_rub > 0) {

				if ($niveau == 0) {
					$couleur = "#cccccc";
				} else {
					if ($niveau == 1) {
						$couleur = "#eeeeee";
					} else {
						$couleur = "white";
					}
				}
				$out .= "<table cellpadding='2' cellspacing='0' border='0' width='100%'>";
				$out .= "\n<tr style='background-color: $couleur'>";
				$out .= "\n<td style='border-bottom: 1px solid #aaaaaa; padding-$spip_lang_left: " . ($niveau * 20 + 5) . "px;'>";


				if ($largeur_branche > 2) {
					$out .= bouton_block_depliable(
						"<a href='" . generer_url_entite($id_rubrique,'rubrique') . "' style='color: black;' title=\"$descriptif\">$titre</a>",
						"incertain",
						"stats$id_rubrique"
					);
				} else {
					$out .= "<div class='rubsimple' style='padding-left: 18px;'>"
						. "<a href='" . generer_url_entite($id_rubrique,
							'rubrique') . "' style='color: black;' title=\"$descriptif\">$titre</a>"
						. "</div>";
				}
				$out .= "</td>";


				// pourcentage de visites dans la branche par rapport au total du site
				$pourcent = round($nombre_branche[$id_rubrique] / $total_site * 1000) / 10;
				$nb_visites = singulier_ou_pluriel($nombre_branche[$id_rubrique], 'statistiques:info_1_visite', 'statistiques:info_nb_visites');
				$out .= "\n<td class='verdana1' style='text-align: $spip_lang_right; width: 40px; border-bottom: 1px solid #aaaaaa;'><abbr title='$nb_visites'>$pourcent%</abbr></td>";


				$out .= "\n<td align='right' style='border-bottom: 1px solid #aaaaaa; width:" . ($taille + 5) . "px'>";


				$out .= "\n<table cellpadding='0' cellspacing='0' border='0' width='" . ($decalage + 1 + $gauche) . "'>";
				$out .= "\n<tr>";
				if ($gauche > 0) {
					$out .= "<td style='width: " . $gauche . "px'></td>";
				}
				$out .= "\n<td style='border: 0px; white-space: nowrap;'>";
				$out .= "<div style='border: 1px solid #999999; background-color: #dddddd; height: 1em; padding: 0px; margin: 0px;$width'>";
				if ($visites_abs > 0) {
					$out .= "<img src='" . chemin_image('rien.gif') . "' style='vertical-align: top; height: 1em; border: 0px; width: " . $visites_abs . "px;' alt= ' '/>";
				}
				if ($largeur_branche > 0) {
					$out .= "<img src='" . chemin_image('rien.gif') . "' class='couleur_cumul' style='vertical-align: top; height: 1em; border: 0px; width: " . $largeur_branche . "px;' alt=' ' />";
				}
				if ($largeur_rub > 0) {
					$out .= "<img src='" . chemin_image('rien.gif') . "' class='couleur_nombre' style='vertical-align: top; width: " . $largeur_rub . "px; height: 1em; border: 0px' alt=' ' />";
				}
				$out .= "</div>";
				$out .= "</td></tr></table>\n";
				$out .= "</td></tr></table>";
			}
		}

		if (isset($largeur_branche) && ($largeur_branche > 0)) {
			$niveau++;
			$out .= debut_block_depliable(false, "stats$id_rubrique");
			$out .= enfants_objets_aff($id_rubrique, $largeur_branche, $taille, $critere, $visites_abs + $gauche, $objets);
			$out .= fin_block();
			$niveau--;
		}
		$visites_abs = $visites_abs + round($nombre_branche[$id_rubrique] / $total_site * $taille);
	}

	return $out;
}
