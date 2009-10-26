<?php

// on regarde s'il y a un logo, sinon un gravatar, et on renvoie le tout
// pour ca il faut modifier un peu le code produit par #LOGO_*, pour introduire
// notre fonction de recherche de logo
function balise_LOGO_AUTEUR($p) {
	$balise_logo_ = charger_fonction('logo_', 'balise');
	$_email1 = champ_sql('email', $p);
	$_email2 = champ_sql('email_ad', $p);
	$_email3 = champ_sql('email_auteur', $p);
	$_email4 = champ_sql('address', $p);

	$_id = champ_sql('id_auteur', $p);
	$_emailsql = "sql_getfetsel('email','spip_auteurs','id_auteur='.intval($_id))";

	$p = $balise_logo_($p);

	$p->code = str_replace('calcule_logo(', 'calcule_logo_ou_gravatar(sinon('.$_email1.',sinon('.$_email2.', sinon('.$_email3.', sinon('.$_email4.','.$_emailsql.')))), ', $p->code);

	return $p;
}

?>