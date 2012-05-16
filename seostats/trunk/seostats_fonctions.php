<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('lib/class.seostats');


/*
 * Fonction générique pour appeler les différentes méthodes de la class SEOstats
 */
function seostats($url,$fonction) {
	try
	{
		$url = new SEOstats($url);
		return 	 $url->$fonction();
	}
	catch (SEOstatsException $e) 
	{
		return $e->getMessage();
	}
	
	return '';
	
}

?>