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
	$format = sinon(interprete_argument_balise(1,$p), "'H:i:s'");
	$utc = sinon(interprete_argument_balise(2,$p), "''");
	$id = sinon(interprete_argument_balise(3,$p), "'0'");
	$p->code = "'<div class=\"jclock'.(intval($id)?' jclock'.$id:'').'\" title=\"'.$utc.'|'.$format.'\"></div>'";
	$p->type = 'php';  
	return $p;
}

?>