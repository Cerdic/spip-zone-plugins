<?php
/**
 * Fonctions utiles au plugin Commits de projet
 *
 * @plugin     Commits de projet
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Commits\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('base/abstract_sql');
include_spip('inc/analyser_rss_commits');
include_spip('dev_fonctions');

function lister_rss_commits ()
{
    $rss_items = array();
    $projet_rss = sql_allfetsel('versioning_rss,id_projet', 'spip_projets', "versioning_rss IS NOT NULL");

    $analyser_rss_commits = charger_fonction('analyser_rss_commits', 'inc');
    if (count($projet_rss) >0) {
        foreach ($projet_rss as $key_rss => $value_rss) {
            $contenu_rss = $analyser_rss_commits($value_rss["versioning_rss"]);
            if (count($contenu_rss) > 0) {
                foreach ($contenu_rss['channel'][0] as $key => $value) {
                    if (preg_match("/^item/", $key)) {
                        $rss_items[] = $value[0];
                    }
                }
            }
            foreach ($rss_items as $key_item => $value_item) {
                foreach ($value_item as $key => $value) {
                    // il n'y a que le contenu de l'index 0 qui nous intéresse.
                    $rss_items[$key_item][$key] = $value[0];
                }
                $rss_items[$key_item]['date_creation'] = strftime(
                    "%Y-%m-%d %H:%M:%S",
                    strtotime($value_item['pubDate'][0])
                );
                unset($rss_items[$key_item]['pubDate']);
                $rss_items[$key_item]['descriptif'] = $value_item['description'][0];
                unset($rss_items[$key_item]['description']);
                $rss_items[$key_item]['titre'] = $value_item['title'][0];
                unset($rss_items[$key_item]['title']);
                $rss_items[$key_item]['url_revision'] = $value_item['link'][0];
                unset($rss_items[$key_item]['link']);
                // Ne pas oublier de mettre l'id_projet auquel il se réfère.
                $rss_items[$key_item]['id_projet'] = $value_rss['id_projet'];
            }
        } // end foreach $projet_rss
    }

    return $rss_items;
}
?>