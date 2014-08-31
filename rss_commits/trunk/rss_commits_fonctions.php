<?php
/**
 * Fonctions utiles au plugin Commits de projet
 *
 * @plugin     Commits de projet
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\RSSCommits\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('base/abstract_sql');

function lister_rss_commits ()
{
    $rss_items = array();
    $items = array();
    $projet_rss = sql_allfetsel('versioning_rss,id_projet', 'spip_projets', "versioning_rss IS NOT NULL");

    $analyser_rss_commits = charger_fonction('analyser_rss_commits', 'inc');
    if (count($projet_rss) >0) {
        foreach ($projet_rss as $key_rss => $value_rss) {
            $contenu_rss = $analyser_rss_commits($value_rss["versioning_rss"]);
            if (count($contenu_rss) > 0) {
                foreach ($contenu_rss['channel']['item'] as $key => $value) {
                        $items[$key]['titre'] = $value['title'];
                        $items[$key]['descriptif'] = $value['description'];
                        $items[$key]['texte'] = $value['texte'];
                        $value['author'] = preg_replace("/\</", "&lt;", $value['author']);
                        $value['author'] = preg_replace("/\>/", "&gt;", $value['author']);
                        $items[$key]['auteur'] = $value['author'];
                        $items[$key]['url_revision'] = $value['link'];
                        $items[$key]['guid'] = $value['guid'];
                        $items[$key]['id_projet'] = $value_rss['id_projet'];
                        $items[$key]['date_creation'] = strftime(
                            "%Y-%m-%d %H:%M:%S",
                            strtotime($value['pubDate'])
                        );
                }
                $rss_items = array_merge($rss_items, $items);
                // echo "<pre>";
                // var_dump($rss_items);
                // echo "</pre>";
            }
        } // end foreach $projet_rss
    }

    return $rss_items;
}

?>