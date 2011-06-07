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

include_once _DIR_PLUGIN_FORUM.'urls/generer_url_forum.php';

/**
 * Modifier l'ancre des urls de forum
 *
 * @param int $id_forum
 * @param string $args
 * @param string $ancre
 * @return string
 */
function urls_generer_url_forum($id_forum, $args='', $ancre='') {
	$url = urls_generer_url_forum_dist($id_forum, $args, $ancre);
	if ($url AND !$ancre)
		$url = ancre_url($url,"comment$id_forum");
	return $url;
}

?>