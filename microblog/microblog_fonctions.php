<?php
/*
 * Plugin spip|microblog
 * (c) Fil 2009-2010
 *
 * envoyer des micromessages depuis SPIP vers twitter ou laconica
 * distribue sous licence GNU/LGPL
 *
 */

function generer_url_microblog($id, $entite, $args, $ancre, $public, $type){
	include_spip('inc/filtres_mini');
	$config = unserialize($GLOBALS['meta']['microblog']);

	if (!$public
	 OR $entite!=='article'
	 OR !$config['short_url'])
		return url_absolue(generer_url_entite($id, $entite, $args, $ancre, $public, $type));
	else
		return $GLOBALS['meta']['adresse_site'].'/'.$id;
}


?>