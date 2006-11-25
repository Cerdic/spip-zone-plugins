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
	if(function_exists('balise_ENV'))
		return balise_ENV($p, '$GLOBALS["auteur_session"]');
	else
		return balise_ENV_dist($p, '$GLOBALS["auteur_session"]');
}

//un filtre un peu fun pour la doc en ligne
function liste_ul_li($tableau) {
	$texte = '';
	if(!empty($tableau))
		foreach($tableau as $titre => $valeur) {
			$texte .= "\t<li>".$titre."</li>\n";
		}
	return $texte ?
		"<ul>\n".$texte."</ul>\n"
		: '';
}

?>