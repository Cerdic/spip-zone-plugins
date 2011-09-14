<?php

/**
 * Permet de lister les articles du site.
 * Redirection
 * 
 * @see http://www.quesaco.org/plugin-spip-articles-lister
 * @author Christian Paulus
 * @license GPLv3
 */
// $LastChangedBy$
// $LastChangedDate$

if(!defined('_ECRIRE_INC_VERSION')) { return; }

if(autoriser('webmestre'))
{
	header('Location: '
		   . htmlspecialchars(sinon($GLOBALS['meta']['adresse_site'],'.'))
		   . '/?page=articles_lister'
		   );
	exit;
}