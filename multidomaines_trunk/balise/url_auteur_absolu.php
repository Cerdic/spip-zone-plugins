<?php 

function balise_URL_AUTEUR_ABSOLU_dist($p) {
	
	include_spip('balise/url_');
	
	$p->code = '$GLOBALS["meta"]["multidomaines_url"].'.generer_generer_url('auteur', $p);

	
	$p->interdire_scripts = false;
	return $p;
}


?>