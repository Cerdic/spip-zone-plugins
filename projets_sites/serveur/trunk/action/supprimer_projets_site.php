<?php

/**
 * Supprimer un site de projet
 *
 * @plugin     Sites pour projets
 * @copyright  2013-2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Action
 */

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

/**
 * Action pour supprimer un site
 *
 * @param null|int $id
 *     `id` : son identifiant. En absence de `id` utilise l'argument de l'action sécurisée.
**/
function action_supprimer_projets_site_dist($id = null)
{
    if (is_null($id)) {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $id = $securiser_action();
    }
    $id_site = intval($id);

    if ($id_site) {
        sql_delete('spip_projets_sites', 'id_site='.$id_site);
        sql_delete('spip_projets_sites_liens', 'id_site='.$id_site);
    } else {
        spip_log(__FUNCTION__ . " $id pas compris");
    }
}
