<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_titre_parametrages' => 'Paramétrages',

	// H
	'htmlminifier_titre' => 'HTML Minifier',

	// T
	'titre_page_configurer_htmlminifier' => 'Configurer le plugin HTML Minifier',

	// General		
	'clean_html_comments_label_case' => 'Removes HTML comments',
	'clean_html_comments_explication' => 'Removes all HTML comments (except those that contain conditional comments',

	'show_signature_label_case' => 'Show signature in source,',
	'show_signature_explication' => 'Adds a comment at the end of the minified output saying the source was minified by HTML Minifier',

		
	'compression_mode_explication' => 'Compression Mode',
	'compression_mode_none' => 'do not remove whitespace',
	'compression_mode_all_whitespace_not_newlines' => 'remove extra whitespace but keep newlines',
	'compression_mode_all_whitespace' => 'removeall whitespaces',
	
	// CSS				
	'clean_css_comments_label_case' => 'Removes comments in &lt;style&gt; tags',
	'clean_css_comments_explication' => 'Removes all comments inside &lt;style&gt; blocks',

	'shift_link_tags_to_head_label_case' => 'Shift all &lt;link&gt; stylesheet tags into &lt;head&gt;',
	'shift_link_tags_to_head_explication' => 'Convenience option for pages that are built dynamically in the backend. Moves any &lt;link rel="stylesheet"&gt; tags outside of &lt;head&gt; into &lt;head&gt;',

	'shift_style_tags_to_head_label_case' => 'Shift all &lt;style&gt; tags into &lt;head&gt;',
	'shift_style_tags_to_head_explication' => 'Convenience option for pages that are built dynamically in the backend. Moves any &lt;style&gt; tags outside of &lt;head&gt; into &lt;head&gt;',

	'combine_style_tags_label_case' => 'Combines CSS in &lt;style&gt; tags',
	'combine_style_tags_explication' => 'Combines all of the on-page CSS inside separate &lt;style&gt; tags into one. Will not combine tags with different media attributes.',

	// Javascript		
	'clean_js_comments_label_case' => 'Remove Javascript comments,',
	'clean_js_comments_explication' => 'Removes all comments inside &lt;script&gt; blocks.',

	'remove_comments_with_cdata_tags_label_case' => 'Remove comments with CDATA tags,',
	'remove_comments_with_cdata_tags_explication' => 'Only works if clean_js_comments is true. In XHTML content inside &lt;script&gt; tags are sometimes encapsulated in commented CDATA blocks to make them XML-compatible. By default these commented CDATA tags are preserved for document integrity.',

	'compression_ignored_tags_label_case' => 'Compression ignored tags',
	'compression_ignored_tags_explication' => 'Array containing a list of all the tags (in lowercase) to skip compression for. This is to prevent HTML Minifier from messing with the behaviour of tags like &lt;textarea&gt; and &lt;pre&gt;, where removed whitespace can affect displayed content.',

	'shift_script_tags_to_bottom_label_case' => 'Shift all &lt;script&gt; tags to the end of &lt;body&gt;,',
	'shift_script_tags_to_bottom_explication' => 'Convenience option for pages that are built dynamically in the backend. Moves all &lt;script&gt; tags to the end of &lt;body&gt;.',

	'combine_javascript_in_script_tags_label_case' => 'Combine Javascript in &lt;script&gt; tags,',
	'combine_javascript_in_script_tags_explication' => 'Only works if shift_script_tags_to_bottom is true. Combines all of the on-page Javascript inside separate &lt;style&gt; tags into one. Might break certain pages.',

	'section_general' => 'Général options',
	'section_css' => 'CSS Options',
	'section_javascript' => 'Javascript Options',

	'config_super_safe' => 'super safe',
	'config_safe' => 'safe',
	'config_moderate' => 'moderate',
	'config_fully_optimised' => 'fully optimised',
	'config_label' => 'Apply compression and optimization options',
);
