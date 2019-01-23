<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2016                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Gestion du cache et des invalidations de cache
 *
 * @package SPIP\Core\Cache
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
// *************************************************************************
// Contient la surcharge de : suivre_invalideur

include_spip('inc/cachelab_invalideur');

// *************************************************************************
// Le reste du fichier est identique à l'original

include_spip('base/serial');


/** Estime la taille moyenne d'un fichier cache, pour ne pas les regarder (10ko) */
if (!defined('_TAILLE_MOYENNE_FICHIER_CACHE')) {
	define('_TAILLE_MOYENNE_FICHIER_CACHE', 1024 * 10);
}
/**
 * Si un fichier n'a pas été servi (fileatime) depuis plus d'une heure, on se sent
 * en droit de l'éliminer
 */
if (!defined('_AGE_CACHE_ATIME')) {
	define('_AGE_CACHE_ATIME', 3600);
}

/**
 * Calcul le nombre de fichiers à la racine d'un répertoire ainsi qu'une
 * approximation de la taille du répertoire
 *
 * On ne calcule que la racine pour pour aller vite.
 *
 * @param string $dir Chemin du répertoire
 * @param string $nb_estim_taille Nombre de fichiers maximum pour estimer la taille
 * @return bool|array
 *
 *     - false si le répertoire ne peut pas être ouvert
 *     - array(nombre de fichiers, approximation de la taille en octet) sinon
 **/
function nombre_de_fichiers_repertoire($dir, $nb_estim_taille = 20) {
	$taille = 0; // mesurer la taille de N fichiers au hasard dans le repertoire
	$nb = $nb_estim_taille;
	if (!$h = @opendir($dir)) {
		return false;
	}
	$total = 0;
	while (($fichier = @readdir($h)) !== false) {
		if ($fichier[0] != '.' and !is_dir("$dir/$fichier")) {
			$total++;
			if ($nb and rand(1, 10) == 1) {
				$taille += filesize("$dir/$fichier");
				$nb--;
			}
		}
	}
	closedir($h);

	return array($total, $taille ? $taille / ($nb_estim_taille - $nb) : _TAILLE_MOYENNE_FICHIER_CACHE);
}


/**
 * Évalue approximativement la taille du cache
 *
 * Pour de gros volumes, impossible d'ouvrir chaque fichier,
 * on y va donc à l'estime !
 *
 * @return int Taille approximative en octets
 **/
function taille_du_cache() {
	# check dirs until we reach > 500 files
	$t = 0;
	$n = 0;
	$time = isset($GLOBALS['meta']['cache_mark']) ? $GLOBALS['meta']['cache_mark'] : 0;
	for ($i=0; $i < 256; $i++) {
		$dir = _DIR_CACHE.sprintf('%02s', dechex($i));
		if (@is_dir($dir) AND is_readable($dir) AND $d = @opendir($dir)) {
			while (($f = readdir($d)) !== false) {
				if (preg_match(',^[[0-9a-f]+\.cache$,S', $f) AND $a = stat("$dir/$f")) {
					$n++;
					if ($a['mtime'] >= $time) {
						if ($a['blocks'] > 0) {
							$t += 512*$a['blocks'];
						} else {
							$t += $a['size'];
						}
					}
				}
			}
		}
		if ($n > 500) {
			return intval(256*$t/(1+$i));
		}
	}
	return $t;
}

/**
 * Purge un répertoire de ses fichiers
 *
 * Utilisée entre autres pour vider le cache depuis l'espace privé
 *
 * @uses supprimer_fichier()
 *
 * @param string $dir
 *     Chemin du répertoire à purger
 * @param array $options
 *     Tableau des options. Peut être :
 *
 *     - atime : timestamp pour ne supprimer que les fichiers antérieurs
 *       à cette date (via fileatime)
 *     - mtime : timestamp pour ne supprimer que les fichiers antérieurs
 *       à cette date (via filemtime)
 *     - limit : nombre maximum de suppressions
 * @return int
 *     Nombre de fichiers supprimés
 **/
function purger_repertoire($dir, $options = array()) {
	$handle = @opendir($dir);
	if (!$handle) {
		return;
	}

	$total = 0;

	while (($fichier = @readdir($handle)) !== false) {
		// Eviter ".", "..", ".htaccess", ".svn" etc.
		if ($fichier[0] == '.') {
			continue;
		}
		$chemin = "$dir/$fichier";
		if (is_file($chemin)) {
			if ((!isset($options['atime']) or (@fileatime($chemin) < $options['atime']))
				and (!isset($options['mtime']) or (@filemtime($chemin) < $options['mtime']))
			) {
				supprimer_fichier($chemin);
				$total++;
			}
		} else {
			if (is_dir($chemin)) {
				$opts = $options;
				if (isset($otpions['limit'])) {
					$otps['limit'] = $otpions['limit'] - $total;
				}
				$total += purger_repertoire($chemin, $opts);
				if (isset($options['subdir']) && $options['subdir']) {
					spip_unlink($chemin);
				}
			}
		}

		if (isset($options['limit']) and $total >= $options['limit']) {
			break;
		}
	}
	closedir($handle);

	return $total;
}


//
// Methode : on prend un des sous-repertoires de CACHE/
// on considere qu'il fait 1/16e de la taille du cache
// et on le ratiboise en supprimant les fichiers qui n'ont pas
// ete sollicites dans l'heure qui vient de s'ecouler
//
// http://code.spip.net/@appliquer_quota_cache
function appliquer_quota_cache() {

	$encore = false;

	$tour_quota_cache = intval(1 + $GLOBALS['meta']['tour_quota_cache']) % 16;
	ecrire_meta('tour_quota_cache', $tour_quota_cache);

	$l = dechex($tour_quota_cache);
	$dir = sous_repertoire(_DIR_CACHE, $l);
	list($nombre, $taille) = nombre_de_fichiers_repertoire($dir);
	$total_cache = $taille * $nombre;
	if ($total_cache)	// Ajout / core car c'est vide avec la memoization
		spip_log("Taille du CACHE estimee ($l): "
			. (intval(16 * $total_cache / (1024 * 1024 / 10)) / 10) . " Mo", "invalideur");

	// Nombre max de fichiers a supprimer
	if ($GLOBALS['quota_cache'] > 0
		and $taille > 0
	) {
		$trop = $total_cache - ($GLOBALS['quota_cache'] / 16) * 1024 * 1024;
		$trop = 3 * intval($trop / $taille);
		if ($trop > 0) {
			$n = purger_repertoire($dir,
				array(
					'atime' => time() - _AGE_CACHE_ATIME,
					'limit' => $trop,
					'subdir' => true // supprimer les vieux sous repertoire de session (avant [15851])
				)
			);
			spip_log("$dir : $n/$trop caches supprimes [taille moyenne $taille]", "invalideur");
			$total_cache = intval(max(0, (16 * $total_cache) - $n * $taille) / (1024 * 1024) * 10) / 10;
			spip_log("cache restant estime : $total_cache Mo, ratio " . $total_cache / $GLOBALS['quota_cache'], "invalideur");

			// redemander la main pour eviter que le cache ne gonfle trop
			// mais pas si on ne peut pas purger car les fichiers sont trops recents
			if (
				$total_cache / $GLOBALS['quota_cache'] > 1.5
				and $n * 50 > $trop
			) {
				$encore = true;
				spip_log("Il faut encore purger", "invalideur");
			}
		}
	}

	return $encore;
}

//
// Destruction des fichiers caches invalides
//

// Securite : est sur que c'est un cache
// http://code.spip.net/@retire_cache
function retire_cache($cache) {

	if (preg_match(
		"|^([0-9a-f]/)?([0-9]+/)?[0-9a-f]+\.cache(\.gz)?$|i",
		$cache)) {
		// supprimer le fichier (de facon propre)
		supprimer_fichier(_DIR_CACHE . $cache);
	} else {
		spip_log("Nom de fichier cache incorrect : $cache");
	}
}

#######################################################################
##
## Ci-dessous les fonctions qui restent appellees dans le core
## pour pouvoir brancher le plugin invalideur ;
## mais ici elles ne font plus rien
##

if  (!defined('LOG_INVALIDATION_CORE'))
	define ('LOG_INVALIDATION_CORE', false);

// Supprimer les caches marques "x"
// A priori dans cette version la fonction ne sera pas appelee, car
// la meta est toujours false ; mais evitons un bug si elle est appellee
// http://code.spip.net/@retire_caches
function retire_caches($chemin = '') {
	if (isset($GLOBALS['meta']['invalider_caches'])) {
		effacer_meta('invalider_caches');
		if  (LOG_INVALIDATION_CORE)
			spip_log ("retire_caches($chemin = '') ne devrait pas être appelé car ['meta']['invalider_caches'] devrait être false", "invalideur_core_retire_caches_BUG");
	} # concurrence
	if  (LOG_INVALIDATION_CORE)
		spip_log ("retire_caches($chemin = '')", "invalideur_core_retire_caches");
}


// Fonction permettant au compilo de calculer les invalideurs d'une page
// http://code.spip.net/@calcul_invalideurs
function calcul_invalideurs($corps, $primary, &$boucles, $id_boucle) {
	return $corps;
}

// Cette fonction permet de supprimer tous les invalideurs
// Elle ne touche pas aux fichiers cache eux memes ; elle est
// invoquee quand on vide tout le cache en bloc (action/purger)
//
// http://code.spip.net/@supprime_invalideurs
function supprime_invalideurs() { 
	if  (LOG_INVALIDATION_CORE)
		spip_log ("supprime_invalideurs()", "invalideur_core");
}

// les invalideurs sont en général de la forme "objet/id_objet"
// http://code.spip.net/@insere_invalideur
// JAMAIS appelé par le noyau, 
// ÉTAIT un hook post maj_invalideurs appelé avec $page['invalideurs'] et $fichier
function insere_invalideur($inval, $fichier) { 
	if  (LOG_INVALIDATION_CORE)
		spip_log ("insere_invalideur($inval, $fichier)", "invalideur_core");
}

//
// Marquer les fichiers caches invalides comme etant a supprimer
//
// http://code.spip.net/@applique_invalideur
// JAMAIS appelé par le noyau
// ÉTAIT un hook post suivre_invalideur avec la liste calculée des fichiers caches à invalider
function applique_invalideur($depart) { 
	if  (LOG_INVALIDATION_CORE)
		spip_log ("applique_invalideur($depart)", "invalideur_core");
}
