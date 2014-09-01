<?php

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

function inc_analyser_rss_commits_dist($url)
{
    include_spip('iterateur/data');
    include_spip('inc/distant');
    $valeurs = array();
    $xml = false;
    $page = recuperer_page($url);


    if (!is_null($page)) {
        // $page = preg_replace("/\<\?(.*)\?\>/", "", $page);
        // Transformer les <dc:creator> en faveur de <author>
        $page = preg_replace("/dc:creator\>/", "author>", $page);
        // Transformer les <content:encoded> du rss de Git en faveur de <texte>
        $page = preg_replace("/content:encoded\>/", "texte>", $page);
        // Merci _Eric_ pour ce code.
        $xml = json_decode(json_encode(simplexml_load_string($page, null, LIBXML_NOCDATA)), true);
    }

    return $xml;
}
?>