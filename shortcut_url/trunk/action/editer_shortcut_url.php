<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/autoriser');

/**
 * Supprimer définitivement un URL
 * 
 * @param int $id_shortcut_url identifiant numérique du url
 * @return int|false 0 si réussite, false dans le cas ou l'url n'existe pas
 */
function shortcut_url_supprimer($id_shortcut_url){
	$valide = sql_getfetsel('id_shortcut_url','spip_shortcut_urls','id_shortcut_url='.intval($id_shortcut_url));
	if($valide && autoriser('supprimer','shortcut_url',$valide)){
		sql_delete("spip_shortcut_urls", "id_shortcut_url=".intval($id_shortcut_url));
		sql_delete("spip_auteurs_liens", "id_objet=".intval($id_shortcut_url));
		sql_delete("spip_urls", "id_objet=".intval($id_shortcut_url)." AND type=".sql_quote('shortcut_url'));
		$id_shortcut_url = 0;
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_shortcut_url/$id_shortcut_url'");
		return $id_shortcut_url;
	}
	return false;
}