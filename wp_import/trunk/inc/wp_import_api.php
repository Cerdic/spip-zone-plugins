<?php
/**
 * Fonctions utiles au plugin Import Wordpress
 *
 * @plugin     wp_import
 * @copyright  2018
 * @author     Guillaume Wauquier / Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\wp_import\Inc
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function wp_import_trim(&$value, $key, $char = null) {
	$value = trim($value, $char);
}

function wp_import_twp($texte) {
	$texte = str_replace('<![CDATA[', '', $texte);
	$texte = str_replace(']]>', '', $texte);
	return $texte;
}

function html_to_spip($texte, $tab_document = array()) {
	$texte = str_replace('<strong></strong>', '', $texte);
	$texte = str_replace(array('<strong>', '</strong>'), array(' {{', '}} '), $texte);
	$texte = str_replace(array('<em>', '</em>'), array(' {', '} '), $texte);
	$texte = str_replace(array('<p>', '</p>'), "\n", $texte);
	$texte = str_replace(array('</span>'), "", $texte);
	$texte = preg_replace('/<p[^>]*>/i', "\n", $texte);
	$texte = preg_replace('/<span[^>]*>/i', "\n", $texte);
	$texte = preg_replace('`<style type="text/css"></style>`i', "", $texte);
	$texte = preg_replace('`{{(<img[0-9]{1,8}>)}}`i', "\\1", $texte);
	$texte = inserer_balise_image($texte, $tab_document);

	return $texte;
}


function inserer_balise_image($texte, $tab_document) {

	$patterns = '`<a[^>]*><img .* src="(.*)/wp-content/uploads/([^"]*)(-[0-9]{1,4}x?[0-9]{1,4})(\.[a-z]{3})"[^>]*></a>`i';
	$texte = preg_replace_callback($patterns, function ($matches) use ($tab_document) {
		if (isset($tab_document[urldecode($matches[2]) . $matches[4]])) {
			return "<img" . intval($tab_document[urldecode($matches[2]) . $matches[4]]) . ">";
		}
		return $matches[0];
	}, $texte);

	$patterns = '`<a[^>]*><img .* src="(.*)/wp-content/uploads/([^"]*)(\.[a-z]{3})"[^>]*></a>`i';
	$texte = preg_replace_callback($patterns, function ($matches) use ($tab_document) {
		if (isset($tab_document[urldecode($matches[2])])) {
			return "<img" . intval($tab_document[urldecode($matches[2])]) . ">";
		}
		return $matches[0];
	}, $texte);

	$patterns = '`<img .* src="(.*)/wp-content/uploads/([^"]*)(-[0-9]{1,4}x?[0-9]{1,4})(\.[a-z]{3})"[^>]*>`i';
	$texte = preg_replace_callback($patterns, function ($matches) use ($tab_document) {
		if (isset($tab_document[urldecode($matches[2]) . $matches[4]])) {
			return "<img" . intval($tab_document[urldecode($matches[2]) . $matches[4]]) . ">";
		}
		return $matches[0];
	}, $texte);

	$patterns = '`<img .* src="(.*)/wp-content/uploads/([^"]*)(\.[a-z]{3})"[^>]*>`i';
	$texte = preg_replace_callback($patterns, function ($matches) use ($tab_document) {
		if (isset($tab_document[urldecode($matches[2])])) {
			return "<img" . intval($tab_document[urldecode($matches[2])]) . ">";
		}
		return $matches[0];
	}, $texte);

	return $texte;
}


function preg_array_key_exists($pattern, $array) {
	$keys = array_keys($array);
	return preg_grep($pattern, $keys);
}

function wp_import_donne_nom_cat($cat) {
	preg_match('/nicename=\"([^\"]*)\"/', $cat, $matches);
	return $matches[1];
}
