<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Inc\Facteur_convertir_styles_inline
 */

/**
 * Gerer l'appel de la fonction d'alerte fail quand un mail a echoue
 * La fonction est surchargeable car ca permet de personaliser/rerouter/...
 *
 * @param $function
 * @param $args
 * @param null $include
 * @return bool|mixed
 */
function inc_facteur_envoyer_alerte_fail_dist($function, $args, $include=null) {

	if (is_string($args)) {
		$args = unserialize($args);
		if ($args === false) {
			spip_log('sendFailAlert: arguments errones ' . json_encode([$function, $args, $include]), 'facteur' . _LOG_ERREUR);
			$args = array();
		}
	}

	if (!empty($include)) {
		if (substr($include, -1) == '/') { // c'est un chemin pour charger_fonction
			$f = charger_fonction($function, rtrim($include, '/'), false);
			if ($f) {
				$function = $f;
			}
		} else {
			include_spip($include);
		}
	}

	if (!function_exists($function)) {
		spip_log("sendFailAlert: fonction $function ($include) inexistante " . json_encode([$function, $args, $include]), 'facteur' . _LOG_ERREUR);
		return false;
	}

	$traceargs = md5(json_encode($args));
	spip_log("sendFailAlert: $function([$traceargs]) start", 'facteur');
	$res = call_user_func_array($function, $args);
	spip_log("sendFailAlert: $function([$traceargs]) end", 'facteur');

	return $res;
}