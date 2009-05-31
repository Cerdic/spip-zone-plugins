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


if (!defined("_ECRIRE_INC_VERSION")) return;

function tidy_appliquer ($texte) {
	if ($GLOBALS['html'] # verifie que la page avait l'entete text/html
		AND strlen($texte)
		AND (_request('var_fragment') === NULL)
		AND !headers_sent()) {
		include_spip('inc/tidy');
		return inc_tidy_dist ($texte);
	} else {
		return $texte;
	}
}


?>