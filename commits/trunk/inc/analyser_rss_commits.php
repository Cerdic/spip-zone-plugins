<?php

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

function inc_analyser_rss_commits_dist($url)
{
    include_spip('iterateur/data');
    include_spip('inc/distant');
    $valeurs = array();
    $page = recuperer_page($url);
    $page = preg_replace("/\<\?(.*)\?\>/", "", $page);
    $convertir = charger_fonction('xml_to_array', 'inc');
    $xml = $convertir($page);

    // echo "<pre>";
    // var_dump($xml);
    // echo "</pre>";

    return $xml;
}
?>