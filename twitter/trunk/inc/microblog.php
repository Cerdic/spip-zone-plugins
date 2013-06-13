<?php
/*
 * Plugin spip|twitter
 * (c) 2009-2013
 *
 * envoyer et lire des messages de Twitter
 * distribue sous licence GNU/LGPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/twitter");

/**
 * Envoyer un microblog sur une des plateformes disponibles
 *
 * $status : ce qu'on veut ecrire
 * $user, $pass : identifiants
 * $service : quel service
 * $api : si on est vraiment desespere :-)
 * $tokens : dans le cas de oAuth chez twitter pouvoir passer des tokens différents
 * de ceux de la conf générale du site
 */
if (!function_exists('microblog')){
function microblog($status, $user=null, $pass=null, $service=null, $api=null, $tokens=null){
	return tweet($status, $tokens);
}
}
