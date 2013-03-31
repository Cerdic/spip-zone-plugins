<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


// Conversion d'un texte en utf-8
function entite2utf($sujet) {
	if (!$sujet) return;
	include_spip('inc/charsets');
	return unicode_to_utf_8(html_entity_decode(preg_replace('/&([lg]t;)/S', '&amp;\1', $sujet), ENT_NOQUOTES, 'utf-8'));
}
?>