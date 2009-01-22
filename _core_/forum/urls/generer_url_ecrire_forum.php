<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

// http://doc.spip.org/@generer_url_ecrire_forum
function urls_generer_url_ecrire_forum_dist($id, $args='', $ancre='', $statut='', $connect='') {
	$a = "id_forum=" . intval($id);
	if (!$statut) {
		$statut = sql_getfetsel('statut', 'spip_forum', $a,'','','','',$connect);
	}
	$h = ($statut == 'publie' OR $connect)
	?  generer_url_entite_absolue($id, 'forum', $args, $ancre, $connect)
	: (generer_url_ecrire('controle_forum', "debut_id_forum=$id" . ($args ? "&$args" : ''))
		. ($ancre ? "#$ancre" : ''));
	return $h;
}