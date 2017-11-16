<?php
/**
 * Utilisations de pipelines par HTML Minifier
 *
 * @plugin     HTML Minifier
 * @copyright  2017
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
		$config_hmtlminifier = array(
			'clean_html_comments' => true,
			'show_signature' => false,
			'compression_mode' => 'all_whitespace_not_newlines',

			'clean_css_comments' => true,
			'shift_link_tags_to_head' => false,
			'shift_style_tags_to_head' => false,
			'combine_style_tags' => false,

			'clean_js_comments' => true,
			'remove_comments_with_cdata_tags' => false,
			'compression_ignore_script_tags' => true,
			'shift_script_tags_to_bottom' => false,
			'combine_javascript_in_script_tags' => false
		);
	}

	include_spip('inc/HTMLMinifier');

	$page = HTMLMinifier::process(
		$page,
		$config_hmtlminifier
	);  
	
    return $page;
}