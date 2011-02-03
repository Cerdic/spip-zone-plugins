<?php
/*
 * Plugin Encarts
 * (c) 2011 Camille Lafitte, Cyril Marion
 * Distribue sous licence GPL
 *
 */


function style_encarts($letexte) {
	$letexte = str_replace('<encart>', '<span class="encart interne">', $letexte);
	$letexte = str_replace('</encart>', '</span>', $letexte);
	return $letexte;
}

?>