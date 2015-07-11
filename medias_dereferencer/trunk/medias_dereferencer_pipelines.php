<?php

/**
 * Utilisations de pipelines par Déréférencer les médias.
 *
 * @plugin     Déréférencer les médias
 *
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

/**
 * On se greffe au pipeline taches_generales_cron pour lancer nos tâches.
 *
 * @param  array $taches
 *
 * @return array
 */
function medias_dereferencer_taches_generales_cron($taches)
{
    $taches['medias_dereferencer'] = 24 * 3600; // toutes les 24h

    return $taches;
}
