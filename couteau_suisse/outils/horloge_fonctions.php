<?php
// action speciale, si cs_dateserveur=oui
if(!isset($GLOBALS['cs_fonctions']) && isset($_GET['cs_dateserveur'])) {
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8"?>',
		'<curTime><O>', date("O"), '</O><U>', date("U"), '</U></curTime>';
	exit;
}

// La balise #HORLOGE{format,utc,id}
function balise_HORLOGE_dist($p) {
	$i = 1; $ar = array();
	while(($a = interprete_argument_balise($i++,$p)) != NULL) $ar[] = $a;
	$ar = count($ar)?join(".'|'.", $ar):"''";
	$p->code = "'<span class=\"jclock\" title=\"'.$ar.'\">99:99</span> '";
	$p->interdire_scripts = false;
	return $p;
}

?>