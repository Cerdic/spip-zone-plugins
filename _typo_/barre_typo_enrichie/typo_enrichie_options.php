<?php
//
// Gerer les variables de personnalisation, qui peuvent provenir
// des fichiers d'appel, en verifiant qu'elles n'ont pas ete passees
// par le visiteur (sinon, pas de cache)
//
function tester_variable($var, $val){
	if (!isset($GLOBALS[$var]))
		$GLOBALS[$var] = $val;

	if (
		isset($_REQUEST[$var])
		AND $GLOBALS[$var] == $_REQUEST[$var]
	)
		die ("tester_variable: $var interdite");
}

tester_variable('debut_intertitre', '<h2>');
tester_variable('fin_intertitre', '</h2>');
?>