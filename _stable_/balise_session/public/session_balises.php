<?php

/***************************************************************************\
 *  Balise #SESSION pour SPIP                                              *
 *  http://www.spip-contrib.net/balise-session                             * 
 *                                                                         *
 *  Auteur : james.at.rezo.net et compagnie (c) 2006-2007                  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
\***************************************************************************/

function balise_SESSION_dist($p) {
	$GLOBALS['squelettes_a_sessions'][$p->descr['nom']] = true;
	if(function_exists('balise_ENV'))
		return balise_ENV($p, '$GLOBALS["auteur_session"]');
	else
		return balise_ENV_dist($p, '$GLOBALS["auteur_session"]');
}

?>