<?php
/**
 * Interface d'administration des Forums
 *
 * (c) 2009 - Cedric Morin
 * Distribue sous licence GPL3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip('inc/forum'); // pour boutons_controle_forum 

// http://code.spip.net/@exec_articles_forum_dist
function exec_articles_forum_dist()
{
	$controle_forum = charger_fonction('controle_forum','exec');
	$controle_forum();
}

?>