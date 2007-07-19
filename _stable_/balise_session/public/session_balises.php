<?php

/***************************************************************************\
 *  Balise #SESSION pour SPIP                                              *
 *  http://www.spip-contrib.net/balise-session                             * 
 *                                                                         *
 *  Auteur : james.at.rezo.net (c) 2006                                    *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
\***************************************************************************/

function balise_SESSION_dist($p) {
	$p->entetes['X-Session'] = 'oui';

	if(function_exists('balise_ENV'))
		return balise_ENV($p, '$GLOBALS["auteur_session"]');
	else
		return balise_ENV_dist($p, '$GLOBALS["auteur_session"]');
}

//
// #CACHE
// avec le systeme de gestion d'entetes de ce plugin
function balise_CACHE($p) {
	$duree = valeur_numerique($p->param[0][1][0]->texte);

	// noter la duree du cache dans un entete proprietaire
	$p->entetes['X-Spip-Cache'] = $duree;

	// Remplir le header Cache-Control
	// cas #CACHE{0}
	if ($duree == 0) {
		$p->entetes['Cache-Control'] = 'no-store, no-cache, must-revalidate';
		$p->entetes['Pragma'] = 'no-cache';
	}

	// cas #CACHE{360, cache-client}
	if (isset($p->param[0][2])) {
		$second = ($p->param[0][2][0]->texte);
		if ($second == 'cache-client'
		AND $duree > 0)
			$p->entetes['Cache-Control'] = 'max-age='.$duree;
	}

	$p->code = "''";
	$p->interdire_scripts = false;
	return $p;
}

?>