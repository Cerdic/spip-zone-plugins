<?php
/**
 * Plugin Cookie bar pour Spip 3.0.
 *
 * @licence    GNU/GPL
 * @package    SPIP\Cookiebar\Pipelines
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Inserer la CSS de cookie bar dans le head public.
 *
 * @pipeline insert_head_css
 *
 * @param string $flux
 * 	Le contenu de la balise #INSERT_HEAD_CSS
 * @return string
 */
function cookiebar_insert_head_css($flux) {
	if (defined('_COOKIEBAR_CSS_NON')) {
		return $flux;
	}

	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/jquery.cookiebar.css').'" />';

	return $flux;
}

/**
 * Inserer le javascript de cookiebar.
 *
 * @pipeline insert_head
 *
 * @param string $flux
 * 	Le contenu de la balise #INSERT_HEAD
 * @return mixed
 */
function cookiebar_insert_head($flux) {
	include_spip('inc/filtres');
	include_spip('inc/config');
	
	// On fait un md5 de la config pour que le squelette change dès que la config change
	$signature = md5(serialize(lire_config('cookiebar')));
	
	$lang = (isset($GLOBALS['spip_lang']) ? $GLOBALS['spip_lang'] : 'fr');
	$js_cookiebar = produire_fond_statique('jquery.cookiebar.js', array('lang' => $lang, 'signature' => $signature));

	$flux .= "<script type='text/javascript' src='$js_cookiebar'></script>\n";
	$flux .= "<script type='text/javascript' src='".find_in_path('js/jquery.cookiebar.call.js')."'></script>";

	return $flux;
}
