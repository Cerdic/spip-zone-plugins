<?php
/**
 * Plugin Bolo pour Spip 2.0
 * Licence GPL (c) 2010
 * Auteur Cyril MARION - Ateliers CYM
 *
 */

// La balise BOLO
function balise_BOLO($p) {
	$type = "'latin'";
	$p->code = "((\$x = charger_fonction($type, 'bolo', true)) ? \$x() : '')"; // si fonction bolo_latin
	$p->interdire_scripts = false;
	return $p;
}

