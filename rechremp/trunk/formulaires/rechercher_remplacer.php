<?php
/**
 * Plugin Rechercher/Remplacer
 * Licence GPL-v3.
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
/**
 * Taille du contexte max affiché avant et après chaque extrait trouvé
 * Si c'est défini à 0, seul le titre de chaque résultat est affiché
 * Sinon chaque occurence est listée
 *  et la valeur indique le nombre de caractère qui doit être présentés de chaque côté de chaque occurence
 */
defined('RECHREMP_CONTEXTE_NB_CHARS') or define('RECHREMP_CONTEXTE_NB_CHARS', 20);

function formulaires_rechercher_remplacer_charger_dist() {
	$valeurs = array(
		'search' => '',
		'replace_yes' => '',
		'replace' => '',
	);

	return $valeurs;
}

function formulaires_rechercher_remplacer_verifier_dist() {
	$erreurs = array();

	if (!_request('search')) {
		$erreurs['search'] = _T('info_obligatoire');
	} else {
		if (!_request('remplacer')) {
			$erreurs['message_erreur'] = '';
			// recherche a blanc pour voir/confirmer le remplacement
			$erreurs['search_results'] =
				"<input type='hidden' name='replace_check_table[dummy]' value='yes' />"
				.rechremp_search_and_replace(_request('search'), '', false, _request('replace_yes') ? 'replace_check_table' : null);
		}
	}

	return $erreurs;
}

function formulaires_rechercher_remplacer_traiter_dist() {
	$res = array();

	// remplacer si demande
	if (_request('remplacer') and _request('replace_yes')) {
		$check_replace = _request('replace_check_table');
		$res['message_ok'] =
			'<h3>'._T('rechremp:resultat_remplacement').'<small>&#171;&nbsp;'.entites_html(_request('search')).'&nbsp;&#187;</small></h3>'
		.rechremp_search_and_replace(_request('search'), _request('replace'), true, $check_replace);
	} else {
		// sinon simple recherche, mais normalement on arrive pas la
		$res['message_ok'] = rechremp_search_and_replace(_request('search'));
	}

	return $res;
}

/**
 * @param string $search        chaine recherchée
 * @param string $replace       chaine remplaçant, si définie
 * @param bool $do_replace      veut on remplacer ?
 * @param array|string $check_replace
 *                  1er appel : chaine vide ou non vide selon qu'on veut remplacer ou pas
 *                  2eme appel dans le cas où on veut remplacer, pour confirmer dans quelles tables on veut remplacer :
 *                      tableau de booléens dont l'index est une table à vérifier ou non
 *                      exemple : Array ([dummy] => yes, [spip_forum] => on)
 * @return array|string
 *                  La liste des résultats de recherche, groupés par table
 *                  avec des checkbox pour chaque table afin de confirmer le remplacement ou non
 */
function rechremp_search_and_replace($search, $replace = null, $do_replace = false, $check_replace = null) {
	include_spip('base/objets');
	$tables_exclues = array('spip_messages','spip_depots','spip_paquets','spip_plugins');
	$champs_exclus = array('extra','tables_liees','obligatoire','comite','minirezo','forum','mode','fichier','distant','media');
	$liste = lister_tables_objets_sql();
	$trouver_table = charger_fonction('trouver_table', 'base');

	$out = array();
	foreach ($liste as $table => $desc) {
		if (!in_array($table, $tables_exclues)) {
			$champs = array();
			if (isset($desc['champs_editables']) and $desc['champs_editables']) {
				$champs = $desc['champs_editables'];
			} elseif (isset($desc['champs_versionnes'])) {
				$champs = $desc['champs_versionnes'];
			}

			// trouver les champs de la vraie table
			$desc = $trouver_table($table);
			// pas touche au champ extra serialize
			$champs = array_diff($champs, $champs_exclus);
			// que les champs qui existent
			$champs = array_intersect($champs, array_keys($desc['field']));
			// et qui sont en texte
			foreach ($champs as $c) {
				if (!preg_match(',text|varchar,', $desc['field'][$c])) {
					$champs = array_diff($champs, array($c));
				}
			}

			if (count($champs)) {
				$replace_here = $do_replace;
				if (is_array($check_replace) and !isset($check_replace[$table])) {
					$replace_here = false;
				}

				$t = rechremp_search_and_replace_table($table, $champs, $search, $replace, $replace_here);
				if ($t and is_string($check_replace)) {
					$i = "<input type='checkbox' name='{$check_replace}[$table]' />";
					$t = preg_replace(',<label[^>]*>,', "\\0$i", $t, 1);
				}
				if ($t) {
					if ($do_replace and !$replace_here) {
						$t = _T('rechremp:aucun_remplacement_sur', array('objets' => _T(objet_info(objet_type($table), 'texte_objets'))));
					}
					$out[] = $t;
				}
			}
		}
	}
	$out = array_filter($out);
	if (count($out)) {
		$out = implode('<br />', $out);
	} else {
		$out = _T('rechremp:aucune_occurence_trouvee');
	}

	return $out;
}

/**
 * @param $table            table dans laquelle la recherche se fait
 * @param $champs           les champs textes déclarés pour cette table
 * @param $search           la recherche
 * @param null $replace     la chaine qui remplace
 * @param bool $do_replace  faut il remplacer ?
 * @return string           liste présentant les résultats de la recherche
 */
function rechremp_search_and_replace_table($table, $champs, $search, $replace = null, $do_replace = false) {
	if (!count($champs) or !$search) {
		return '';
	}

	$len = intval(RECHREMP_CONTEXTE_NB_CHARS);
	$len_moins_un = max($len-1, 0);
	$pattern = "/(^.{0,$len_moins_un}|.{".$len.'})'.preg_quote($search, '/')."(.{0,$len_moins_un}$|.{".$len.'})/s';
	// Par exemple : "/(^.{0,9}|.{10})ma recherche(.{0,9}$|.{10})/s"

	include_spip('action/editer_objet');
	include_spip('inc/filtres');
	include_spip('inc/texte');

	$objet = objet_type($table);
	$primary = id_table_objet($table);
	$select = "$primary,".implode(',', $champs);

	$nb_occurences = 0;
	$contextes = $founds = array();
	$res = sql_select($select, $table);

	while ($row = sql_fetch($res)) {
		$set = array();
		foreach ($champs as $c) {
			$nb = 0;

			$v = str_replace($search, $replace, $row[$c], $nb);
			// si on a confirmé un remplacement, $v est le résultat du remplacement
			// sinon c'est $nb seulement qui nous intéresse ($v est inutilisable car $replace est vide)

			if ($nb) {
				$set[$c] = $v;
				if (!isset($founds[$row[$primary]])) {
					$founds[$row[$primary]] = 0;
				}
				$founds[$row[$primary]] += $nb;
				if (RECHREMP_CONTEXTE_NB_CHARS) {
					preg_match_all($pattern, $row[$c], $matches, PREG_SET_ORDER);
					$contextes[$row[$primary]] = $matches;      // cool raoul
				}
				$nb_occurences += $nb;
			}
		}

		// Mise à jour d'un champ de la table
		if ($do_replace and count($set)) {
			objet_modifier($objet, $row[$primary], $set);
		}
	}

	if (!$nb_occurences) {
		return '';
	}

	$out = singulier_ou_pluriel($nb_occurences, 'rechremp:1_occurence_dans', 'rechremp:nb_occurences_dans');

	$out .= ' '.objet_afficher_nb(count($founds), $objet);
	$out = "<label><strong>$out</strong></label><ul class='spip'>";

	// dans un fichier d'options on peut personnaliser l'affichage de chaque ligne de résultats 
	// via la constante RECHREMP_INFO_RESULTAT_A_GENERER et en spécifiant un autre champ que le titre
	// ou avec une 'info' calculée par une fonction generer_${info}_${type_objet}($id, $objet)
	// et/ou generer_$info_entite($id,$type,$objet)

	if (!defined('RECHREMP_INFO_RESULTAT_A_GENERER'))
		define ('RECHREMP_INFO_RESULTAT_A_GENERER', 'titre');

	foreach ($founds as $id_objet => $nb) {
		$l = singulier_ou_pluriel($nb, 'rechremp:1_occurence_dans', 'rechremp:nb_occurences_dans');
		$l .= ' <a href="'.generer_url_entite($id_objet, $objet).'">'.generer_info_entite($id_objet, $objet, RECHREMP_INFO_RESULTAT_A_GENERER).'</a>';
		$out .= "<li>$l";
		if (RECHREMP_CONTEXTE_NB_CHARS) {
			$out .= "<ul class='rechremp_liste_contextes'>";
			foreach($contextes[$id_objet] as $occurences) {
				$out .= "<li>
							<span class='rechremp_contexte'>".htmlentities($occurences[1]).'</span>'
							.htmlentities($search)
							."<span class='rechremp_contexte'>".htmlentities($occurences[2]).'</span>
						</li>';
			}
			$out .= "</ul>";
		}
		$out .= "</li>\n";
	}

	$out .= '</ul>';

	return $out;
}
