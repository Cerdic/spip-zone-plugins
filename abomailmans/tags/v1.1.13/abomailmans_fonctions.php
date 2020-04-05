<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2012
 * $Id$
*/

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function nettoie_chemin($chemin)
{
    $liste = explode('/', $chemin);
    $dernier = count($liste) - 1;
    $chemin = str_replace('.html', '', $liste[$dernier]);
    $liste2 = explode('&', $chemin);
    $chemin = $liste2[0];

    return $chemin;
}

function noextension($chemin)
{
    return str_replace('.html', '', $chemin);
}

function recup_param($chemin)
{
    $a = explode('&', $chemin);
    $retour = '';
    $i = 1;
    while ($i < count($a)) {
        $retour .= '&'.htmlspecialchars(urldecode($a[$i]));
        ++$i;
    }

    return $retour;
}

function array_param($params)
{
    parse_str($params, $output);

    return $output;
}

function moins30($date)
{
    $moins30 = date('Y-m-d h:m:s', time() - 24 * 3600 * 30);

    return $moins30;
}
