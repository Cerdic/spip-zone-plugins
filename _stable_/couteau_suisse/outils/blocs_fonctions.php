<?php
	
/* Trois balises pour creer des blocs depliables : 
	#BLOC_TITRE
	Mon titre
	#BLOC_DEBUT
	Mon texte depliable
	#BLOC_FIN
	
	Les trois balises doivent se suivre et sont imperatives
*/

function balise_BLOC_TITRE($p) {
	$p->code = "'<div class=\"cs_blocs\"><h4 class=\"blocs_titre blocs_replie\"><a href=\"#\">'";
	return $p;
}

function balise_BLOC_DEBUT($p) {
	$p->code = "'</a></h4><div class=\"blocs_invisible\">'";
	return $p;
}

function balise_BLOC_FIN($p) {
	$p->code = "'</div></div>'";
	return $p;
}

?>