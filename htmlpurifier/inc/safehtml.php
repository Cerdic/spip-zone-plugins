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

function inc_safehtml($t) {
	static $purifier;

	include_spip('inc/memoization');
	if (function_exists('cache_get')
	AND $a = cache_get($cle = 'safehtml:'.md5($t)))
		return $a;

	include_spip('lib/HTMLPurifier.standalone');
	if (!isset($purifier))
		$purifier = new HTMLPurifier();
		
	$config = HTMLPurifier_Config::createDefault();
	$config->set('Cache.SerializerPath', preg_replace(',/$,', '', realpath(_DIR_TMP)));

	// HTML Purifier prefere l'utf-8
	if ($GLOBALS['meta']['charset'] == 'utf-8')
		$t = $purifier->purify($t);
	else
		$t = unicode_to_charset($purifier->purify(charset2unicode($t)));

	if (function_exists('cache_get'))
		cache_set($cle, $t);

	return $t;

}

?>