<?php

/**
 * Récupérer le contenu d'un flux xml, mais aussi HTML, XHTML, image, etc.
 * à partir d'une URL. Retourne un tableau contenant toutes les infos de l'entête
 * HTTP du server et le contenu de la page.
 *
 * @param string $url URL de la page à récupérer
 * @return array
 */
function inc_recuperer_rss_commits_dist ($url)
{
    include_spip('inc/config');
    $login    = lire_config('rss_commits/login');
    $password = lire_config('rss_commits/password');
    $header   = array();

    // On teste si CURL est présent ou pas.
    // Sinon on passe par recuperer_page()
    if (function_exists('curl_init')) {
        // code adapté du script issu de cette page :
        // http://stackoverflow.com/a/14953910
        $options  = array(
            CURLOPT_CUSTOMREQUEST  => "GET",    // set request type post or get
            CURLOPT_POST           => false,    // set to GET
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_USERPWD        => $login . ':' . $password,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false,    // stop after 10 redirects
        );

        $ch      = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err     = curl_errno($ch);
        $errmsg  = curl_error($ch);
        $header  = curl_getinfo($ch);
        curl_close($ch);

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;

    } else {
        $header['content'] = recuperer_page($url);
    }

    return $header;
}
?>