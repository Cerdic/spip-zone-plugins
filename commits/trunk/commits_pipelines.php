<?php
/**
 * Utilisations de pipelines par Commits de projet
 *
 * @plugin     Commits de projet
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Commits\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}


function commits_taches_generales_cron($taches)
{
    $taches['import_commits'] = 1*3600; // toutes les heures
    return $taches;
}

?>