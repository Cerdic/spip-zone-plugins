<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Lister tous les squelettes nommés dans le cache du site.
 *
 * @return array
 */
function lister_cache_skel() {
	$timestart = microtime(true);
	$squelettes = array();
	$fichiers = find_all_in_path(_DIR_RACINE . 'tmp/cache/skel/', 'html_*');
	if (is_array($fichiers) and count($fichiers)) {
		foreach ($fichiers as $nom => $chemin) {
			$fichier_cache = file_get_contents($chemin);
			preg_match(",\* Squelette : (.*)\n,i", $fichier_cache, $matches);
			$squelettes[] = $matches[1];
		}
	}
	$squelettes = array_unique($squelettes);
	natcasesort($squelettes);
	$squelettes = array_map('effacer_chemin_relatif', $squelettes);
	$squelettes = array_values($squelettes);

	$timeend = microtime(true);
	$time = $timeend-$timestart;
	$page_load_time = number_format($time, 3);

	return $squelettes;
}

/**
 * Lister tous les squelettes utilisés durant la consultation des différentes pages.
 *
 * @uses lister_cache_skel()
 * @return array
 */
function lister_squelettes() {
	$timestart = microtime(true);
	// Récupérer les pages nomées dans le cache
	$squelettes = lister_cache_skel();
	// Si la globale `decomposer` n'existe pas, on la crée.
	if (!isset($GLOBALS['decomposer'])) {
		$GLOBALS['decomposer'] = array();
	}
	// Une petite fusion avec les squelettes sortis du cache du site.
	$squelettes = array_merge($GLOBALS['decomposer'], $squelettes);
	$squelettes = array_unique($squelettes);
	natcasesort($squelettes);
	$squelettes = array_map('effacer_chemin_relatif', $squelettes);
	$squelettes = array_values($squelettes);

	$timeend = microtime(true);
	$time = $timeend-$timestart;
	$page_load_time = number_format($time, 3);

	return $squelettes;
}

/**
 * Lister tous les éléments appelés par la balise `#CHEMIN` dans les différents squelettes mise en cache.
 *
 * @uses lister_squelettes()
 * @return array
 */
function lister_chemin() {
	$chemin = array();
	$squelettes = lister_squelettes();
	if (is_array($squelettes) and count($squelettes)) {
		foreach ($squelettes as $squelette) {
			$url_squelette = find_in_path($squelette);
			// Récupérer la liste des éléments que les templates récupère par la balise #CHEMIN
			preg_match_all(',\#CHEMIN{(.*?)},', file_get_contents($url_squelette), $matches);
			$results = $matches[1];
			$results = array_filter($results);
			if (is_array($results) and count($results)) {
				foreach ($results as $result) {
					// On recherche les fichiers existants
					$chemin[] = find_in_path($result);
				}
			}
		}
	}
	$chemin = array_unique($chemin);
	$chemin = array_filter($chemin);
	$chemin = array_map('effacer_chemin_relatif', $chemin);
	natcasesort($chemin);
	$chemin = array_values($chemin);

	return $chemin;
}

/**
 * Lister tous les appels fait par #URL_PAGE dans les squelettes.
 *
 * @uses lister_squelettes()
 * @return array
 */
function lister_url_pages() {
	$url_page = array();
	$squelettes = lister_squelettes();
	if (is_array($squelettes) and count($squelettes)) {
		foreach ($squelettes as $squelette) {
			$url_squelette = find_in_path($squelette);
			// Récupérer la liste des éléments que les templates récupère par la balise #CHEMIN
			preg_match_all(',\#URL_PAGE{(.*?)},', file_get_contents($url_squelette), $matches);
			$results = $matches[1];
			$results = array_filter($results);
			if (is_array($results) and count($results)) {
				foreach ($results as $result) {
					// On recherche les fichiers existants
					$page = explode(',', $result);
					// On recherche les templates html
					$url_page[] = find_in_path($page[0] . '.html');
				}
			}
		}
	}
	$url_page = array_unique($url_page);
	$url_page = array_filter($url_page);
	$url_page = array_map('effacer_chemin_relatif', $url_page);
	natcasesort($url_page);
	$url_page = array_values($url_page);

	return $url_page;
}

/**
 * Cette fonction permet de fusionner le contenu de la fonction `lister_squelettes` et `lister_url_pages`
 *
 * @uses lister_squelettes()
 * @uses lister_url_pages()
 * @return array
 */
function lister_all_squelettes() {
	$squelettes = lister_squelettes();
	$url_pages = lister_url_pages();
	$all_squelettes = array_merge($squelettes, $url_pages);

	$all_squelettes = array_unique($all_squelettes);
	$all_squelettes = array_filter($all_squelettes);
	natcasesort($all_squelettes);
	$all_squelettes = array_values($all_squelettes);

	return $all_squelettes;
}

/**
 * Permet d'enlever les `../` en début d'appel d'un chemin vers un squelette
 *
 * @param string $element
 *
 * @return mixed
 */
function effacer_chemin_relatif($element) {
	return preg_replace(",\.\./,", '', $element);
}

function lister_themes_perso() {
	$all_squelettes = lister_all_squelettes();
	$chemins = lister_chemin();
	$all_themes = array_merge($all_squelettes, $chemins);

	if (is_array($all_themes) and count($all_themes)) {
		foreach ($all_themes as $index => $fichier) {
			if (!preg_match(",^squelettes/,", $fichier)) {
				unset($all_themes[$index]);
			}
		}
	}

	$all_themes = array_unique($all_themes);
	$all_themes = array_filter($all_themes);
	natcasesort($all_themes);
	$all_themes = array_values($all_themes);

	return $all_themes;
}

function lister_themes_repertoire() {
	$results = lister_contenu_repertoire(_DIR_RACINE . "squelettes");
	$results = array_map('effacer_chemin_relatif', $results);

	return $results;
}

function lister_contenu_repertoire($dir, &$results = array()) {

	$handle = opendir($dir);

	while ($file = readdir($handle)) {

		if ($file != "." && $file != "..") {

			if (!is_file($dir . DIRECTORY_SEPARATOR . $file)) {
				lister_contenu_repertoire($dir . DIRECTORY_SEPARATOR . $file, $results);
			} elseif (!preg_match(',\.DS_Store$,', $file)) {
				$results[] = $dir . DIRECTORY_SEPARATOR . $file;
			}
		}
	}

	return $results;

}

function filtre_decomposer_themes_controle_dist($tableau) {
	$texte = '';
	$liste_contenu_repertoire = lister_themes_perso();
	$modele = 'decomposer_themes_controle';
	$img_ok = find_in_path('ok-16.png', 'images/');
	$img_ko = find_in_path('ko-16.png', 'images/');
	if (is_array($tableau)) {
		$texte .= '<ul class="spip">';
		foreach ($tableau as $k => $v) {
			$res = recuperer_fond('modeles/' . $modele,
				array_merge(array('cle' => $k), array('valeur' => $v),
					array('validation' => (in_array($v, $liste_contenu_repertoire) ? $img_ok : $img_ko)))
			);
			$texte .= $res;
		}
		$texte .= '</ul>';
	}

	return $texte;
}