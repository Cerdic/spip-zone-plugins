<?php
/**
 * Utilisations de pipelines par Commits de projet
 *
 * @plugin     Commits de projet
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\RSSCommits\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}


function rss_commits_taches_generales_cron($taches)
{
    $taches['import_commits'] = 1*3600; // toutes les heures
    return $taches;
}

?>