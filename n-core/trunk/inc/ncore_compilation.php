<?php
/**
 * Ce fichier contient les fonctions du service N-Core pour les noisettes.
 *
 * Chaque fonction, soit aiguille vers une fonction "homonyme" propre au service si elle existe,
 * soit déroule sa propre implémentation pour le service appelant.
 * Ainsi, les services externes peuvent, si elle leur convient, utiliser l'implémentation proposée par N-Core
 * sans coder la moindre fonction.
 *
 * @package SPIP\NCORE\NOISETTE\SERVICE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_NCORE_CONFIG_AJAX_DEFAUT')) {
	/**
	 * Valeur par défaut de la configuration AJAX des noisettes.
	 * Pour N-Core, le défaut est `true`.
	 *
	 */
	define('_NCORE_CONFIG_AJAX_DEFAUT', true);
}
if (!defined('_NCORE_DYNAMIQUE_DEFAUT')) {
	/**
	 * Valeur par défaut de l'indicateur d'inclusion dynamique des noisettes.
	 * Pour N-Core, le défaut est `false`.
	 */
	define('_NCORE_DYNAMIQUE_DEFAUT', false);
}


/**
 * Renvoie la configuration par défaut de l'ajax à appliquer pour les noisettes.
 * Cette information est utilisée si la description YAML d'une noisette ne contient pas de tag ajax
 * ou contient un tag ajax à `defaut`.
 *
 * Le service N-Core considère que toute noisette est par défaut insérée en ajax.
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * 		Cet argument n'est utilisé que si la fonction N-Core est appelée.
 *
 * @return bool
 * 		`true` si par défaut une noisette est insérée en ajax, `false` sinon.
 */
function ncore_noisette_config_ajax($service) {

	// On recherche au préalable si il existe une fonction propre au service et si oui on l'appelle.
	include_spip("ncore/${service}");
	$config_ajax = "${service}_noisette_config_ajax";
	if (function_exists($config_ajax)) {
		$defaut_ajax = $config_ajax();
	} else {
		// Le service ne propose pas de fonction propre, on utilise celle de N-Core.
		$defaut_ajax = _NCORE_CONFIG_AJAX_DEFAUT;
	}

	return $defaut_ajax;
}
