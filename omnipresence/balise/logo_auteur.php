<?php

// on regarde s'il y a un logo, sinon un avatar Jabber, et on renvoie le tout
// pour ca il faut modifier un peu le code produit par #LOGO_*, pour introduire
// notre fonction de recherche de logo
function balise_LOGO_AUTEUR($p) {
	include_spip('inc/omnipresence');
	$balise_logo_ = charger_fonction('logo_', 'balise');
	$_jid = champ_sql(CHAMP_JID, $p);
	$host = champ_sql(CHAMP_SERVEUR_OMNIPRESENCE, $p);
	$p = $balise_logo_($p);
	$p->code = str_replace('calcule_logo(', "calcule_logo_ou_avatar($_jid, $host, ", $p->code);
	return $p;
}

?>
