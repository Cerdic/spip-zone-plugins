<?php
/**
 * Ce fichier contient les fonctions du service N-Core pour les noisettes.
 *
 * Chaque fonction, soit aiguille vers une fonction "homonyme" propre au service si elle existe,
 * soit déroule sa propre implémentation pour le service appelant.
 * Ainsi, les services externes peuvent, si elle leur convient, utiliser l'implémentation proposée par N-Core
 * sans coder la moindre fonction.
 *
 * @package SPIP\NCORE\API\COMPILATION
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

/**
 * Détermine si la noisette spécifiée doit être incluse en AJAX ou pas.
 *
 * @api
 * @filtre
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *      La fonction utilisera les fonctions de lecture de la configuration globale de l'ajax et de
 *      lecture du paramètre ajax de la noisette, spécifiques au service, ou à défaut, celles fournies par N-Core.
 * @param string	$noisette
 * 		Identifiant de la $noisette.
 *
 * @return bool
 * 		`true` si la noisette doit être ajaxée, `false` sinon.
 */
function ncore_noisette_est_ajax($service, $noisette) {

	// On indexe le tableau des indicateurs ajax par le service appelant en cas d'appel sur le même hit
	// par deux services différents.
	static $est_ajax = array();

	if (!isset($est_ajax[$service][$noisette])) {
		// On détermine le cache en fonction du service, puis son existence et son contenu.
		include_spip('inc/ncore_cache');
		$est_ajax[$service] = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_AJAX);

		// On doit recalculer le cache.
		if (!$est_ajax[$service]
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On charge l'API de N-Core.
			// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
			include_spip("ncore/ncore");

			// On détermine la valeur par défaut de l'ajax des noisettes pour le service appelant.
			$defaut_ajax = ncore_noisette_config_ajax($service);

			// On repertorie la configuration ajax de toutes les noisettes disponibles et on compare
			// avec la valeur par défaut configurée pour le service appelant.
			if ($ajax_noisettes = ncore_noisette_lister($service,'ajax')) {
				foreach ($ajax_noisettes as $_noisette => $_ajax) {
					$est_ajax[$service][$_noisette] = ($_ajax == 'defaut')
						? $defaut_ajax
						: ($_ajax == 'non' ? false : true);
				}
			}

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant la valeur ajax par défaut afin de toujours renvoyer
			// quelque chose.
			if (!isset($est_ajax[$service][$noisette])) {
				$est_ajax[$service][$noisette] = $defaut_ajax;
			}

			// In fine, on met à jour le cache
			cache_ecrire($service, _NCORE_NOMCACHE_NOISETTE_AJAX, $est_ajax[$service]);
		}
	}

	return $est_ajax[$service][$noisette];
}


/**
 * Détermine si la noisette spécifiée doit être incluse dynamiquement ou pas.
 *
 * @api
 * @filtre
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *      La fonction utilisera les fonctions de lecture du paramètre d'inclusion de la noisette,
 *      spécifique au service, ou à défaut, celle fournie par N-Core.
 * @param string	$noisette
 * 		Identifiant de la $noisette.
 *
 * @return bool
 * 		`true` si la noisette doit être incluse dynamiquement, `false` sinon.
 */
function ncore_noisette_est_dynamique($service, $noisette) {

	// On indexe le tableau des indicateurs ajax par le service appelant en cas d'appel sur le même hit
	// par deux services différents.
	static $est_dynamique = array();

	if (!isset($est_dynamique[$service][$noisette])) {
		// On détermine le cache en fonction du service, puis son existence et son contenu.
		include_spip('inc/ncore_cache');
		$est_dynamique[$service] = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_INCLUSION);

		// On doit recalculer le cache.
		if (!$est_dynamique[$service]
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On charge l'API de N-Core.
			// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
			include_spip("ncore/ncore");

			// On repertorie la configuration d'inclusion de toutes les noisettes disponibles et on
			// détermine si celle-ci est dynamique ou pas.
			if ($inclusion_noisettes = ncore_noisette_lister($service,'inclusion')) {
				foreach ($inclusion_noisettes as $_noisette => $_inclusion) {
					$est_dynamique[$service][$_noisette] = ($_inclusion == 'dynamique') ? true : false;
				}
			}

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant en positionnant l'inclusion dynamique à false.
			if (!isset($est_dynamique[$service][$noisette])) {
				$est_dynamique[$service][$noisette] = _NCORE_DYNAMIQUE_DEFAUT;
			}

			// In fine, on met à jour le cache
			cache_ecrire($service, _NCORE_NOMCACHE_NOISETTE_INCLUSION, $est_dynamique[$service]);
		}
	}

	return $est_dynamique[$service][$noisette];
}


/**
 * Renvoie le contexte de la noisette sous la forme d'un tableau éventuellement vide.
 *
 * @api
 * @filtre
 *
 * @param string	$service
 *      Le service permet de distinguer l'appelant qui peut-être un plugin comme le noiZetier ou
 *      un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 *      La fonction utilisera les fonctions de lecture du contexte de la noisette, spécifique au service,
 *      ou à défaut, celle fournie par N-Core.
 * @param string	$noisette
 * 		Identifiant de la $noisette.
 *
 * @return array
 * 		Le tableau éventuellement vide des éléments de contexte de la noisette.
 */
function ncore_noisette_contexte($service, $noisette) {

	// On indexe le tableau des indicateurs ajax par le service appelant en cas d'appel sur le même hit
	// par deux services différents.
	static $contexte = array();

	if (!isset($contexte[$service][$noisette])) {
		// On détermine le cache en fonction du service, puis son existence et son contenu.
		include_spip('inc/ncore_cache');
		$contexte[$service] = cache_lire($service, _NCORE_NOMCACHE_NOISETTE_CONTEXTE);

		// On doit recalculer le cache.
		if (!$contexte[$service]
		or (_request('var_mode') == 'recalcul')
		or (defined('_NO_CACHE') and (_NO_CACHE != 0))) {
			// On charge l'API de N-Core.
			// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
			include_spip("ncore/ncore");

			// On repertorie la configuration du contexte de toutes les noisettes disponibles et on
			// le renvoie le résultat tel quel.
			$contexte[$service] = ncore_noisette_lister($service, 'contexte');
			$contexte[$service] = unserialize($contexte[$service]);

			// On vérifie que la noisette demandée est bien dans la liste.
			// Si non, on la rajoute en utilisant en positionnant le contexte à tableau vide.
			if (!isset($contexte[$service][$noisette])) {
				$contexte[$service][$noisette] = array();
			}

			// In fine, on met à jour le cache
			cache_ecrire($service, _NCORE_NOMCACHE_NOISETTE_CONTEXTE, $contexte[$service]);
		}
	}

	return $contexte[$service][$noisette];
}
