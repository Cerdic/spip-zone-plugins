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

// http://doc.spip.org/@exec_articles_forum_dist
function exec_articles_forum_dist()
{
	$controle_forum = charger_fonction('controle_forum','exec');
	$controle_forum();
}

?>