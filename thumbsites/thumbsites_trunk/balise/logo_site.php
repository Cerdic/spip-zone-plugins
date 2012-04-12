<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// on regarde s'il y a un logo, sinon un thumbshot, et on renvoie le tout
// pour ca il faut modifier un peu le code produit par #LOGO_*, pour introduire
// notre fonction de recherche de logo
function balise_LOGO_SITE($p) {
	$balise_logo_ = charger_fonction('logo_', 'balise');
	$_url = champ_sql('url_site', $p);

	$p = $balise_logo_($p);

	# spip 2.0
	if (preg_match(',calcule_logo,', $p->code)) {
		$p->code = str_replace('calcule_logo(', 'calcule_logo_ou_thumbshot('.$_url.', ', $p->code);
	}
	# spip 2.1
	else {
		$p->code = '(($a = '
		. $p->code
		.') ? $a : thumbshot_img('.$_url.'))';
	}
	return $p;
}

?>