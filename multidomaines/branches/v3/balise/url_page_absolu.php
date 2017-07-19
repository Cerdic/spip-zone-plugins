<?php 

function balise_URL_PAGE_ABSOLU_dist($p) {
	$p->code = interprete_argument_balise(1,$p);
	$args = interprete_argument_balise(2,$p);
	if ($args != "''" && $args!==NULL)
	    $p->code .= ','.$args;
	else
		$p->code .= ',""';
	$p->code = 'lire_config("multidomaines/editer_url").generer_url_public(' . $p->code .',false,true)';
	$p->interdire_scripts = true;
	return $p;
}
?>
