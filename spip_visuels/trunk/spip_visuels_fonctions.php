<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function parser_liste_types($text){
	// $text = str_replace('\n', 'toto', $text);
	// $text = nl2br($text);
	$lignes = explode(PHP_EOL, $text);
	return $lignes;
}