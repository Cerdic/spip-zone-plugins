<?php
/**
 * Utilisations de pipelines par HTML Minifier
 *
 * @plugin     HTML Minifier
 * @copyright  2018
 * @author     ladnet
 * @licence    GNU/GPL
 * @package    SPIP\Htmlminifier\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function htmlminifier_affichage_final($page) {   
	
	if (!$GLOBALS['html']) return $page;

	$config_hmtlminifier = lire_config('htmlminifier', array());

	if (empty($config_hmtlminifier)) {
		$config_hmtlminifier = HTMLMinifier::get_presets('super_safe');
	}

	$page = HTMLMinifier::process(
		$page,
		$config_hmtlminifier
	);  
	
    return $page;
}