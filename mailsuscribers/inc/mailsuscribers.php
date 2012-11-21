<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Normaliser le nom d'une liste de diffusion
 *
 * @param string $liste
 * @param string $category
 * @return string
 */
function mailsuscribers_normaliser_nom_liste($liste='', $category="newsletter"){
	$category = trim(preg_replace(",\W,","",$category));

	if (!$liste)
		return $category;

	if (strpos($liste,"::")!==false){
		$liste = explode("::",$liste);
		return mailsuscribers_normaliser_nom_liste($liste[1],$liste[0]);
	}

	$liste = trim(preg_replace(",\W,","",$liste));
	$liste = "$category::$liste";
	return $liste;
}