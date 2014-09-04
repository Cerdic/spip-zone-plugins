<?php

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

function inc_analyser_rss_commits_dist($url)
{
    include_spip('iterateur/data');
    include_spip('inc/distant');
    $recuperer_rss_commits = charger_fonction('recuperer_rss_commits', 'inc');
    // $convertir             = charger_fonction('xml_to_array', 'inc');

    $valeurs = array();
    $xml = false;

    $valeurs   = array();
    $page      = $recuperer_rss_commits($url);
    // $xml       = $convertir($page['content']);

    if (!is_null($page)) {
        // $page = preg_replace("/\<\?(.*)\?\>/", "", $page);
        // Transformer les <dc:creator> en faveur de <author>
        $page = preg_replace("/dc:creator\>/", "author>", $page['content']);
        // Transformer les <content:encoded> du rss de Git en faveur de <texte>
        $page = preg_replace("/content:encoded\>/", "texte>", $page);
        // Merci _Eric_ pour ce code.
        // var_dump($page);
        $xml = json_decode(json_encode(simplexml_load_string($page, null, LIBXML_NOCDATA)), true);
    }

    return $xml;
}
?>