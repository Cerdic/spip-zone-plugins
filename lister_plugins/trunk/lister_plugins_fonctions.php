<?php
/**
 * Fichier de fonctions pour le plugin lister_plugins
 *
 * @plugin     Lister les plugins nécessaires à votre site
 * @copyright  2013-2017
 * @author     Teddy
 * @licence    GNU/GPL
 * @package    SPIP/ListerPlugins/Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!function_exists('nom_machine')) {
	/**
	 * Cette fonction permet de transformer en nom machine, soit en enlevant tous les accents, toutes les ponctuations. Les espaces sont remplacés par le séparateur `_`.
	 *
	 * @param string $subject    Texte à transformer en nom machine
	 * @param string $separateur Par défaut, un underscore `_`.
	 *
	 * @return string
	 */
	function nom_machine($subject, $separateur = '_') {
		include_spip('inc/charsets');
		$nom_tmp = trim($subject); // On enlève les espaces indésirables
		$nom_tmp = translitteration($nom_tmp); // On enlève les accents et cie
		$nom_tmp = preg_replace(",(/|[[:punct:][:space:]]+),u", $separateur,
			$nom_tmp); // On enlève les espaces et les slashs
		$nom_tmp = preg_replace("/(" . $separateur . "+)/", $separateur, $nom_tmp); // pas de double underscores
		if (preg_match("/" . $separateur . "$/", $nom_tmp)) {
			$nom_tmp = trim($nom_tmp, $separateur); // On ne doit pas terminer par le séparateur
		}
		$nom_tmp = preg_replace("/'/", $separateur, $nom_tmp); // pas d'apostrophes
		$nom_tmp = strtolower($nom_tmp); // On met en minuscules

		return $nom_tmp;
	}
}
