<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/* Cette fonction est équivalente à json_encode(str, JSON_UNESCAPED_UNICODE)
 * et fonctionne pour PHP 5.3.
 * En effet, JSON_UNESCAPED_UNICODE n'est introduit qu'en PHP 5.4.0.
 *
 * référence : http://stackoverflow.com/a/2934602
 * */
function json_encode_utf8($str) {
	$str = json_encode($str);
	$str = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
	return $str;
}
function replace_unicode_escape_sequence($match) {
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}

?>
