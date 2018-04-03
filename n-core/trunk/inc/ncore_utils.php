<?php
/**
 * Ce fichier contient les utilitaires de N-Core.
 *
 * @package SPIP\NCORE\OUTILS
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Cherche une fonction donnée en se basant sur le service de stockage ou à défaut sur le plugin appelant.
 * Si ni le service de stockage ni le plugin ne fournissent la fonction demandée la chaîne vide est renvoyée.
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param bool   $fonction
 *        Nom de la fonction à chercher.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return string
 *        Nom complet de la fonction si trouvée ou chaine vide sinon.
 */
function ncore_chercher_service($plugin, $fonction, $stockage = '') {

	$fonction_trouvee = '';

	// Si le stockage n'est pas précisé on cherche la fonction dans le plugin appelant.
	if (!$stockage) {
		$stockage = $plugin;
	}

	// Eviter la réentrance si on demande explicitement le stockage N-Core
	if ($stockage != 'ncore') {
		include_spip("ncore/${stockage}");
		$fonction_trouvee = "${stockage}_${fonction}";
		if (!function_exists($fonction_trouvee)) {
			$fonction_trouvee = '';
		}
	}

	return $fonction_trouvee;
}
