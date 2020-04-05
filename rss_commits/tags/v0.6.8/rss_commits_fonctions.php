<?php
/**
 * Fonctions utiles au plugin Commits de projet
 *
 * @plugin     Commits de projet
 * @copyright  2014-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\RSSCommits\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('base/abstract_sql');
include_spip('inc/filtres');

function lister_rss_commits($id_projet = null, $force = true) {
	$rss_items = array();
	$items = array();
	$where = "versioning_rss !=''";
	if (!is_null($id_projet) and $id_projet = intval($id_projet)) {
		$where = "versioning_rss !='' AND id_projet=$id_projet";
	}
	if ($force == true) {
		$where = "versioning_rss !=''";
	}

	$projet_rss = sql_allfetsel('versioning_rss,id_projet', 'spip_projets', $where);
	// spip_log(print_r($projet_rss, true), 'rss_commits');

	$analyser_rss_commits = charger_fonction('analyser_rss_commits', 'inc');
	if (count($projet_rss) > 0) {
		foreach ($projet_rss as $key_rss => $value_rss) {
			$contenu_rss = $analyser_rss_commits($value_rss["versioning_rss"]);
			// spip_log(print_r($contenu_rss, true), 'rss_commits');
			if (count($contenu_rss) > 0) {
				if (isset($contenu_rss['channel'])) {
					// On est sur un flux pour un SVN…
					foreach ($contenu_rss['channel']['item'] as $key => $value) {
						// spip_log(print_r($value, true), 'rss_commits');
						$items[$key]['titre'] = echapper_tags($value['title']);
						$items[$key]['descriptif'] = $value['description'];
						$items[$key]['texte'] = isset($value['texte']) ? trim($value['texte']) : '';
						$items[$key]['auteur'] = echapper_tags($value['author']);
						$items[$key]['url_revision'] = $value['link'];
						$items[$key]['guid'] = $value['guid'];
						$items[$key]['id_projet'] = $value_rss['id_projet'];
						$items[$key]['date_creation'] = strftime(
							"%Y-%m-%d %H:%M:%S",
							strtotime($value['pubDate'])
						);
					}
				} elseif (isset($contenu_rss['feed'])) {
					// Ce flux est pour des git-like
					foreach ($contenu_rss['feed']['entry'] as $key => $value) {
						// spip_log(print_r($value, true), 'rss_commits');
						$items[$key]['titre'] = echapper_tags($value['title']);
						$items[$key]['descriptif'] = '';
						$items[$key]['texte'] = $value['summary'];
						$items[$key]['auteur'] = isset($value['author']['email']) ? echapper_tags($value['author']['email']) : echapper_tags($value['author']['name']);
						$items[$key]['url_revision'] = $value['link']['@attributes']['href'];
						$items[$key]['guid'] = $value['id'];
						$items[$key]['id_projet'] = $value_rss['id_projet'];
						$items[$key]['date_creation'] = strftime(
							"%Y-%m-%d %H:%M:%S",
							strtotime($value['updated'])
						);
					}
				}
				$rss_items = array_merge($rss_items, $items);
			}
		} // end foreach $projet_rss
	}

	return $rss_items;
}

