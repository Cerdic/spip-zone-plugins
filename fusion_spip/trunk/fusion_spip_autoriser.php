<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser
 */
function fusion_spip_autoriser() { }

/**
 * Par défaut seuls les webmestres peuvent fusionner
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $opt
 *
 * @return bool
 */
function autoriser_fusionspip_menu($faire, $type, $id, $qui, $opt) {
    if ($qui['webmestre']=='oui') {
        return true;
    }

    return false;
}