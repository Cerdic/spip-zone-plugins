<?php
/**
 * Plugin Nettoyer la médiathèque
 *
 * @plugin     Nettoyer la médiathèque
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Medias_nettoyage\Action\Supprimer_Orphelins
 */


if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

function action_supprimer_orphelins_dist ()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();

    if (isset($arg)) {
        supprimer_fichier($arg);
        spip_log(
            _T(
                'medias_nettoyage:message_log_supprimer_orphelins',
                array(
                    'date' => date_format(date_create(), 'Y-m-d H:i:s'),
                    'fichier' => $arg,
                    'id_auteur' => session_get('id_auteur'),
                    'auteur' => session_get('nom'),
                    'fonction' => __FUNCTION__
                )
            ),
            "medias_nettoyage"
        );
    }
}

?>