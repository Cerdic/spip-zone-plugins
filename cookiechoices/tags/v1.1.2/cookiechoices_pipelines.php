<?php
/**
 * Plugin Cookiechoices pour Spip 3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Inserer le javascript de cookiechoices
 *
 * @param $flux
 * @return mixed
 */
function cookiechoices_affichage_final($page){
	include_spip('inc/filtres');
	$lang = extraire_attribut($page, 'lang');
	if (!$lang) {
		$lang = $GLOBALS["spip_lang"];
	}
	$js_cookiechoices = produire_fond_statique("cookiechoices_call.js", array('lang' => $lang));

	$script =
		"<script type='text/javascript' src='".find_in_path('js/cookiechoices.js')."'></script>\n"
		. "<script type='text/javascript' src='$js_cookiechoices'></script>\n";

	$pos_body = strpos($page, '</body>');
	if ($pos_body > 0) {// pour placer le script uniquement sur les pages HTML
		return substr_replace($page, $script, $pos_body, 0);
	}

	return $page;
}

