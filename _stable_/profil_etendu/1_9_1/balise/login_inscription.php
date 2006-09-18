<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');
include_spip('balise/login_public');
spip_connect();

function balise_LOGIN_INSCRIPTION ($p, $nom='LOGIN_INSCRIPTION') {
	return calculer_balise_dynamique($p, $nom, array('url'));
}

# retourner:
# 1. l'url collectee ci-dessus (args0) ou donnee en filtre (filtre0)
# 2. l'eventuel parametre de la balise (args1) fournie par
#    calculer_balise_dynamique, en l'occurence le #LOGIN courant si l'on
#    programme une <boucle(AUTEURS)>[(#LOGIN_INSCRIPTION{#LOGIN})]

function balise_LOGIN_INSCRIPTION_stat ($args, $filtres) {
	return array($filtres[0] ? $filtres[0] : $args[0], $args[1], $args[2]);
}

function balise_LOGIN_INSCRIPTION_dyn($url, $login) {

	if (!$url 		# pas d'url passee en filtre ou dans le contexte
	AND !$url = _request('url') # ni d'url passee par l'utilisateur
	)
		$url = str_replace('&amp;', '&', self());
	return login_explicite_inscription($login, $url);
}

function login_explicite_inscription($login, $cible) {
	global $auteur_session;

	$action = str_replace('&amp;', '&', self());
	if ($cible) {
		$cible = parametre_url($cible, 'var_erreur', '', '&');
		$cible = parametre_url($cible, 'var_login', '', '&');
	} else {
		if (ereg("[?&]url=([^&]*)", $action, $m))
			$cible = rawurldecode($m[1]);
		else
			$cible = self();//_DIR_RESTREINT ;
	}

	verifier_visiteur();

	// Si on est connecte, envoyer vers la destination
	// sauf si on y est deja
	if ($auteur_session AND
	($auteur_session['statut']=='0minirezo'
	OR $auteur_session['statut']=='1comite'
	OR $auteur_session['statut']=='6forum')) {
		if ($cible != $action) {
			if (!headers_sent() AND !$_GET['var_mode'])
				redirige_par_entete($cible);
			else {
				include_spip('inc/minipres');
				return '';//http_href($cible, _T('login_par_ici'));
			}
		} else
			return ''; # on est arrive on bon endroit, et logue'...
	}
	return login_pour_tous($login ? $login : _request('var_login'), $cible, $action);
}
?>