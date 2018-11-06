<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2017                                                *
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

  include_spip('inc/utils');
  if ( html5_permis() ){
    include_spip('lib/html5/HTMLPurifier.standalone');
    include_spip('HTMLPurifier.extended');  
  } else {
    include_spip('lib/html4/HTMLPurifier.standalone');
    include_spip('HTMLPurifier.extended');  
  }
  
	$config = HTMLPurifier_Config::createDefault();

	$config->set('Attr.EnableID', true);
	$config->set('HTML.SafeIframe', true);	
	$config->set('URI.SafeIframeRegexp', "%^http[s]?://[a-z0-9\.]*".$_SERVER['HTTP_HOST']."%iS" );	
	
	$config->set('HTML.TidyLevel', 'none');
	$config->set('Cache.SerializerPath', preg_replace(',/$,', '', realpath(_DIR_TMP)));
	$config->set('Attr.AllowedFrameTargets', array('_blank'));
	$config->set('Attr.AllowedRel', 'facebox,nofollow,print,external');
  
	$config->set('URI.AllowedSchemes', array ('http' => true, 'https' => true, 'mailto' => true, 'ftp' => true, 'nntp' => true, 'news' => true, 'tel' => true, 'tcp'=>true, 'udp'=>true, 'ssh'=>true,));
	
	$html = $config->getHTMLDefinition(true);
	$html->manager->addModule('Forms');
	$html->manager->registeredModules["Forms"]->safe = true;
  
	if (!isset($purifier))
		$purifier = new HTMLPurifier($config);
		
    
	// HTML Purifier prefere l'utf-8
	if ($GLOBALS['meta']['charset'] == 'utf-8')
		$t = $purifier->purify($t);
	else
		$t = unicode_to_charset($purifier->purify(charset2unicode($t)));

	if (function_exists('cache_set'))
		cache_set($cle, $t);

	return $t;

}
