<?php

/**
 * Fichier d'une tâche de fond du plugin 'Déréférencer les médias'.
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
include_spip('medias_dereferencer_fonctions');

function genie_medias_dereferencer_dist($t)
{
    include_spip('inc/session');
    $message_log = array();
    $message_log[] = "\n-----";
    $message_log[] = date_format(date_create(), 'Y-m-d H:i:s');
    $message_log[] = 'Fonction : '.__FUNCTION__;
    if (session_get('id_auteur')) {
        $message_log[] = "L'action a été lancé par l'auteur #".session_get('id_auteur').', '.session_get('nom').' ('.session_get('statut').')';
    } else {
        $message_log[] = "L'action a été lancé par SPIP en tâche de fond.";
    }

    medias_maj_documents_lies();
    medias_maj_documents_non_lies();
    // on met l'heure de fin de la procédure dans le message de log
    $message_log[] = date_format(date_create(), 'Y-m-d H:i:s');
    $message_log[] = "-----\n";
    // Et maintenant on stocke les messages dans un fichier de log.
    spip_log(implode("\n", $message_log), 'medias_dereferencer');

    return true;
}
