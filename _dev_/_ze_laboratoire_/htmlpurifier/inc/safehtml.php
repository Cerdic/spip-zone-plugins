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


function inc_safehtml_dist($t) {
	static $purifier;

	include_spip('library/HTMLPurifier.auto');
	if (!isset($purifier))
		$purifier = new HTMLPurifier();

	// HTML Purifier prefere l'utf-8
	if ($GLOBALS['meta']['charset'] == 'utf-8')
		return $purifier->purify($t);
	else
		return unicode_to_charset($purifier->purify(charset2unicode($t)));
}

?>