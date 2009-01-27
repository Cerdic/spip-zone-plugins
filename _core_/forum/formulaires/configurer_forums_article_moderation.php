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

if (!defined("_ECRIRE_INC_VERSION")) return;

// Recuperer le reglage des forums publics de l'article x
// http://doc.spip.org/@get_forums_publics
function get_forums_publics($id_article=0) {

	if ($id_article) {
		$obj = sql_fetsel("accepter_forum", "spip_articles", "id_article=$id_article");

		if ($obj) return $obj['accepter_forum'];
	} else { // dans ce contexte, inutile
		return substr($GLOBALS['meta']["forums_publics"],0,3);
	}
	return $GLOBALS['meta']["forums_publics"];
}

/**
 * Charger
 *
 * @param int $id_article
 * @return array
 */
function formulaires_configurer_forums_article_moderation_charger_dist($id_article){
	if (!autoriser('modererforum', 'article', $id_article))
		return false;

	include_spip('inc/presentation');
	include_spip('base/abstract_sql');
	$nb_forums = sql_countsel("spip_forum", "id_article=".intval($id_article)." AND statut IN ('publie', 'off', 'prop', 'spam')");

	return array(
		'accepter_forum' => get_forums_publics($id_article),
		'_suivi_forums' => $nb_forums?_T('icone_suivi_forum', array('nb_forums' => $nb_forums)):"",
	);
	
}

/**
 * Traiter
 *
 * @param int $id_article
 * @return array
 */
function formulaires_configurer_forums_article_moderation_traiter_dist($id_article){
	include_spip('inc/autoriser');
	if (autoriser('modererforum', 'article', $id_article)){
		$statut = _request('accepter_forum');
		include_spip('base/abstract_sql');
		sql_updateq("spip_articles", array("accepter_forum" => $statut), "id_article=". intval($id_article));
		
		if ($statut == 'abo') {
			ecrire_meta('accepter_visiteurs', 'oui');
		}
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_forum/a$id_article'");
	}
		
	return array('message_ok'=>_T('config_info_enregistree'));
}

?>