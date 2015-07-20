<?php

/**
 * Plugin Nettoyer la médiathèque.
 *
 * @plugin     Nettoyer la médiathèque
 *
 * @copyright  2014-2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function action_supprimer_orphelins_dist()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();
}
