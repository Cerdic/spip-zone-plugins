<?php

function show_tweets() {
	return '<div id="twitter"></div>';
}	

function balise_TWEETNSPIP($p) {
	    $p->code = "show_tweets()";
	    $p->interdire_scripts = false;
	    return $p;
}

?>
