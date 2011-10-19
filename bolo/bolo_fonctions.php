<?php
/**
 * Plugin Bolo pour Spip 2.0
 * Licence GPL (c) 2010
 * Auteur Cyril MARION - Ateliers CYM
 *
 */

// La balise BOLO
function balise_BOLO($p) {
	include_spip('inc/bolo_latin');
	$p->code = "$bolo";
	$p->interdire_scripts = false;
	return $p;   
}

?>