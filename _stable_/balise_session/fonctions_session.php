<?php

/***************************************************************************\
 *  Balise #SESSION pour SPIP                                              *
 *  http://www.spip-contrib.net/balise-session                             * 
 *                                                                         *
 *  Auteur : james.at.rezo.net (c) 2006                                    *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
\***************************************************************************/

include_spip('public/session_balises');

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