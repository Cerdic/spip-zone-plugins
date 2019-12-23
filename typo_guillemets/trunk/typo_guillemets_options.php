<?php
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

if (!function_exists('inc_lien')) {
	// pour les liens, on transforme les guill « » des titres en guill ??
	function inc_lien() {

		if (!function_exists('inc_lien_dist')){
			include_spip('inc/lien');
		}
		// recuperer le resultat normal
		$args = func_get_args();
		$typo = call_user_func_array('inc_lien_dist', $args);

		// si on n'avait pas precise de titre, changer les guill du titre auto
		if ($args[1] === '') {
			$typo = preg_replace(',&#171;(&nbsp;)?(.*?)(&nbsp;)?&#187;,S', '&#8220;\2&#8221;', $typo);
		}

		// et hop
		return $typo;
	}
}
