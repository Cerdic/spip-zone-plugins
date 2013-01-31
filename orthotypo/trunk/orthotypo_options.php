<?php
/**
 * Plugin Ortho-Typographie
 * (c) 2013 cedric
 * Licence GNU/GPL
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!function_exists('inc_lien')){
/**
 * pour les liens, on transforme les guill francais &laquo; &raquo; des titres en guill de second niveau
 *
 * @return mixed
 */
function inc_lien() {
	static $config;
	if (is_null($config))
		$config = lire_config("orthotypo/");

	if (!function_exists('inc_lien_dist')){
		include_spip('inc/lien');
	}
	// recuperer le resultat normal
	$args = func_get_args();
	$typo = call_user_func_array('inc_lien_dist', $args);

	if (!isset($config['guillemets']) OR $config['guillemets']){
		// si on n'avait pas precise de titre, changer les guill du titre auto
		if ($args[1] === '' AND strpos($typo,"&#171;")!==false)
			$typo = preg_replace(',&#171;(&nbsp;)?(.*?)(&nbsp;)?&#187;,S', '&#8220;\2&#8221;', $typo);
	}

	// et hop
	return $typo;
}
}