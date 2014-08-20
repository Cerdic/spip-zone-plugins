<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function parser_liste_types($text){
	$lignes = explode(PHP_EOL, $text);
	return $lignes;
}