<?php
/**
 * Fonctions utiles au plugin Double Click
 *
 * @plugin     Double Click
 * @copyright  2014
 * @author     Camille Sauvage
 * @licence    GNU/GPL
 * @package    SPIP\Doubleclick\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/config');

/**
 * Une securite qui nous protege contre :
 * - les doubles validations de forums (derapages humains ou des brouteurs)
 * - les abus visant a mettre des forums malgre nous sur un article (??)
 * On installe un fichier temporaire dans _DIR_TMP/spool (et pas _DIR_CACHE
 * afin de ne pas bugguer quand on vide le cache)
 * Le lock est leve au moment de l'appel à la fonction traiter()
 *
 * @return int
 */
function doubleclick_cree_lock() {
	// on vérifie l'existence du répertoire de spool
	if (file_exists(_DIR_TMP . '/spool') == false) {
		mkdir(_DIR_TMP . '/spool');
	}
	
	// on calcule un hash complet dépendant du temps + aléa
	while (($hash = md5(lire_config('doubleclick/secret') . ($alea = time()+@mt_rand())))
		AND @file_exists($f = _DIR_TMP . "/spool/doubleclick_$hash.lck"));
	
	// on crée le fichier
	spip_touch($f);
	
	// et maintenant on purge les locks de forums ouverts depuis > 4 h
	$file_dates = array();
	
	if ($dh = @opendir(_DIR_TMP . '/spool')){
		while (($file = @readdir($dh))!==false){
			if (preg_match('/^doubleclick_\S+?\.lck$/', $file)) {
				$cpt_lock++;
				$file_dates[$file] = @filemtime(_DIR_TMP . "spool/$file");
				if ((time() - $file_dates[$file]) > 4*3600) {
					spip_unlink(_DIR_TMP . "spool/$file");
					unset($file_dates[$file]);
				}
			}
		}
	}
	
	// on supprime les anciens fichiers surnuméraires
	$max_spool = intval(lire_config('doubleclick/taille_spool'));
	if ($max_spool == 0) $max_spool = 500;
	
	// Est-ce qu'on a encore trop de fichiers ?
	if (count($file_dates) > $max_spool) {
		asort($file_dates, SORT_NUMERIC);
		
		foreach ($file_dates as $file => $date) {
			spip_unlink(_DIR_TMP . "spool/$file");
			unset($file_dates[$file]);
			if (count($file_dates) <= $max_spool) break;
		}
	}
	
	return $alea;
}

function doubleclick_supprime_lock($alea) {
	$hash = md5(lire_config('doubleclick/secret') . $alea);
	$file = _DIR_TMP . "spool/doubleclick_$hash.lck";
	spip_unlink($file);
}

function doubleclick_existe_lock($alea) {
	$hash = md5(lire_config('doubleclick/secret') . $alea);
	$file = _DIR_TMP . "spool/doubleclick_$hash.lck";

	return @file_exists($file);
}

?>