<?php

// on regarde s'il y a un logo, sinon un gravatar, et on renvoie le tout
// pour ca il faut modifier un peu le code produit par #LOGO_*, pour introduire
// notre fonction de recherche de logo
function balise_LOGO_AUTEUR($p) {
	$balise_logo_ = charger_fonction('logo_', 'balise');
	$_email1 = champ_sql('email', $p);
	$_email2 = champ_sql('email_ad', $p);
	$_email3 = champ_sql('email_auteur', $p);

	$p = $balise_logo_($p);

	$p->code = str_replace('calcule_logo(', '!include_spip("balise/logo_auteur")?"":calcule_logo_ou_gravatar(sinon('.$_email1.',sinon('.$_email2.','.$_email3.')), ', $p->code);

	return $p;
}

// notre fonction de recherche de logo
function calcule_logo_ou_gravatar($email) {
	$a = func_get_args();
	$email = array_shift($a);

	// la fonction normale
	$c = call_user_func_array('calcule_logo',$a);

	// si elle repond pas, on va chercher le gravatar
	if (!$c[0])
		$c[0] = gravatar($email);

	return $c;
}

function gravatar($email) {
	if (!strlen($email)
	OR !email_valide($email))
		return '';

	$md5_email = md5(strtolower($email));
	$gravatar_cache = sous_repertoire(_DIR_VAR, 'cache-gravatar')
		.$md5_email.'.jpg';

	if (!file_exists($gravatar_cache)
	OR time()-3600*24 > filemtime($gravatar_cache)) {
		if ($gravatar
		= recuperer_page('http://www.gravatar.com/avatar/'.$md5_email)
		// ceci est le hash du gravatar bleu moche par defaut : on l'ignore
		AND md5($gravatar) !== '2bd0ca9726695502d06e2b11bf4ed555') {
			spip_log('gravatar ok pour '.$email);
			ecrire_fichier($gravatar_cache, $gravatar);
		} else
			ecrire_fichier($gravatar_cache, '');
	}

	// On verifie si le gravatar existe en controlant la taille du fichier
	if (filesize($gravatar_cache))
		return $gravatar_cache;
	else
		return '';
}