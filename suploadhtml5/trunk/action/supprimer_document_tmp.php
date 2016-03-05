<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Cette action permet de supprimer un document temporaire par une personne
 * qui n'en a pas forcément les droits.
 *
 * @param mixed $arg
 * @access public
 */
function action_supprimer_document_tmp_dist($arg = null) {

    if (is_null($arg)) {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
    }

    // Charger la fonction de suppression du core
    $supprimer_document = charger_fonction('supprimer_document', 'action');

    include_spip('inc/autoriser');
    // Si le statut est bien TMP, on continue
    if (autoriser('tmp', 'supprimer', $arg)) {

        // Autoriser temporairement la suppression du document
        autoriser_exception('supprimer', 'document', $arg, true);
        // Supprimer le document
        $supprimer_document($arg);
        // Refermer l'exception d'autorisation
        autoriser_exception('supprimer', 'document', $arg, false);

        // On nettoye le tableau de session suite à la suppression
        include_spip('inc/saisie_upload');
        saisie_supprimer_document_session($arg);
    }
}
