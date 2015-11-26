<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Lister toutes les fonctions définies dans l'instance de SPIP.
 * Les fonctions fournies par les différents plugins actifs seront aussi prise en compte.
 *
 * @param null|string $prefixe
 *                             Préfixe de la fonction. Exemple : `autoriser`, `auth`, etc.
 *
 * @return array
 *               Si aucun préfixe, on listera toutes les fonctions.
 *               Si un préfixe est identifié, on listera toutes les fonctions avec ce préfixe.
 */
function lister_fonctions($prefixe = null)
{
	$fonctions = get_defined_functions();

	$fonctions_user = $fonctions['user'];
	sort($fonctions_user);

	foreach ($fonctions_user as $value) {
		if ($fonction = preg_split('/_/', $value, -1, PREG_SPLIT_NO_EMPTY)) {
			$fonctions_user[$fonction[0]][] = $value;
			if (($key = array_search($value, $fonctions_user)) !== false) {
				unset($fonctions_user[$key]);
			}
		}
	}
	ksort($fonctions_user);

	$resultat = $fonctions_user;

	if ($prefixe) {
		// On pourrait faire aussi un contrôle avec array_key_exists()
		// Mais ça risque de fausser le résultat attendu.
		$resultat = array($prefixe => $fonctions_user[$prefixe]);
	}

	return $resultat;
}

/**
 * Lister toutes les constantes définies dans l'instance de SPIP.
 * Les constantes fournies par les différents plugins actifs seront aussi prise en compte.
 *
 * @param null|string $prefixe
 *                             Préfixe de la constantes.
 *
 * @return array
 *               Si aucun préfixe, on listera toutes les constantes.
 *               Si un préfixe est identifié, on listera toutes les constantes avec ce préfixe.
 */
function lister_constantes($prefixe = null)
{
	$constantes = get_defined_constants(true);

	$constantes_user = $constantes['user'];

	foreach ($constantes_user as $key => $value) {
		if ($constante = preg_split('/_/', $key, -1, PREG_SPLIT_NO_EMPTY)) {
			if ($constante[0] == '_') {
				$constantes_user[$constante[1]][$key] = $value;
			} else {
				$constantes_user[$constante[0]][$key] = $value;
			}
			unset($constantes_user[$key]);
		}
	}

	ksort($constantes_user);

	$resultat = $constantes_user;

	if ($prefixe) {
		// On pourrait faire aussi un contrôle avec array_key_exists()
		// Mais ça risque de fausser le résultat attendu.
		$resultat = array($prefixe => $constantes_user[$prefixe]);
	}

	return $resultat;
}

/**
 * Afficher le nom et le chemin du fichier dans lequel
 * est défini la fonction passée en paramètre.
 *
 * @param null|string $fonction
 *
 * @return void|string
 */
function fonction_fichier($fonction = null)
{
	if ($fonction == null) {
		return;
	}
	// On prépare le pattern pour ne pas avoir le chemin depuis les méandres du serveur d'hébergement.
	$pattern_root = '/^'.preg_replace('/\//', '\/', $_SERVER['DOCUMENT_ROOT']).'/';

	// API offerte par PHP 5.
	$refFonction = new ReflectionFunction($fonction);

	// On enlève le chemin 'root' pour ne garder que le chemin à la "racine" de notre site.
	$filename = preg_replace($pattern_root, '', $refFonction->getFileName()).':'.$refFonction->getStartLine();

	return $filename;
}

/**
 * Lister les fichiers php dans un répertoire donné
 * ou, par défaut, dans tout le site.
 *
 * @param string $dir
 *                    Par défaut, le répertoire racine du site.
 *
 * @return array
 *               Tableau contenant le chemin vers chaque fichier php.
 */
function lister_fichiers_php($dir = _DIR_RACINE)
{
	global $list;

	// Si $dir est vide, on va chercher le répertoire
	// dans lequel se trouve le script pour référence.
	if (empty($dir)) {
		# on recherche le script sur lequel on est
		$script = end(explode('/', $_SERVER['PHP_SELF']));
		# Et on l'enlève de l'url pour donner une bonne valeur à $dir
		$dir = preg_replace('/'.$script.'/', '', $_SERVER['SCRIPT_FILENAME']);
	}

	if (!empty($dir) and is_dir($dir)) {
		$ffs = scandir($dir);
		$exclu = preg_match('/(tmp|local)/', $dir);
		if (substr($dir, -1) !== '/') {
			$dir = $dir.'/';
		}
		foreach ($ffs as $ff) {
			if ($ff[0] != '.' and $exclu == false) {
				if (strlen($ff) >= 5) {
					if (substr($ff, -4) == '.php') {
						$list[] = $dir.$ff;
					}
				}
				if (is_dir($dir.$ff)) {
					lister_fichiers_php($dir.$ff);
				}
			}
		}
	}

	return $list;
}

/**
 * Rechercher le nom de toutes les fonctions
 * dans le fichier php passé en paramètre.
 *
 * @param string $fichier
 *
 * @return array
 */
function lister_noms_fonctions($fichier)
{
	$liste_fonctions = array();

	if (is_file($fichier)) {
		$content = file_get_contents($fichier);
		preg_match_all("/(function)([\s|\t]+)(\w+)/", $content, $fonctions);
		foreach ($fonctions[3] as $fonction) {
			$liste_fonctions[] = array('fichier' => $fichier, 'fonction' => $fonction);
		}
	}

	return $liste_fonctions;
}

function lister_toutes_fonctions($prefixe = null)
{
	$fichiers_php = lister_fichiers_php();
	$fonctions_user = array();

	if (count($fichiers_php) > 0) {
		foreach ($fichiers_php as $fichier) {
			foreach (lister_noms_fonctions($fichier) as $value) {
				$fonctions_user[] = $value;
			}
		}
		@natcasesort($fonctions_user);
	}

	if (count($fonctions_user) > 0) {
		foreach ($fonctions_user as $value) {
			if ($fonction = preg_split('/_/', $value['fonction'], -1, PREG_SPLIT_NO_EMPTY)) {
				$fonctions_user[$fonction[0]][] = $value;
				if (($key = array_search($value, $fonctions_user)) !== false) {
					unset($fonctions_user[$key]);
				}
			}
		}
		ksort($fonctions_user);
	}

	$resultat = $fonctions_user;

	if ($prefixe and count($fonctions_user) > 0) {
		// On pourrait faire aussi un contrôle avec array_key_exists()
		// Mais ça risque de fausser le résultat attendu.
		$resultat = array($prefixe => $fonctions_user[$prefixe]);
	}

	return $resultat;
}
